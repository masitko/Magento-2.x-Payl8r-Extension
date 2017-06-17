<?php

namespace Magento\Payl8rPaymentGateway\Controller;

use Magento\Framework\App\ObjectManager;
use Magento\Payment\Gateway\ConfigInterface;
use Magento\Framework\App\Action\Context;
use Magento\Payl8rPaymentGateway\Helper\Data;
use Magento\Framework\App\Request\Http;
use Magento\Payment\Model\Method\Logger;

/**
 * DirectPost Payment Controller
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
abstract class Payment extends \Magento\Framework\App\Action\Action {

  protected $dataHelper;
  protected $request;
  protected $logger;
  protected $config;

  /**
   * 
   * @param \Magento\Framework\App\Action\Context $context
   * @param \Magento\Payl8rPaymentGateway\Helper\Data $dataHelper
   * @param \Magento\Framework\App\Request\Http $request
   * @param ConfigInterface $config
   */
  public function __construct(
  Context $context, Data $dataHelper, Http $request, ConfigInterface $config, Logger $logger
  ) {
    $this->logger = $logger ?: ObjectManager::getInstance()->get(LoggerInterface::class);
    $this->dataHelper = $dataHelper;
    $this->request = $request;
    $this->config = $config;
    parent::__construct($context);
  }

}
