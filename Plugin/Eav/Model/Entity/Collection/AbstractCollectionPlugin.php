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
namespace Smile\RetailerAdmin\Plugin\Eav\Model\Entity\Collection;

use Magento\Eav\Model\Entity\Collection\AbstractCollection;
use Magento\Framework\Registry;
use Smile\RetailerAdmin\Api\AdminRetailersInterface;
use Smile\RetailerAdmin\Helper\FilterHelper;

/**
 * Class AbstractCollectionPlugin
 *
 * @package Smile\RetailerAdmin\Plugin\Eav\Model\Entity\Collection
 */
class AbstractCollectionPlugin
{
    /** @var FilterHelper */
    protected $filterHelper;

    /** @var Registry */
    protected $registry;

    /**
     * AbstractCollectionPlugin constructor.
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
     * Add "on the fly" a filter on seller tables based on allowed retailer of the current admin user.
     *
     * @param AbstractCollection $subject    The plugin subject.
     * @param bool               $printQuery 1rst params of `load` function
     * @param bool               $logQuery   2nd params of `load` function
     *
     * @return array
     *
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     * @throws \Exception
     */
    public function beforeLoad(AbstractCollection $subject, $printQuery = false, $logQuery = false)
    {
        if ($this->registry->registry(AdminRetailersInterface::PREVENT_FILTER_FLAG)) {
            return [$printQuery, $logQuery];
        }

        $retailerTableName = $this->filterHelper->getSellerTable();

        if ($subject->getMainTable() === $retailerTableName) {
            $this->filterHelper->applyFilterOnCollection($subject);
        }

        return [$printQuery, $logQuery];
    }
}
