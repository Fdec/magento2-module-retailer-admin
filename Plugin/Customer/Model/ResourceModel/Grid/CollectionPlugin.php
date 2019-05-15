<?php
/**
 * @category  Smile
 * @package   Smile\RetailerAdmin
 * @author    Florent Maissiat <florent.maissiat@smile.eu>
 * @copyright 2019 Smile
 */
namespace Smile\RetailerAdmin\Plugin\Customer\Model\ResourceModel\Grid;

use Dompro\RetailerCustomer\Api\Data\AttributesInterface;
use Magento\Customer\Model\ResourceModel\Grid\Collection;
use Smile\RetailerAdmin\Api\AdminRetailersInterface;

/**
 * Class CollectionPlugin
 *
 * @package Smile\RetailerAdmin\Plugin\Customer\Model\ResourceModel\Grid
 */
class CollectionPlugin
{
    /** @var AdminRetailersInterface */
    protected $adminRetailers;

    /**
     * DynamicFilterObserver constructor.
     *
     * @param AdminRetailersInterface $adminRetailers Retailers of the current Admin.
     */
    public function __construct(AdminRetailersInterface $adminRetailers)
    {
        $this->adminRetailers = $adminRetailers;
    }

    /**
     * Before loadWithFilter plugin.
     *
     * @param Collection $subject    The Plugin subject.
     * @param bool       $printQuery [optional] 1rst param of the function.
     * @param bool       $logQuery   [optional] 2nd param of the function.
     *
     * @return array
     *
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function beforeLoadWithFilter(
        Collection $subject,
        $printQuery = false,
        $logQuery = false
    ) {

        if (!interface_exists(AttributesInterface::class)) {
            return [$printQuery, $logQuery];
        }

        $subject->addFieldToFilter(
            AttributesInterface::CUSTOMER_SELLER_ID,
            ['in' => $this->adminRetailers->getAdminRetailerIds()]
        );

        return [$printQuery, $logQuery];
    }
}
