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
namespace Smile\RetailerAdmin\Block\Widget\Grid\Column\Renderer;

use Magento\Backend\Block\Context;
use Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer;
use Magento\Framework\Api\SearchCriteriaBuilderFactory;
use Smile\Retailer\Api\RetailerRepositoryInterface;

/**
 * Class Retailers
 *
 * @package Smile\RetailerAdmin\Block\Widget\Grid\Column\Renderer
 */
class Retailers extends AbstractRenderer
{
    /** @var RetailerRepositoryInterface */
    protected $retailerRepository;

    /** @var SearchCriteriaBuilderFactory*/
    protected $criteriaBuilderFactory;

    /**
     * Retailers constructor.
     *
     * @param Context                      $context            Context.
     * @param RetailerRepositoryInterface  $retailerRepository Retailer repository.
     * @param SearchCriteriaBuilderFactory $criteriaBuilder    Criteria builder.
     * @param array                        $data               Data.
     */
    public function __construct(
        Context $context,
        RetailerRepositoryInterface $retailerRepository,
        SearchCriteriaBuilderFactory $criteriaBuilder,
        array $data = []
    ) {
        parent::__construct($context, $data);

        $this->retailerRepository     = $retailerRepository;
        $this->criteriaBuilderFactory = $criteriaBuilder;
    }

    /**
     * Returns formatted data.
     *
     * @param \Magento\Framework\DataObject $row Row.
     *
     * @return string
     */
    public function render(\Magento\Framework\DataObject $row)
    {
        $extra = $row->getExtra() ?? [];
        if (is_string($extra)) {
            $extra = json_decode($extra, true);
        }

        if (!$extra || !isset($extra['retailers'])) {
            return '';
        }

        $criteria = $this->criteriaBuilderFactory->create();
        $criteria->addFilter('entity_id', $extra['retailers'], 'IN');
        $retailers = $this->retailerRepository->getList($criteria->create())->getItems();

        $renderer = [];
        foreach ($retailers as $retailer) {
            $renderer[] = $retailer->getName();
        }

        return implode('|', $renderer);
    }
}
