<?php
/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module to newer
 * versions in the future.
 *
 * Schema Upgrade.
 *
 * @category  Smile
 * @package   Smile\RetailerAdmin
 * @author    Florent Maissiat <florent.maissiat@smile.eu>
 * @copyright 2019 Smile
 * @license   OSL-3.0 https://opensource.org/licenses/OSL-3.0
 */
namespace Smile\RetailerAdmin\Setup;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UpgradeSchemaInterface;

/**
 * Class UpgradeSchema.
 *
 * - v1.0.3: Add column and foreign key for retailer in tables 'sales_order_grid', 'sales_invoice_grid',
 *   'sales_shipment_grid'.
 *
 * @package Smile\RetailerAdmin\Setup
 */
class UpgradeSchema implements UpgradeSchemaInterface
{

    /**
     * Upgrades DB schema for a module
     *
     * @param SchemaSetupInterface   $setup   Module Setup.
     * @param ModuleContextInterface $context Module Context.
     *
     * @return void
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        if (version_compare($context->getVersion(), '1.0.3', '<')) {
            $this->addForeignKeyOnSaleInvoiceShipment($setup);
        }
        if (version_compare($context->getVersion(), '1.0.5', '<')) {
            $this->addForeignKeyOnRmaCreditmemo($setup);
        }
    }

    /**
     * Add a column with a foreign key of retailer.
     *
     * @param SchemaSetupInterface $setup      The module setup.
     * @param string               $tableName  The table name.
     * @param string               $columnName The column name that will contains the retailer id.
     *
     * @return void
     */
    private function addRetailerForeignKey(
        SchemaSetupInterface $setup,
        string $tableName,
        string $columnName = 'seller_id'
    ) {
        $table = $setup->getTable($tableName);
        if (!$setup->getConnection()->isTableExists($table)
            || $setup->getConnection()->tableColumnExists($table, $columnName)
        ) {
            return;
        }

        $setup->getConnection()->addColumn(
            $table,
            $columnName,
            [
                'type'     => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                'unsigned' => true,
                'nullable' => true,
                'default'  => null,
                'comment'  => 'Seller ID',
            ]
        );

        $setup->getConnection()->addForeignKey(
            $setup->getFkName($tableName, $columnName, 'smile_seller_entity', 'entity_id'),
            $table,
            $columnName,
            $setup->getTable('smile_seller_entity'),
            'entity_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_SET_NULL
        );
    }

    /**
     * Add column and foreign key in tables 'sales_order_grid', 'sales_invoice_grid', 'sales_shipment_grid'
     *
     * @param SchemaSetupInterface $setup Module Setup.
     *
     * @return void
     */
    private function addForeignKeyOnSaleInvoiceShipment(SchemaSetupInterface $setup)
    {
        $setup->startSetup();

        $tables = ['sales_order_grid', 'sales_invoice_grid', 'sales_shipment_grid'];

        foreach ($tables as $tableName) {
            $this->addRetailerForeignKey($setup, $tableName);
        }

        $setup->endSetup();
    }

    /**
     * Add column and foreign key in tables 'sales_order_grid', 'sales_invoice_grid', 'sales_shipment_grid'
     *
     * @param SchemaSetupInterface $setup Module Setup.
     *
     * @return void
     */
    private function addForeignKeyOnRmaCreditmemo(SchemaSetupInterface $setup)
    {
        $setup->startSetup();

        $tables = ['magento_rma_grid', 'sales_creditmemo_grid'];

        foreach ($tables as $tableName) {
            $this->addRetailerForeignKey($setup, $tableName);
        }

        $setup->endSetup();
    }
}
