<?php
/**
 * MageWorx
 * Abandoned Cart Recovery
 *
 * @category   MageWorx
 * @package    MageWorx_AbCart
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_AbCart_Model_Statistics extends Mage_Core_Model_Abstract
{
    /**
     * @param $collection
     * @return mixed
     */
    protected function _addCustomerFilter($collection)
    {
        if (!Mage::app()->getStore()->isAdmin() || !($customer = Mage::registry('current_customer'))) {
            return $collection;
        }

        $collection->addFieldToFilter('customer_id', $customer->getId());
    }

    /**
     * @return Varien_Object
     */
    public function getStartedStat()
    {
        $cartCollection = Mage::getModel('mageworx_abcart/cart')->getCollection();
        $this->_addCustomerFilter($cartCollection);

        $data['count'] = $cartCollection->getSize();

        return new Varien_Object($data);
    }

    /**
     * @param bool $recoveredOnly
     * @return Varien_Object
     */
    public function getAbandonedStat($recoveredOnly = false)
    {
        $data = array();
        $statusFlag = 2;
        $customStatus = $recoveredOnly ? MageWorx_AbCart_Model_Cart::CART_STATUS_RECOVERED : false;

        $cartCollection = Mage::getModel('mageworx_abcart/cart')->getFilteredCollection($statusFlag, $customStatus);
        $this->_addCustomerFilter($cartCollection);

        $data['count'] = $cartCollection->getSize();

        $cartCollection->clear();
        $cartCollection->getSelect()
            ->columns(array('revenue' => 'SUM(base_subtotal)'));

        $data['revenue'] = $cartCollection->getFirstItem()->getRevenue();

        return new Varien_Object($data);
    }
}