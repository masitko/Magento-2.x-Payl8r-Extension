<?php

namespace Magento\Payl8rPaymentGateway\Controller;

use Magento\Framework\App\ObjectManager;
use \Magento\Framework\App\Request\Http;

/**
 * DirectPost Payment Controller
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
abstract class Payment extends \Magento\Framework\App\Action\Action
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    protected $dataHelper;
    protected $request;
    

    /**
     * Constructor
     *
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Payl8rPaymentGateway\Helper\Data $dataHelper
     * @param \Magento\Framework\App\Request\Http $request
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Payl8rPaymentGateway\Helper\Data $dataHelper,
        \Magento\Framework\App\Request\Http $request
    ) {
        $this->_coreRegistry = $coreRegistry;
        $this->dataHelper = $dataHelper;
        $this->request = $request;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Checkout\Model\Session
     */
    protected function _getCheckout()
    {
        return $this->_objectManager->get(\Magento\Checkout\Model\Session::class);
    }

}
