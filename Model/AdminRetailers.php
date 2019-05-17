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
namespace Smile\RetailerAdmin\Model;

use Magento\Backend\Model\Auth\Session;
use Magento\Backend\Model\Auth\Session\Proxy;
use Magento\Framework\Api\SearchCriteriaBuilderFactory;
use Magento\Framework\Registry;
use Smile\Retailer\Api\Data\RetailerInterface;
use Smile\Retailer\Api\RetailerRepositoryInterface;
use Smile\RetailerAdmin\Api\AdminRetailersInterface;

/**
 * Class AdminRetailers
 *
 * @package Smile\RetailerAdmin\Model
 */
class AdminRetailers implements AdminRetailersInterface
{
    /**
     * Instance cache for the current Admin retailer
     *
     * @var RetailerInterface
     */
    private $retailer;

    /**
     * Instance cache for the current Admin retailers
     *
     * @var RetailerInterface[]
     */
    private $retailers;

    /** @var Session */
    protected $authSession;

    /** @var RetailerRepositoryInterface */
    protected $retailerRepository;

    /** @var SearchCriteriaBuilderFactory */
    protected $criteriaBuilderFactory;

    /** @var Registry */
    protected $registry;

    /**
     * Instance cache for the list of all retailers
     *
     * @var RetailerInterface[]
     */
    protected $allRetailers;

    /**
     * AdminRetailers constructor.
     *
     * @param Session                      $authSession        Admin Session.
     * @param RetailerRepositoryInterface  $retailerRepository Retailer Repository.
     * @param Registry                     $registry           Request registry.
     * @param SearchCriteriaBuilderFactory $criteriaBuilder    Criteria Builder.
     */
    public function __construct(
        Session\Proxy $authSession,
        RetailerRepositoryInterface $retailerRepository,
        Registry $registry,
        SearchCriteriaBuilderFactory $criteriaBuilder
    ) {
        $this->authSession = $authSession;
        $this->retailerRepository = $retailerRepository;
        $this->criteriaBuilderFactory = $criteriaBuilder;
        $this->registry = $registry;
    }

    /**
     * Get the retailer of the current Admin
     *
     * @return RetailerInterface
     *
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getAdminRetailer(): RetailerInterface
    {
        if (!$this->retailer) {
            $customerExtra = $this->authSession->getUser()->getExtra();
            $retailerId = $customerExtra['retailers'] ?? [];

            $retailerId = reset($retailerId);

            if ($retailerId === false) {
                throw new \RuntimeException(__('Retailer ID is missing'));
            }

            $this->retailer = $this->retailerRepository->get($retailerId);
        }

        return $this->retailer;
    }

    /**
     * Set Retailers.
     *
     * @param array $retailers Retailers.
     *
     * @return void
     */
    public function setRetailers(array $retailers)
    {
        $this->retailers = $retailers;
    }

    /**
     * Get all Retailer Ids associated to the current Admin user
     *
     * @param bool $defaultToAll if true, no value mean all retailer
     *
     * @return array
     *
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function getAdminRetailerIds(bool $defaultToAll = true)
    {
        $retailerIds = $this->getAdminRetailers(false);

        if ($defaultToAll && count($retailerIds) === 0) {
            $retailerIds = $this->getAllRetailers();
        }
        return  array_map(function (RetailerInterface $retailer): int {
            return $retailer->getId();
        }, $retailerIds);
    }

    /**
     * Get the retailer of the current Admin
     *
     * @param bool $defaultToAll If no retailers is found on the admin user, all retailer is returned
     *
     * @return RetailerInterface[]
     *
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function getAdminRetailers(bool $defaultToAll = true)
    {
        if (!$this->retailers) {
            $retailerIds = $this->getAuthSessionData();

            if (!$defaultToAll && count($retailerIds) === 0) {
                return [];
            }

            if (count($retailerIds) === 0) {
                return $this->getAllRetailers();
            }

            $criteria = $this->criteriaBuilderFactory->create();
            $criteria->addFilter('entity_id', $retailerIds, 'IN');

            $this->registry->register(self::PREVENT_FILTER_FLAG, true, true);
            $this->retailers = $this->retailerRepository->getList($criteria->create())->getItems();
            $this->registry->unregister(self::PREVENT_FILTER_FLAG);
        }

        return $this->retailers;
    }

    /**
     * Get the list of retailer ids set on the admin user.
     *
     * @return array
     */
    protected function getAuthSessionData(): array
    {
        if (PHP_SAPI === 'cli') {
            return [];
        }
        $customerExtra = $this->authSession->getUser() ? $this->authSession->getUser()->getExtra() : [];
        if (\is_string($customerExtra)) {
            $customerExtra = json_decode($customerExtra, true);
        }
        return $customerExtra['retailers'] ?? [];
    }

    /**
     * Get all retailers.
     *
     * @return RetailerInterface[]
     */
    protected function getAllRetailers(): array
    {
        if ($this->allRetailers === null) {
            $builder = $this->criteriaBuilderFactory->create();

            $this->registry->register(self::PREVENT_FILTER_FLAG, true, true);
            $this->allRetailers = $this->retailerRepository->getList($builder->create())->getItems();
            $this->registry->unregister(self::PREVENT_FILTER_FLAG);
        }

        return $this->allRetailers;
    }
}
