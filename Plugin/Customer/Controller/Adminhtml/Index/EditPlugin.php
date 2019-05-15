<?php
/**
 * @category  Smile
 * @package   Smile\RetailerAdmin
 * @author    Florent Maissiat <florent.maissiat@smile.eu>
 * @copyright 2019 Smile
 */
namespace Smile\RetailerAdmin\Plugin\Customer\Controller\Adminhtml\Index;

use Dompro\RetailerCustomer\Api\Data\AttributesInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Controller\Adminhtml\Index\Edit;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Message\ManagerInterface;
use Smile\RetailerAdmin\Api\AdminRetailersInterface;

/**
 * Class EditPlugin
 *
 * @package Smile\RetailerAdmin\Plugin\Customer\Controller\Adminhtml\Index
 */
class EditPlugin
{
    /** @var AdminRetailersInterface */
    protected $adminRetailers;

    /** @var CustomerRepositoryInterface */
    protected $customerRepository;

    /** @var ManagerInterface */
    protected $messageManager;

    /** @var RedirectFactory */
    protected $resultRedirectFactory;

    /**
     * EditPlugin constructor.
     *
     * @param AdminRetailersInterface     $adminRetailers        Retailers of the current Admin.
     * @param CustomerRepositoryInterface $customerRepository    Customer Repository.
     * @param ManagerInterface            $messageManager        Session message manager.
     * @param RedirectFactory             $resultRedirectFactory Result factory.
     */
    public function __construct(
        AdminRetailersInterface $adminRetailers,
        CustomerRepositoryInterface $customerRepository,
        ManagerInterface $messageManager,
        RedirectFactory $resultRedirectFactory
    ) {
        $this->adminRetailers = $adminRetailers;
        $this->customerRepository = $customerRepository;
        $this->messageManager = $messageManager;
        $this->resultRedirectFactory = $resultRedirectFactory;
    }

    /**
     * Prevent edit of customer of another retailer that the current admin allowed retailers.
     *
     * @param Edit     $subject Plugin subject.
     * @param callable $proceed The 'original' function.
     *
     * @return Redirect
     */
    public function aroundExecute(Edit $subject, $proceed)
    {
        if (!interface_exists(AttributesInterface::class)) {
            return $proceed();
        }

        $customerId = (int)$subject->getRequest()->getParam('id');

        try {
            $customer = $this->customerRepository->getById($customerId);

            $customerRetailer = $customer->getCustomAttribute(AttributesInterface::CUSTOMER_SELLER_ID)->getValue();
            if (!\in_array($customerRetailer, $this->adminRetailers->getAdminRetailerIds(), false)) {
                $this->messageManager->addErrorMessage(__('Something went wrong while editing the customer.'));
                $resultRedirect = $this->resultRedirectFactory->create();
                $resultRedirect->setPath('customer/*/index');
                return $resultRedirect;
            }
        } catch (NoSuchEntityException $e) {
            return $proceed();
        } catch (LocalizedException $e) {
            return $proceed();
        }

        return $proceed();
    }
}
