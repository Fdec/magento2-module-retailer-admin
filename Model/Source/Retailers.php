<?php
/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module to newer
 * versions in the future.
 *
 * @category  Smile
 * @package   Smile\RetailerAdmin
 * @author    Fanny DECLERCK <fadec@smile.fr>
 * @copyright 2019 Smile
 * @license   Open Software License ("OSL") v. 3.0
 */
namespace Smile\RetailerAdmin\Model\Source;

use Magento\Framework\Api\SearchCriteriaBuilderFactory;
use Magento\Eav\Model\Entity\Attribute\Source\AbstractSource;
use Smile\Retailer\Api\RetailerRepositoryInterface;

/**
 * Class Retailers
 *
 * @package Smile\RetailerAdmin\Model\Source
 */
class Retailers extends AbstractSource
{
    /** @var RetailerRepositoryInterface */
    protected $retailerRepository;

    /** @var SearchCriteriaBuilderFactory*/
    protected $searchCriteriaBuilder;

    /**
     * Retailers constructor.
     *
     * @param RetailerRepositoryInterface  $retailerRepository    Retailer Repository.
     * @param SearchCriteriaBuilderFactory $searchCriteriaBuilder Search criteria builder.
     */
    public function __construct(
        RetailerRepositoryInterface $retailerRepository,
        SearchCriteriaBuilderFactory $searchCriteriaBuilder
    ) {
        $this->retailerRepository    = $retailerRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    /**
     * Returns all retailers for option list.
     *
     * @return array
     */
    public function getAllOptions()
    {
        $options[] = [
            'value' => '',
            'label' => ' ',
        ];
        $searchResults = $this->retailerRepository->getList($this->searchCriteriaBuilder->create()->create());

        foreach ($searchResults->getItems() as $retailer) {
            $options[] = [
                'value' => $retailer->getId(),
                'label' => $retailer->getName(),
            ];
        }

        return $options;
    }
}
