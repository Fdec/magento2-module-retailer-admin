<?php
/**
 * Upgrade Data.
 *
 * @category  Smile
 * @package   Smile\RetailerAdmin
 * @author    Tony DEPLANQUE <tony.deplanque@smile.fr>
 * @copyright 2019 Smile
 * @license   OSL-3.0
 */
namespace Smile\RetailerAdmin\Setup;

use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Smile\RetailerAdmin\Model\Setup\Role as RoleSetup;

/**
 * Class UpgradeData
 *
 * @package Dompro\CatalogPrice\Setup
 */
class UpgradeData implements UpgradeDataInterface
{
    /**
     * Role Setup.
     *
     * @var RoleSetup
     */
    protected $roleSetup;

    /**
     * UpgradeData constructor.
     *
     * @param RoleSetup $roleSetup Role Setup.
     */
    public function __construct(
        RoleSetup $roleSetup
    ) {
        $this->roleSetup = $roleSetup;
    }

    /**
     * Upgrades data for a module
     *
     * @param ModuleDataSetupInterface $setup   Data Setup.
     * @param ModuleContextInterface   $context Module Context.
     *
     * @return void
     *
     * @throws \Exception
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        if (version_compare($context->getVersion(), '1.0.6', '<')) {
            $this->roleSetup->import(['Smile_RetailerAdmin::fixtures/roles/seller.csv']);
        }

        $setup->endSetup();
    }
}
