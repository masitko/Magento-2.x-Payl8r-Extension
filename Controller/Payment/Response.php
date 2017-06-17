<?php

namespace Magento\Payl8rPaymentGateway\Controller\Payment;

use Psr\Log\LoggerInterface;

class Response extends \Magento\Payl8rPaymentGateway\Controller\Payment {


  public function execute() {
    
    $publicKey = $this->config->getValue('merchant_gateway_key');
    $params = $this->request->getParams();

    $this->logger->debug(array('RESPONSE!!!!!'));
    $this->logger->debug($publicKey);
    $this->logger->debug($params);
    
    echo var_export($params);
    echo var_export($order);
    
    die('test index');
  }

}
