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
 * @author    Fanny DECLERCK <fadec@smile.fr>
 * @copyright 2019 Smile
 * @license   OSL-3.0
 */
namespace Smile\RetailerAdmin\Rewrite\User\Block\User\Edit\Tab;

use Magento\Framework\Api\SearchCriteriaBuilderFactory;
use Magento\Framework\Locale\OptionInterface;
use Smile\Retailer\Api\Data\RetailerInterface;
use Smile\Retailer\Api\RetailerRepositoryInterface;

/**
 * Class Main
 *
 * @package Smile\RetailerAdmin\Rewrite\User\Block\User\Edit\Tab
 */
class Main extends \Magento\User\Block\User\Edit\Tab\Main
{
    /** @var RetailerRepositoryInterface */
    protected $retailerRepository;

    /** @var SearchCriteriaBuilderFactory*/
    protected $searchCriteriaBuilder;

    /**
     * Main constructor.
     *
     * @param \Magento\Backend\Block\Template\Context  $context               Context
     * @param \Magento\Framework\Registry              $registry              Registry
     * @param \Magento\Framework\Data\FormFactory      $formFactory           Html Form factory
     * @param \Magento\Backend\Model\Auth\Session      $authSession           Admin session
     * @param \Magento\Framework\Locale\ListsInterface $localeLists           List of locales
     * @param RetailerRepositoryInterface              $retailerRepository    The retailer repository
     * @param SearchCriteriaBuilderFactory             $searchCriteriaBuilder The criteria builder
     * @param array                                    $data                  Additional Data
     * @param OptionInterface|null                     $deployedLocales       List of installed locales
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Backend\Model\Auth\Session $authSession,
        \Magento\Framework\Locale\ListsInterface $localeLists,
        RetailerRepositoryInterface $retailerRepository,
        SearchCriteriaBuilderFactory $searchCriteriaBuilder,
        array $data = [],
        OptionInterface $deployedLocales = null
    ) {
        parent::__construct($context, $registry, $formFactory, $authSession, $localeLists, $data, $deployedLocales);

        $this->retailerRepository    = $retailerRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    /**
     * Prepare form fields
     *
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     * @return \Magento\Backend\Block\Widget\Form
     */
    protected function _prepareForm()
    {
        parent::_prepareForm();

        $form = $this->getForm();

        /** @var $model \Magento\User\Model\User */
        $model = $this->_coreRegistry->registry('permissions_user');

        $extra = $model->getExtra() ?? [];
        if (is_string($extra)) {
            $extra = json_decode($extra, true);
        }

        $fieldset = $form->addFieldset('retailer', ['legend' => __('Retailer Information')]);
        $fieldset->addField('allowed_retailer', 'multiselect', [
            'name' => 'extra[retailers]',
            'values' => $this->getRetailersListToOptionArray(),
            'label' => 'Allowed retailers',
            'value' => $extra['retailers'] ?? []
        ]);

        return $this;
    }

    /**
     * Returns retailers list of options.
     *
     * @return array
     */
    protected function getRetailersListToOptionArray()
    {
        $searchResults = $this->retailerRepository->getList($this->searchCriteriaBuilder->create()->create());

        return array_map(function (RetailerInterface $retailer) {
            return [
                'value' => $retailer->getId(),
                'label' => $retailer->getName()
            ];
        }, $searchResults->getItems());

    }
}
