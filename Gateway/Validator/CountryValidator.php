<?php

namespace Magento\Payl8rPaymentGateway\Gateway\Validator;

use Magento\Framework\App\ObjectManager;
use Magento\Payment\Gateway\Validator\AbstractValidator;
use Magento\Payment\Gateway\Validator\ResultInterfaceFactory;
use Magento\Payment\Gateway\ConfigInterface;
use Psr\Log\LoggerInterface;

class CountryValidator extends AbstractValidator {

  private $logger;
  private $config;

  public function __construct(
  ResultInterfaceFactory $resultFactory, ConfigInterface $config, LoggerInterface $logger = null
  ) {
    $this->config = $config;
    $this->logger = $logger ?: ObjectManager::getInstance()->get(LoggerInterface::class);
    $this->logger->info('Country Validator Constructor!!!');
    parent::__construct($resultFactory);
  }

  /**
   * @inheritdoc
   */
  public function validate(array $validationSubject) {
    $isValid = true;

    $this->logger->info('Country Validator!!!');
    if ($validationSubject['country'] !== 'GB') {
      $isValid = false;
    }

    return $this->createResult($isValid);
  }

}
