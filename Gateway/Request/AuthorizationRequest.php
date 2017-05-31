<?php

namespace Magento\Payl8rPaymentGateway\Gateway\Request;

use Magento\Framework\App\ObjectManager;
use Magento\Payment\Gateway\ConfigInterface;
use Magento\Payment\Gateway\Data\PaymentDataObjectInterface;
use Magento\Payment\Gateway\Request\BuilderInterface;
use Magento\Payment\Model\Method\Logger;
//use Psr\Log\LoggerInterface;

class AuthorizationRequest implements BuilderInterface
{
    /**
     * @var ConfigInterface
     */
    private $config;
    private $logger;
    

    /**
     * @param ConfigInterface $config
     */
    public function __construct(
        ConfigInterface $config,
        Logger $logger = null
    ) {
        $this->config = $config;
        $this->logger = $logger ?: ObjectManager::getInstance()->get(LoggerInterface::class);

    }

    /**
     * Builds ENV request
     *
     * @param array $buildSubject
     * @return array
     */
    public function build(array $buildSubject)
    {
        if (!isset($buildSubject['payment'])
            || !$buildSubject['payment'] instanceof PaymentDataObjectInterface
        ) {
            throw new \InvalidArgumentException('Payment data object should be provided');
        }

        /** @var PaymentDataObjectInterface $payment */
        $payment = $buildSubject['payment'];
        $order = $payment->getOrder();
        $address = $order->getShippingAddress();

//        $this->logger->critical('CRITICAL TEST!');
        $this->logger->debug(array('DEBUG DEBUG TEST!', 'some more'));
//        $this->logger->addDebug('ADD DEBUG TEST!');
//        $this->logger->info('TEST!!!!!');
//        $this->logger->info($order);
//        var_dump($order);
        
        return [
            'TXN_TYPE' => 'A',
            'INVOICE' => $order->getOrderIncrementId(),
            'AMOUNT' => $order->getGrandTotalAmount(),
            'CURRENCY' => $order->getCurrencyCode(),
            'EMAIL' => $address->getEmail(),
            'MERCHANT_KEY' => $this->config->getValue(
                'merchant_gateway_key',
                $order->getStoreId()
            )
        ];
    }
}
