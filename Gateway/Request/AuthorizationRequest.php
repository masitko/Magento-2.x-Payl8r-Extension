<?php

namespace Magento\Payl8rPaymentGateway\Gateway\Request;

use Magento\Framework\App\ObjectManager;
use Magento\Payment\Gateway\ConfigInterface;
use Magento\Payment\Gateway\Data\PaymentDataObjectInterface;
use Magento\Payment\Gateway\Request\BuilderInterface;
use Magento\Payment\Model\Method\Logger;
use Magento\Framework\UrlInterface;
use Magento\Framework\Encryption\EncryptorInterface;

//use Psr\Log\LoggerInterface;

class AuthorizationRequest implements BuilderInterface {

  /**
   * @var ConfigInterface
   */
  private $config;
  private $logger;
  private $urlBuilder;
  private $encryptor;


  /**
   * @param ConfigInterface $config
   */
  public function __construct(
  ConfigInterface $config, UrlInterface $urlBuilder, 
        EncryptorInterface $encryptor,
          Logger $logger = null
  ) {
    $this->config = $config;
    $this->urlBuilder = $urlBuilder;
    $this->encryptor = $encryptor;
    $this->logger = $logger ? : ObjectManager::getInstance()->get(LoggerInterface::class);
  }

  /**
   * Builds ENV request
   *
   * @param array $buildSubject
   * @return array
   */
  public function build(array $buildSubject) {
    if (!isset($buildSubject['payment']) || !$buildSubject['payment'] instanceof PaymentDataObjectInterface
    ) {
      throw new \InvalidArgumentException('Payment data object should be provided');
    }

    /** @var PaymentDataObjectInterface $payment */
    $payment = $buildSubject['payment'];
    $order = $payment->getOrder();
    
    $billing = $order->getBillingAddress();
    
    return [
      'TXN_TYPE' => 'A',
      'INVOICE' => $order->getOrderIncrementId(),
      'AMOUNT' => $order->getGrandTotalAmount(),
      'CURRENCY' => $order->getCurrencyCode(),
      'EMAIL' => $billing->getEmail(),
      'MERCHANT_KEY' => $this->config->getValue(
              'merchant_gateway_key', $order->getStoreId()
      )
    ];
  }

}
