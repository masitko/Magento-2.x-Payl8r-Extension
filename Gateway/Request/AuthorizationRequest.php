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
    
//    $order->setState(\Magento\Sales\Model\Order::STATE_PENDING_PAYMENT);
//    $order->setStatus('payl8r_pending');
    
    $billing = $order->getBillingAddress();
    $shipping = $order->getShippingAddress();

    $test = $this->config->getValue('test_mode', $order->getStoreId());
    $username = $this->encryptor->decrypt($this->config->getValue('merchant_username', $order->getStoreId()));
    $publicKey = $this->encryptor->decrypt($this->config->getValue('merchant_gateway_key', $order->getStoreId()));

    $abortUrl = $this->urlBuilder->getUrl('payl8rpaymentgateway/payment/abort');
    $failUrl = $this->urlBuilder->getUrl('payl8rpaymentgateway/payment/fail');
    $successUrl = $this->urlBuilder->getUrl('checkout/onepage/success');
    $returnUrl = $this->urlBuilder->getUrl('payl8rpaymentgateway/payment/response');

    $products = [];
    foreach ($order->getItems() as $product) {
      // don't get parent items
      if (!$product->getHasChildren()) {
        $products[] = $product->getName();
      }
    }

//    $this->logger->debug(array($test, $username, $publicKey, $this->urlBuilder->getUrl('checkout/onepage/success/')));
//
//    $this->logger->debug($products);
//        $this->logger->debug(array($this->con));
//        $this->logger->debug($order);

    $data = array(
      "username" => $username,
      "request_data" => array(
        "return_urls" => array(
          "abort" => str_replace('http:', 'https:', $abortUrl),
          "fail" => str_replace('http:', 'https:', $failUrl),
          "success" => str_replace('http:', 'https:', $successUrl),
          "return_data" => str_replace('http:', 'https:', $returnUrl),
        ),
        "request_type" => "standard_finance_request",
        "test_mode" => (int) $test,
        "order_details" => array(
          "order_id" => $order->getOrderIncrementId(),
          "description" => implode("<br>", $products),
          "currency" => "GBP",
          "total" => floatval($order->getGrandTotalAmount())
        ),
        "customer_details" => array(
          "student" => 0,
          "firstnames" => $billing ? $billing->getFirstname() : '',
          "surname" => $billing ? $billing->getLastname() : '',
          "email" => $billing ? $billing->getEmail() : '',
          "phone" => $billing ? $billing->getTelephone() : '',
          "address" => $billing ? $billing->getStreetLine1() : '',
          "city" => $billing ? $billing->getCity() : '',
          "country" => "UK",
          "postcode" => $billing ? str_replace(' ', '', $billing->getPostcode()) : '',
        )
      )
    );

    $this->logger->debug(array('DATA COMMING!!!!!'));
    $this->logger->debug($data);

//    die();
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
