<?php
/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module to newer
 * versions in the future.
 *
 * @category  Smile
 * @package   Smile\RetailerAdmin
 * @author    Florent Maissiat <florent.maissiat@smile.eu>
 * @copyright 2019 Smile
 */
namespace Smile\RetailerAdmin\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Smile\RetailerAdmin\Helper\FilterHelper;

/**
 * Class DynamicFilterObserver
 *
 * @package Smile\RetailerAdmin\Observer
 */
class DynamicFilterObserver implements ObserverInterface
{
    /** @var FilterHelper */
    protected $filterHelper;

    /**
     * DynamicFilterObserver constructor.
     *
     * @param FilterHelper $filterHelper Filter helper.
     */
    public function __construct(FilterHelper $filterHelper)
    {
        $this->filterHelper = $filterHelper;
    }

    /**
     * Observer of the event "core_collection_abstract_load_before".
     *
     * @param Observer $observer The event observer.
     *
     * @return void
     *
     * {@event core_collection_abstract_load_before; @area: adminhtml}
     * @throws \Zend_Db_Select_Exception
     */
    public function execute(Observer $observer)
    {
        /** @var \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection $collection */
        $collection = $observer->getData('collection');

        $tables = $collection->getSelect()->getPart('from');
        $resource = $collection->getConnection();
        $retailerTableName = $this->filterHelper->getSellerTable();

        foreach ($tables as $alias => $tableData) {
            $name = $tableData['tableName'];
            $foreignKeys = $resource->getForeignKeys($name);

            foreach ($foreignKeys as $foreignKey) {
                if ($foreignKey['REF_TABLE_NAME'] === $retailerTableName) {
                    $this->filterHelper->applyFilterOnCollection(
                        $collection,
                        $alias,
                        $foreignKey['COLUMN_NAME']
                    );
                }
            }

            if ($name === $retailerTableName) {
                $this->filterHelper->applyFilterOnCollection($collection, $alias);
            }
        }
    }
}
