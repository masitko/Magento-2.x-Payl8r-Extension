<?php

namespace Magento\Payl8rPaymentGateway\Model\Order\Payment\State;

use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\Data\OrderPaymentInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Payment\State\CommandInterface;

/**
 * Class AuthorizeCommand
 */
class AuthorizeCommand implements CommandInterface {

  /**
   * @param OrderPaymentInterface $payment
   * @param string|float $amount
   * @param OrderInterface $order
   * @return \Magento\Framework\Phrase
   */
  public function execute(OrderPaymentInterface $payment, $amount, OrderInterface $order) {
    if ($payment->getMethod() === 'payl8r_gateway') {
      $state = 'pending_payment';
      $status = 'payl8r_pending';
    } else {
      $state = Order::STATE_PROCESSING;
      $status = false;
    }
    $formattedAmount = $order->getBaseCurrency()->formatTxt($amount);

    if ($payment->getIsTransactionPending()) {
      $message = 'We will authorize %1 after the payment is approved at the payment gateway.';
    } else {
      $message = 'Authorized amount of %1.';
    }

    if ($payment->getIsFraudDetected()) {
      $state = Order::STATE_PAYMENT_REVIEW;
      $status = Order::STATUS_FRAUD;
      $message .= ' Order is suspended as its authorizing amount %1 is suspected to be fraudulent.';
    }
    $this->setOrderStateAndStatus($order, $status, $state);

    return __($message, $formattedAmount);
  }

  /**
   * @param Order $order
   * @param string $status
   * @param string $state
   * @return void
   */
  protected function setOrderStateAndStatus(Order $order, $status, $state) {
    if (!$status) {
      $status = $order->getConfig()->getStateDefaultStatus($state);
    }

    $order->setState($state)->setStatus($status);
  }

}
