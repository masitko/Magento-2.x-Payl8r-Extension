<?php

namespace Magento\Payl8rPaymentGateway\Controller\Payment;

use Psr\Log\LoggerInterface;

class Response extends \Magento\Payl8rPaymentGateway\Controller\Payment {


  public function execute() {
    
    $order = $this->_coreRegistry->registry('payl8r_order');
    
    echo var_export($this->_coreRegistry);
    echo var_export($order);
    
    die('test index');
  }

}
