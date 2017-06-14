<?php

namespace Magento\Payl8rPaymentGateway\Gateway\Response;

use Magento\Payment\Gateway\Data\PaymentDataObjectInterface;
use Magento\Payment\Gateway\Response\HandlerInterface;

class Payl8rHandler implements HandlerInterface
{
    /**
     * Handles transaction id
     *
     * @param array $handlingSubject
     * @param array $response
     * @return void
     */
    public function handle(array $handlingSubject, array $response)
    {
        if (!isset($handlingSubject['payment'])
            || !$handlingSubject['payment'] instanceof PaymentDataObjectInterface
        ) {
            throw new \InvalidArgumentException('Payment data object should be provided');
        }

        /** @var PaymentDataObjectInterface $paymentDO */
        $paymentDO = $handlingSubject['payment'];

        $payment = $paymentDO->getPayment();
        $order = $payment->getOrder();
        $message = $order->getCustomerNote();

        /** @var $payment \Magento\Sales\Model\Order\Payment */
        $payment->setIsTransactionPending(true);
        
//        $order->setState('pending_payment')
//            ->setStatus('payl8r_pending')
//            ->addStatusHistoryComment('waiting for authorisation...')
//            ->setIsCustomerNotified(false)
//          ;
        
        
    }
}
