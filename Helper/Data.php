<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Payl8rPaymentGateway\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\ObjectManager;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Sales\Model\OrderFactory;
use Psr\Log\LoggerInterface;

/**
 * Authorize.net Data Helper
 *
 * @api
 */
class Data extends AbstractHelper
{
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Sales\Model\OrderFactory
     */
    protected $orderFactory;

    /**
     * @var CartRepositoryInterface
     */
    protected $cartRepository;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Sales\Model\OrderFactory $orderFactory
     * @param CartRepositoryInterface $cartRepository
     */
    public function __construct(
        Context $context,
        StoreManagerInterface $storeManager,
        OrderFactory $orderFactory,
        CartRepositoryInterface $cartRepository
    ) {
        $this->logger = ObjectManager::getInstance()->get(LoggerInterface::class);

        $this->storeManager = $storeManager;
        $this->orderFactory = $orderFactory;
        $this->cartRepository = $cartRepository;
        parent::__construct($context);
    }

    public function test(
    ) {
      return 'DUPA';
    }

}
