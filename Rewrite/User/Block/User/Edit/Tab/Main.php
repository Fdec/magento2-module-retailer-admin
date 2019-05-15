<?php
/**
 * @category  Smile
 * @package   Smile\RetailerAdmin
 * @author    Florent Maissiat <florent.maissiat@smile.eu>
 * @copyright 2019 Smile
 * @license   OSL-3.0
 */
namespace Smile\RetailerAdmin\Rewrite\User\Block\User\Edit\Tab;

use Dompro\CatalogPrice\Ui\Component\Listing\Source\Retailer;
use Magento\Framework\Locale\OptionInterface;

/**
 * Class Main
 *
 * @package Smile\RetailerAdmin\Rewrite\User\Block\User\Edit\Tab
 */
class Main extends \Magento\User\Block\User\Edit\Tab\Main
{
    /** @var \Dompro\CatalogPrice\Ui\Component\Listing\Source\Retailer */
    protected $source;

    /**
     * Main constructor.
     *
     * @param \Magento\Backend\Block\Template\Context  $context         Context
     * @param \Magento\Framework\Registry              $registry        Registry
     * @param \Magento\Framework\Data\FormFactory      $formFactory     Html Form factory
     * @param \Magento\Backend\Model\Auth\Session      $authSession     Admin session
     * @param \Magento\Framework\Locale\ListsInterface $localeLists     List of locales
     * @param Retailer                                 $retailerSource  Retailer source Attribute//FIXME: Scope conflict
     * @param array                                    $data            Additional Data
     * @param OptionInterface|null                     $deployedLocales List of installed locales
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Backend\Model\Auth\Session $authSession,
        \Magento\Framework\Locale\ListsInterface $localeLists,
        Retailer $retailerSource,
        array $data = [],
        OptionInterface $deployedLocales = null
    ) {
        parent::__construct($context, $registry, $formFactory, $authSession, $localeLists, $data, $deployedLocales);
        $this->source = $retailerSource;
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
            'values' => $this->source->toOptionArray(),
            'label' => 'Allowed retailers',
            'value' => $extra['retailers'] ?? []
        ]);

        return $this;
    }
}
