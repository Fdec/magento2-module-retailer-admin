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
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Registry;
use Smile\RetailerAdmin\Api\AdminRetailersInterface;
use Smile\RetailerAdmin\Helper\FilterHelper;

/**
 * Class DynamicModelFilterObserver
 *
 * @package Smile\RetailerAdmin\Observer
 */
class DynamicModelFilterObserver implements ObserverInterface
{
    /** @var FilterHelper */
    protected $filterHelper;

    /** @var Registry */
    protected $registry;

    /**
     * DynamicFilterObserver constructor.
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
     * Observer of the event "model_load_after".
     *
     * @param Observer $observer The event observer.
     *
     * @return void
     *
     * {@event model_load_after; @area: adminhtml}
     * @throws \Magento\Framework\Exception\LocalizedException
     *
     * @SuppressWarnings(PHPMD.ElseExpression)
     */
    public function execute(Observer $observer)
    {
        if ($this->registry->registry(AdminRetailersInterface::PREVENT_FILTER_FLAG)) {
            return;
        }

        /** @var \Magento\Framework\Model\AbstractModel $object */
        $object = $observer->getData('object');

        try {
            $resourceModel = $object->getResource();
        } catch (\Exception $e) {
            return;
        }

        if ($resourceModel instanceof \Magento\Eav\Model\Entity\AbstractEntity) {
            $table = $resourceModel->getEntityTable();
        } else {
            $table = $resourceModel->getMainTable();
        }

        $resource = $object->getResource()->getConnection();
        $retailerTableName = $this->filterHelper->getSellerTable();

        $adminRetailerIds = $this->filterHelper->getFilterSellerIds(false);

        if (count($adminRetailerIds) === 0) {
            return;
        }

        $foreignKeys = $resource->getForeignKeys($table);

        foreach ($foreignKeys as $foreignKey) {
            if ($foreignKey['REF_TABLE_NAME'] === $retailerTableName) {
                $retailerId = $object->getData($foreignKey['COLUMN_NAME']);
                if (!\in_array($retailerId, $adminRetailerIds, false)) {
                    $object->setData([]);
                    throw new NoSuchEntityException();
                }
            }
        }
    }
}
