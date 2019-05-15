<?php
/**
 * Helper class.
 *
 * @category  Smile
 * @package   Smile\RetailerAdmin
 * @author    Florent Maissiat <florent.maissiat@smile.eu>
 * @copyright 2019 Smile
 * @license   OSL-3.0 https://opensource.org/licenses/OSL-3.0
 */
namespace Smile\RetailerAdmin\Helper;

use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\EntityManager\MetadataPool;
use Smile\Retailer\Api\Data\RetailerInterface;
use Smile\RetailerAdmin\Api\AdminRetailersInterface;
use Smile\RetailerAdmin\Api\AdminRetailersInterface\Proxy;

/**
 * Class FilterHelper
 *
 * @package Smile\RetailerAdmin\Helper
 */
class FilterHelper
{
    /** @var MetadataPool */
    protected $metadataPool;

    /** @var AdminRetailersInterface */
    protected $adminRetailers;

    /**
     * FilterHelper constructor.
     *
     * @param MetadataPool            $metadataPool   Entities metadata.
     * @param AdminRetailersInterface $adminRetailers Retailers of the current admin.
     */
    public function __construct(MetadataPool $metadataPool, AdminRetailersInterface\Proxy $adminRetailers)
    {
        $this->metadataPool = $metadataPool;
        $this->adminRetailers = $adminRetailers;
    }

    /**
     * Get the table name of Retailer/Seller.
     * (Table prefix included)
     *
     * @return string
     *
     * @throws \Exception
     */
    public function getSellerTable(): string
    {
        $sellerMetadata = $this->metadataPool->getMetadata(RetailerInterface::class);

        return $sellerMetadata->getEntityTable();
    }

    /**
     * Get the primary key of the Retailer/Seller table.
     *
     * @return string
     *
     * @throws \Exception
     */
    public function getSellerPrimaryKey(): string
    {
        return $this->metadataPool->getMetadata(RetailerInterface::class)->getIdentifierField();
    }

    /**
     * Get the list of Retailer/Seller id to use in filtering.
     *
     * @return int[]
     */
    public function getFilterSellerIds(): array
    {
        return $this->adminRetailers->getAdminRetailerIds();
    }

    /**
     * Apply Retailers/Sellers filter on a collection.
     *
     * @param AbstractDb $collection The collection where to apply the filter.
     * @param string     $alias      The retailer/seller table alias
     *                               (set null, if the collection is the retailer/seller table)
     * @param string     $column     The column to filter (set null to use the retailer/seller one)
     *
     * @return void
     *
     * @throws \Exception
     */
    public function applyFilterOnCollection(AbstractDb $collection, string $alias = null, string $column = null)
    {
        $column = $column ?? $this->getSellerPrimaryKey();

        $key = $alias ? ($alias . '.' . $column) : $column;

        $collection->addFieldToFilter($key, ['in' => $this->getFilterSellerIds()]);
    }
}
