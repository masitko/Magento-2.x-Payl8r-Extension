<?php

namespace Magento\Payl8rPaymentGateway\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Sales\Model\Order;
use Psr\Log\LoggerInterface;

class SaveOrderAfterSubmitObserver implements ObserverInterface
{
    /**
     *
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry;

    /**
     * @param \Magento\Framework\Registry $coreRegistry
     */
    public function __construct(
        \Magento\Framework\Registry $coreRegistry,
        LoggerInterface $logger = null
    ) {
        $this->coreRegistry = $coreRegistry;
        $this->logger = $logger ?: ObjectManager::getInstance()->get(LoggerInterface::class);
        $this->logger->info('Observer Save Order Constructor!!!');
    }

    /**
     * Save order into registry to use it in the overloaded controller.
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /* @var $order Order */
        $order = $observer->getEvent()->getData('order');
        $this->coreRegistry->register('payl8r_order', $order, true);

        $this->logger->info('Observer Order Saved !!! - '.$order->getId());
//        $this->logger->info(var_export($this->coreRegistry));
        
        return $this;
    }
}
