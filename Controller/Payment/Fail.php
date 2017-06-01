<?php

namespace Magento\Payl8rPaymentGateway\Controller\Payment;

class Fail extends \Magento\Payl8rPaymentGateway\Controller\Payment {

  public function execute() {
    die('fail index');
  }

}
