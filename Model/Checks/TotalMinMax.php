<?php

namespace Magento\Payl8rPaymentGateway\Model\Checks;

use Magento\Framework\App\ObjectManager;
use Magento\Payment\Model\MethodInterface;
use Magento\Payment\Model\Checks\SpecificationInterface;
use Magento\Quote\Model\Quote;
use Psr\Log\LoggerInterface;

/**
 * Checks is order total in allowed range or not
 *
 * @api
 */
class TotalMinMax implements SpecificationInterface {

  /**
   * Config value key for min order total
   */
  const MIN_ORDER_TOTAL = 'min_order_total';

  /**
   * Config value key for max order total
   */
  const MAX_ORDER_TOTAL = 'max_order_total';

  private $logger;

  public function __construct(
  LoggerInterface $logger = null
  ) {
    $this->logger = $logger ?: ObjectManager::getInstance()->get(LoggerInterface::class);
    $this->logger->info('Total Min Max Constructor!!!');
  }

  /**
   * Check whether payment method is applicable to quote
   *
   * @param MethodInterface $paymentMethod
   * @param \Magento\Quote\Model\Quote $quote
   * @return bool
   */
  public function isApplicable(MethodInterface $paymentMethod, Quote $quote) {

    $total = $quote->getBaseGrandTotal();

    if( $paymentMethod->getCode() === 'payl8r_gateway') {
      $ch = curl_init("https://payl8r.com/getrates");
      curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_HTTPHEADER, array(
          "Content-Type: text/plain",
      ));
      $result = curl_exec($ch);
      $rates = explode(",", $result);
      $minTotal = $rates[1];
      $maxTotal = $rates[4];
    }
    else {
      $minTotal = $paymentMethod->getConfigData(self::MIN_ORDER_TOTAL);
      $maxTotal = $paymentMethod->getConfigData(self::MAX_ORDER_TOTAL);
    }
    $this->logger->info('Checking min and max values: ' . $minTotal . ' - ' . $maxTotal);
    if (!empty($minTotal) && $total < $minTotal || !empty($maxTotal) && $total > $maxTotal) {
      return false;
    }
    return true;
  }

}
