<?php

namespace Magento\Payl8rPaymentGateway\Gateway\Validator;

use Magento\Framework\App\ObjectManager;
use Magento\Payment\Gateway\Validator\AbstractValidator;
use Magento\Payment\Gateway\Validator\ResultInterfaceFactory;
use Magento\Payment\Gateway\ConfigInterface;
//use Magento\Payment\Model\Method\Logger;
use Psr\Log\LoggerInterface;


class CountryValidator extends AbstractValidator
{
  
    private $logger;
    private $config;
    
    public function __construct(
        ResultInterfaceFactory $resultFactory,
        ConfigInterface $config,
        LoggerInterface $logger = null
    ) {
        $this->config = $config;
        $this->logger = $logger ?: ObjectManager::getInstance()->get(LoggerInterface::class);
        $this->logger->info('Country Validator Constructor!!!');
        parent::__construct($resultFactory);
    }
  
    /**
     * @inheritdoc
     */
    public function validate(array $validationSubject)
    {
        $isValid = true;
        $storeId = $validationSubject['storeId'];

        $this->logger->info('Country Validator!!!');
        
        if ((int)$this->config->getValue('allowspecific', $storeId) === 1) {
            $availableCountries = explode(
                ',',
                $this->config->getValue('specificcountry', $storeId)
            );

            if (!in_array($validationSubject['country'], $availableCountries)) {
                $isValid =  false;
            }
        }

        return $this->createResult($isValid);
    }
}