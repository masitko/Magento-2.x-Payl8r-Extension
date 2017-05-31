<?php

namespace Magento\Payl8rPaymentGateway\Gateway\Validator;

use Magento\Framework\App\ObjectManager;
use Magento\Payment\Gateway\Validator\AbstractValidator;
use Magento\Payment\Gateway\Validator\ResultInterfaceFactory;
use Magento\Payment\Gateway\ConfigInterface;
use Psr\Log\LoggerInterface;

class GlobalValidator extends AbstractValidator
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
        $this->logger->info('Global Validator Constructor!!!');
        parent::__construct($resultFactory);
    }
  
    /**
     * @inheritdoc
     */
    public function validate(array $validationSubject)
    {
        $this->logger->info('Global!!!');
        $isValid = true;
        $storeId = $validationSubject['storeId'];

//        if ((int)$this->config->getValue('allowspecific', $storeId) === 1) {
//            $availableCountries = explode(
//                ',',
//                $this->config->getValue('specificcountry', $storeId)
//            );
//
//            if (!in_array($validationSubject['country'], $availableCountries)) {
//                $isValid =  false;
//            }
//        }

        return $this->createResult($isValid);
    }
}