<?php

namespace Magento\Payl8rPaymentGateway\Gateway\Validator;

use Magento\Framework\App\ObjectManager;
use Magento\Payment\Gateway\Validator\AbstractValidator;
use Magento\Payment\Gateway\Validator\ResultInterfaceFactory;
use Magento\Payment\Gateway\ConfigInterface;
use Magento\Framework\HTTP\Adapter\Curl;
use Psr\Log\LoggerInterface;

class CountryValidator extends AbstractValidator {

  private $logger;
  private $config;
  private $curl;

  public function __construct(
  ResultInterfaceFactory $resultFactory, ConfigInterface $config, Curl $curl, LoggerInterface $logger = null
  ) {
    $this->config = $config;
    $this->curl = $curl;
    $this->logger = $logger ?: ObjectManager::getInstance()->get(LoggerInterface::class);
    $this->logger->info('Country Validator Constructor!!!');
    parent::__construct($resultFactory);
  }

  /**
   * @inheritdoc
   */
  public function validate(array $validationSubject) {
    $isValid = true;
    
//    $r = $this->curl->connect('payl8r.com/getrates');
//    $response = $this->curl->read();
//    $this->curl->close();

    $ch = curl_init("https://payl8r.com/getrates");

    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
      "Content-Type: text/plain", 
    ));

    $result = curl_exec($ch);    
    
    $this->logger->info('Country Validator!!!');
    $this->logger->info('Response: '.json_encode($result));
    if ($validationSubject['country'] !== 'GB') {
      $isValid = false;
    }

    return $this->createResult($isValid);
  }

}
