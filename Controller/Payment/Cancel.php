<?php

namespace Magento\Payl8rPaymentGateway\Controller\Payment;

use Magento\Framework\App\Action\Context;
use Magento\Payl8rPaymentGateway\Helper\Checkout;

class Cancel extends \Magento\Framework\App\Action\Action
{
    /**
     * @var Checkout
     */
    private $checkoutHelper;

    /**
     * @param Context $context
     * @param Checkout $checkoutHelper
     */
    public function __construct(
        Context $context,
        Checkout $checkoutHelper
    ) {
        parent::__construct($context);
        $this->checkoutHelper = $checkoutHelper;
    }

    /**
     * Customer canceled payment on gateway side.
     *
     * @return void
     */
    public function execute()
    {
        $this->checkoutHelper->cancelCurrentOrder('');
        $this->checkoutHelper->restoreQuote();

        $this->_redirect('checkout', ['_fragment' => 'payment']);
    }
}
