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
 */
namespace Smile\RetailerAdmin\Plugin\Framework\View\Element\UiComponent;

use Magento\Framework\View\Element\UiComponent\Context;
use Magento\Framework\View\Element\UiComponentInterface;
use Smile\RetailerAdmin\Helper\FilterHelper;

/**
 * Class ContextPlugin
 *
 * @package Smile/RetailerAdmin/Plugin/Framework/View/Element/UiComponent
 */
class ContextPlugin
{
    /** @var FilterHelper */
    protected $filterHelper;

    /**
     * ContextPlugin constructor.
     *
     * @param FilterHelper $filterHelper Filter helper.
     */
    public function __construct(FilterHelper $filterHelper)
    {
        $this->filterHelper = $filterHelper;
    }

    /**
     * Check role before add button.
     *
     * @param Context              $subject
     * @param array                $buttons
     * @param UiComponentInterface $component
     *
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameters)
     */
    public function beforeAddButtons(Context $subject, array $buttons, UiComponentInterface $component)
    {
        if (isset($buttons['add'])
            && isset($buttons['add']['id'])
            && $buttons['add']['id'] == 'add-new-retailer'
        ) {
            $adminRetailerIds = $this->filterHelper->getFilterSellerIds(false);
            if (count($adminRetailerIds) !== 0) {
                unset($buttons['add']);
            }
        }

        return [$buttons, $component];
    }
}
