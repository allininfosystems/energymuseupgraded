<?php
/**
 * MageWorx
 * Abandoned Cart Recovery
 *
 * @category   MageWorx
 * @package    MageWorx_AbCart
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_AbCart_Model_Cart extends Mage_Sales_Model_Quote
{
    const CART_STATUS_ACTIVE = 'active';
    const CART_STATUS_ABANDONED = 'abandoned';
    const CART_STATUS_ABANDONED_AGAIN = 'abandoned_again';
    const CART_STATUS_LINK_CLICKED = 'link_clicked';
    const CART_STATUS_COMPLETED = 'completed';
    const CART_STATUS_COMPLETED_MANUALLY = 'completed_manually';
    const CART_STATUS_RECOVERED = 'recovered';

    private $_abandonedAt = false;

    protected function getLogModel()
    {
        return Mage::getModel('mageworx_abcart/cart_log');
    }

    /**
     * @param bool $allStatuses
     * @return array
     */
    public function getStatusOptionArray($allStatuses = true)
    {
        $options = array(
            self::CART_STATUS_ABANDONED         => Mage::helper('mageworx_abcart')->__('Abandoned'),
            self::CART_STATUS_ABANDONED_AGAIN   => Mage::helper('mageworx_abcart')->__('Abandoned Again'),
            self::CART_STATUS_LINK_CLICKED      => Mage::helper('mageworx_abcart')->__('Link Clicked'),
            self::CART_STATUS_COMPLETED_MANUALLY => Mage::helper('mageworx_abcart')->__('Completed Manually'),
            self::CART_STATUS_RECOVERED         => Mage::helper('mageworx_abcart')->__('Recovered'),
        );

        $additional = array(
            self::CART_STATUS_ACTIVE            => Mage::helper('mageworx_abcart')->__('Active'),
            self::CART_STATUS_COMPLETED         => Mage::helper('mageworx_abcart')->__('Completed'),
        );

        if ($allStatuses) {
            $options = array_merge($options, $additional);
        }

        return $options;
    }

    /**
     * @param $quoteId
     * @param $status
     * @return $this
     */
    public function logStatusChange($quoteId, $status)
    {
        if ($quoteId && $status) {
            $this->getLogModel()
                ->setQuoteId($quoteId)
                ->setStatus($status)
                ->save();
        }

        return $this;
    }

    /**
     * @param int $statusFlag
     * @return array
     */
    public function getAbandonedStatuses($statusFlag = 0)
    {
        $abandonedStatusArr = array(
            self::CART_STATUS_ABANDONED,
            self::CART_STATUS_ABANDONED_AGAIN
        );

        $recoveredStatusArr = array(
            self::CART_STATUS_LINK_CLICKED,
            self::CART_STATUS_RECOVERED
        );

        switch ($statusFlag) {
            case 1: // Only recovered statuses
                $result = $recoveredStatusArr;
                break;
            case 2: // Both recovered and currently abandoned statuses
                $result = array_merge($abandonedStatusArr, $recoveredStatusArr);
                break;
            default: // Only currently abandoned statuses
                $result = $abandonedStatusArr;
        }

        return $result;
    }

    /**
     * @return object
     */
    public function getAbandonedCarts()
    {
        $carts = $this->getCollection();
        $carts->getSelect()
            ->where('abcart_status IN(?)', $this->getAbandonedStatuses());

        return $carts;
    }

    /**
     * @param $quoteId
     * @return bool
     */
    public function getAbandonedAt($quoteId)
    {
        $log = Mage::getModel('mageworx_abcart/cart_log')->getCollection();
        $log->getSelect()
            ->where('quote_id = "'.$quoteId.'" AND status IN(?)', $this->getAbandonedStatuses())
            ->order('changed_at DESC');

        if ($abandonedAt = $log->getFirstItem()->getChangedAt()) {
            return $abandonedAt;
        }

        return false;
    }

    /**
     * @param $statusFlag
     * @param bool $customStatus
     * @return object
     */
    public function getFilteredCollection($statusFlag, $customStatus = false)
    {
        if ($customStatus) {
            $statuses = $customStatus;
        } else {
            $statuses = $this->getAbandonedStatuses($statusFlag);
        }

        $collection = $this->getCollection();
        $collection->getSelect()->where('abcart_status IN(?)', $statuses);

        return $collection;
    }
}