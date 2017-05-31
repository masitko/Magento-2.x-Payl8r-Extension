<?php

namespace Magento\Payl8rPaymentGateway\Setup;

use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Quote\Setup\QuoteSetupFactory;
use Magento\Sales\Setup\SalesSetupFactory;

/**
 * @codeCoverageIgnore
 */
class InstallData implements InstallDataInterface {

  /**
   * @var SalesSetupFactory
   */
  protected $salesSetupFactory;

  /**
   * @var QuoteSetupFactory
   */
  protected $quoteSetupFactory;

  /**
   * @param SalesSetupFactory $salesSetupFactory
   * @param QuoteSetupFactory $quoteSetupFactory
   */
  public function __construct(SalesSetupFactory $salesSetupFactory, QuoteSetupFactory $quoteSetupFactory) {
    
  }

  /**
   * {@inheritdoc}
   */
  public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context) {
    /**
     * Prepare database for install
     */
    $setup->startSetup();

    $data = [];
    $statuses = [
        'payl8r_pending' => 'Payl8r (Pending)',
        'payl8r_abandoned' => 'Payl8r (Abandoned)',
        'payl8r_declined' => 'Payl8r (Declined)',
        'payl8r_accepted' => 'Payl8r (Accepted)'
    ];
    foreach ($statuses as $code => $info) {
      $data[] = ['status' => $code, 'label' => $info];
    }
    $setup->getConnection()
      ->insertArray($setup->getTable('sales_order_status'), ['status', 'label'], $data);

    $setup->getConnection()
      ->insertArray($setup->getTable('sales_order_status_state'), ['status', 'state', 'visible_on_front', 'is_default'], array(
          array(
              'status' => 'payl8r_pending',
              'state' => 'pending_payment',
              'visible_on_front' => 1,
              'is_defualt' => 1
          ),
          array(
              'status' => 'payl8r_abandoned',
              'state' => 'canceled',
              'visible_on_front' => 1,
              'is_defualt' => 0
          ),
          array(
              'status' => 'payl8r_declined',
              'state' => 'canceled',
              'visible_on_front' => 1,
              'is_defualt' => 0
          ),
          array(
              'status' => 'payl8r_accepted',
              'state' => 'processing',
              'visible_on_front' => 1,
              'is_defualt' => 0
          ),
        )
    );

    /**
     * Prepare database after install
     */
    $setup->endSetup();
  }

}
