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
 * @license   OSL-3.0
 */
namespace Smile\RetailerAdmin\Api;

use Smile\Retailer\Api\Data\RetailerInterface;

/**
 * Interface AdminRetailersInterface
 *
 * @package Smile\RetailerAdmin\Api
 */
interface AdminRetailersInterface
{
    const PREVENT_FILTER_FLAG = 'smile_admin_retailer_prevent_filter';

    /**
     * Get all Retailer Ids associated to the current Admin user
     *
     * @param bool $defaultToAll if true, no value mean all retailer
     *
     * @return array
     *
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function getAdminRetailerIds(bool $defaultToAll = true);

    /**
     * Get the retailer of the current Admin
     *
     * @param bool $defaultToAll If no retailers is found on the admin user, all retailer is returned
     *
     * @return RetailerInterface[]
     *
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function getAdminRetailers(bool $defaultToAll = true);

    /**
     * Set Retailers.
     *
     * @param array $retailers Retailers.
     *
     * @return void
     */
    public function setRetailers(array $retailers);
}
