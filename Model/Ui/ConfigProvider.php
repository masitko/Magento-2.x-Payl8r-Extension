<?php

namespace Magento\Payl8rPaymentGateway\Model\Ui;

use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Payl8rPaymentGateway\Gateway\Http\Client\ClientMock;

/**
 * Class ConfigProvider
 */
final class ConfigProvider implements ConfigProviderInterface
{
    const CODE = 'payl8r_gateway';

    /**
     * Retrieve assoc array of checkout configuration
     *
     * @return array
     */
    public function getConfig()
    {
        return [
            'payment' => [
                self::CODE => [
//                    'transactionResults' => [
//                        ClientMock::SUCCESS => __('Success'),
//                        ClientMock::FAILURE => __('Fraud')
//                    ],
                    'actionUrl' => 'https://payl8r.com/process'
                ]
            ]
        ];
    }
}
