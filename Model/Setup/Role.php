<?php
/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module to newer
 * versions in the future.
 *
 * Role Install.
 *
 * @category  Smile
 * @package   Smile\RetailerAdmin
 * @author    Tony DEPLANQUE <tony.deplanque@smile.fr>
 * @copyright 2019 Smile
 * @license   OSL-3.0
 */

namespace Smile\RetailerAdmin\Model\Setup;

use Magento\Authorization\Model\RoleFactory;
use Magento\Authorization\Model\RulesFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Setup\SampleData\Context as SampleDataContext;
use Magento\Authorization\Model\ResourceModel\Role\Collection as RoleCollection;
use Smile\RetailerAdmin\Model\Setup\Rule\Converter as RuleConverter;

/**
 * Class Role
 *
 * @package Smile\RetailerAdmin\Model\Setup
 */
class Role
{
    /**
     * RoleFactory
     *
     * @var roleFactory
     */
    protected $roleFactory;

    /**
     * RulesFactory
     *
     * @var rulesFactory
     */
    protected $rulesFactory;

    /**
     * Role Collection.
     *
     * @var RoleCollection
     */
    protected $roleCollection;

    /**
     * Rule Converter.
     *
     * @var RuleConverter
     */
    protected $ruleConverter;

    /**
     * Role constructor.
     *
     * @param SampleDataContext $sampleDataContext Sample data context.
     * @param RoleFactory       $roleFactory       Role Factory.
     * @param RulesFactory      $rulesFactory      Rule Factory.
     * @param RoleCollection    $roleCollection    Role Collection.
     * @param RuleConverter     $ruleConverter     Rule Converter.
     */
    public function __construct(
        SampleDataContext $sampleDataContext,
        RoleFactory $roleFactory,
        RulesFactory $rulesFactory,
        RoleCollection $roleCollection,
        RuleConverter $ruleConverter
    ) {
        $this->fixtureManager = $sampleDataContext->getFixtureManager();
        $this->csvReader = $sampleDataContext->getCsvReader();
        $this->roleFactory = $roleFactory;
        $this->rulesFactory = $rulesFactory;
        $this->roleCollection = $roleCollection;
        $this->ruleConverter = $ruleConverter;
    }

    /**
     * Import Roles fixtures.
     *
     * @param array $fixtures Fixtures.
     *
     * @return void
     *
     * @throws LocalizedException
     * @throws \Exception
     */
    public function import(array $fixtures)
    {
        foreach ($fixtures as $fileName) {
            $fileName = $this->fixtureManager->getFixture($fileName);
            if (!file_exists($fileName)) {
                continue;
            }

            $rows = $this->csvReader->getData($fileName);
            $header = array_shift($rows);
            foreach ($rows as $row) {
                $data = array_combine($header, $row);
                $this->saveRole($data);
            }
        }
    }

    /**
     * Save User Role.
     *
     * @param array $data Role Data.
     *
     * @return void
     *
     * @throws \Exception
     */
    protected function saveRole(array $data)
    {
        $roleName = $data['role_name'];
        if (empty($roleName)) {
            throw new LocalizedException(__('Role name is empty.'));
        }

        try {
            $role = $this->roleCollection->addFieldToFilter(
                'role_name',
                [
                    'eq' => $roleName
                ]
            )->getFirstItem();
        } catch (\Exception $e) {
            $role = null;
        }

        if (empty($role) || !$role->getId()) {
            /**
             * @var \Magento\Authorization\Model\Role $role
             */
            $role = $this->roleFactory->create();
            $role->setRoleName($roleName)
                ->setParentId($data['parent_id'])
                ->setRoleType($data['role_type'])
                ->setUserType($data['user_type']);
            $role->save();
        }

        $resources = $this->ruleConverter->getResources($data['rules']);

        /* Array of resource ids which we want to allow this role*/
        $this->rulesFactory->create()->setRoleId($role->getId())->setResources($resources)->saveRel();
    }
}
