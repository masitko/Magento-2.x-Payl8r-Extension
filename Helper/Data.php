<?php

namespace Magento\Payl8rPaymentGateway\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Encryption\EncryptorInterface;
use Magento\Payment\Gateway\ConfigInterface;
use Magento\Sales\Model\OrderRepository;
use Magento\Payment\Model\Method\Logger;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Email\Sender\OrderSender;


class Data extends AbstractHelper {

  private $orderRepository;
  private $config;
  private $logger;
  private $encryptor;
  private $orderSender;

  /**
   * 
   * @param Context $context
   * @param OrderRepository $orderRepository
   * @param ConfigInterface $config
   * @param EncryptorInterface $encryptor
   * @param Logger $logger
   */
  public function __construct(
  Context $context, OrderRepository $orderRepository, OrderSender $orderSender, ConfigInterface $config, EncryptorInterface $encryptor, Logger $logger
  ) {
    $this->orderRepository = $orderRepository;
    $this->orderSender = $orderSender;
    $this->config = $config;
    $this->encryptor = $encryptor;
    $this->logger = $logger ?: ObjectManager::getInstance()->get(LoggerInterface::class);

    parent::__construct($context);
  }

  public function prepareIframeData($orderId) {
    $order = $this->orderRepository->get($orderId);

    $billing = $order->getBillingAddress();
    $shipping = $order->getShippingAddress();
    if (empty($billing)) {
      $billing = $shipping;
    }

    $test = $this->config->getValue('test_mode', $order->getStoreId());
    $username = $this->config->getValue('merchant_username', $order->getStoreId());
    $publicKey = $this->config->getValue('merchant_gateway_key', $order->getStoreId());
    
    $abortUrl = $this->_urlBuilder->getUrl('payl8rpaymentgateway/payment/cancel');
    $failUrl = $this->_urlBuilder->getUrl('payl8rpaymentgateway/payment/cancel');
    $successUrl = $this->_urlBuilder->getUrl('checkout/onepage/success');
    $returnUrl = $this->_urlBuilder->getUrl('payl8rpaymentgateway/payment/response');

    $products = [];
    foreach ($order->getItems() as $product) {
      // don't get parent items
      if (!$product->getHasChildren()) {
        $products[] = $product->getName();
      }
    }

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
                "order_id" => $orderId,
                "description" => implode("<br>", $products),
                "currency" => "GBP",
                "total" => floatval($order->getGrandTotal())
            ),
            "customer_details" => array(
                "student" => 0,
                "firstnames" => $billing ? $billing->getFirstname() : '',
                "surname" => $billing ? $billing->getLastname() : '',
                "email" => $billing ? $billing->getEmail() : '',
                "phone" => $billing ? $billing->getTelephone() : '',
                "address" => $billing ? $billing->getStreetLine(1) : '',
                "city" => $billing ? $billing->getCity() : '',
                "country" => "UK",
                "postcode" => $billing ? str_replace(' ', '', $billing->getPostcode()) : '',
            )
        )
    );

    if (!empty($shipping) && !$shipping->getSameAsBilling()) {
      $data["request_data"]["customer_details"]["delivery_name"] = $shipping->getFirstname() . ' ' . $shipping->getLastname();
      $data["request_data"]["customer_details"]["delivery_address"] = $shipping->getStreetLine(1);
      $data["request_data"]["customer_details"]["delivery_city"] = $shipping->getCity();
      $data["request_data"]["customer_details"]["delivery_postcode"] = $shipping->getPostcode();
      $data["request_data"]["customer_details"]["delivery_country"] = "UK";
    }

    $json_data = json_encode($data);
    openssl_public_encrypt($json_data, $crypted, $publicKey);


    return array(
      'rid' => $username,
      'data' => base64_encode($crypted),
      'action' => 'https://payl8r.com/process'
    );
  }
  
  public function processResponse( $response ) {
    
    $this->logger->debug((array)$response);
    
    $order = $this->orderRepository->get($response->order_id);
    
    switch( $response->status  ) {
      case 'ACCEPTED':
        $order->setState(Order::STATE_PROCESSING);
        $order->setStatus('payl8r_accepted');
        $order->addStatusToHistory($order->getStatus(), 'Payment Successful', true);
        $order->save();
        try {
          $this->orderSender->send($order);
        } catch (Exception $e) {}
        break;
      case 'DECLINED':
        $order->setState(Order::STATE_CANCELED);
        $order->setStatus('payl8r_declined');
        $order->addStatusToHistory($order->getStatus(), $response->reason, false);
        $order->save();
        break;
      case 'ABANDONED':
      default:
        $order->setState(Order::STATE_CANCELED);
        $order->setStatus('payl8r_abandoned');
        $order->addStatusToHistory($order->getStatus(), $response->reason, false);
        $order->save();
        break;
    }
    
  }
  
  

}
