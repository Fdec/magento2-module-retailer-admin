<?php
/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module to newer
 * versions in the future.
 *
 * Add the seller id information on RMA grid.
 *
 * @category  Smile
 * @package   Smile\RetailerAdmin
 * @author    Florent Maissiat <florent.maissiat@smile.eu>
 * @copyright 2019 Smile
 * @license   OSL-3.0 https://opensource.org/licenses/OSL-3.0
 */
namespace Smile\RetailerAdmin\Observer\Magento;

use Magento\Framework\DataObject;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Sales\Api\OrderRepositoryInterface;

/**
 * Class AddRetailerOnRmaGrid
 *
 * @package Smile\RetailerAdmin\Observer\Magento
 */
class AddRetailerOnRmaGrid implements ObserverInterface
{
    /** @var OrderRepositoryInterface */
    protected $orderRepository;

    /**
     * AddRetailerOnRmaGrid constructor.
     *
     * @param OrderRepositoryInterface $orderRepository The order repository
     */
    public function __construct(OrderRepositoryInterface $orderRepository)
    {
        $this->orderRepository = $orderRepository;
    }

    /**
     * Add Seller Id on RMA grid item
     *
     * @param Observer $observer The Observer.
     *
     * @return void
     *
     * {@event core_abstract_save_before ; @area adminhtml }
     */
    public function execute(Observer $observer)
    {
        /** @var \Magento\Rma\Model\Grid $rmaGrid */
        $rmaGrid = $observer->getData('data_object');

        if ((!$rmaGrid instanceof \Magento\Rma\Model\Grid)) {
            return;
        }

        $orderId = $rmaGrid->getData('order_id');

        $order = $this->orderRepository->get($orderId);

        if ($order instanceof DataObject) {
            $rmaGrid->setData('seller_id', $order->getData('seller_id'));
        }
    }
}
