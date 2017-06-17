<?php

namespace Magento\Payl8rPaymentGateway\Controller\Payment;

use Psr\Log\LoggerInterface;

class Response extends \Magento\Payl8rPaymentGateway\Controller\Payment {


  public function execute() {
    
    $publicKey = $this->config->getValue('merchant_gateway_key');
    $params = $this->request->getParams();
    
    if( !isset($params['response'])) {
      die();
    }
    
    $response = $params['response'];
    
    $this->logger->debug(array('RESPONSE !!!!!!!!!'));
    
    if ($encrypted_response = base64_decode($response)) {
      if (openssl_public_decrypt($encrypted_response, $json_response, $publicKey)) {
        if ($decoded_response = json_decode($json_response)) {
          if (isset($decoded_response->return_data)) {
            if ($decoded_response->return_data->order_id != '') {
              $this->dataHelper->processResponse($decoded_response->return_data);
              echo 'OK';
            }
          }
        }
      }
    }

    die();
  }

}
