<?php
/**
 * @category  Smile
 * @package   Smile\RetailerAdmin
 * @author    Florent Maissiat <florent.maissiat@smile.eu>
 * @copyright 2019 Smile
 */
namespace Smile\RetailerAdmin\Plugin\Framework\Data\Collection;

use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Registry;
use Smile\RetailerAdmin\Api\AdminRetailersInterface;
use Smile\RetailerAdmin\Helper\FilterHelper;

/**
 * Class AbstractDbPlugin
 *
 * @package Smile\RetailerAdmin\Plugin\Framework\Data\Collection
 */
class AbstractDbPlugin
{
    /** @var FilterHelper */
    protected $filterHelper;

    /** @var Registry */
    protected $registry;

    /**
     * AbstractDbPlugin constructor.
     *
     * @param FilterHelper $filterHelper Filter helper.
     * @param Registry     $registry     Registry.
     */
    public function __construct(FilterHelper $filterHelper, Registry $registry)
    {
        $this->filterHelper = $filterHelper;
        $this->registry = $registry;
    }

    /**
     * Add "on the fly" a filter on any tables that have a seller based on allowed retailer of the current admin user.
     *
     * @param AbstractDb $subject The plugin subject.
     *
     * @return array
     *
     * @throws \Zend_Db_Select_Exception
     */
    public function beforeGetSelectCountSql(AbstractDb $subject)
    {
        if ($this->registry->registry(AdminRetailersInterface::PREVENT_FILTER_FLAG)) {
            return [];
        }

        $tables = $subject->getSelect()->getPart('from');
        $resource = $subject->getConnection();
        $retailerTableName = $this->filterHelper->getSellerTable();

        $adminRetailerIds = $this->filterHelper->getFilterSellerIds();

        foreach ($tables as $alias => $tableData) {
            $name = $tableData['tableName'];
            $foreignKeys = $resource->getForeignKeys($name);

            foreach ($foreignKeys as $foreignKey) {
                if ($foreignKey['REF_TABLE_NAME'] === $retailerTableName) {
                    $subject->getSelect()->where($alias . '.'. $foreignKey['COLUMN_NAME'].' in (?)', $adminRetailerIds);
                }
            }
        }

        return [];
    }
}
