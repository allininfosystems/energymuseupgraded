<?php
/**
 * MageWorx
 * Abandoned Cart Recovery
 *
 * @category   MageWorx
 * @package    MageWorx_AbCart
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_AbCart_Model_Resource_Cart_Report_Collection extends Mage_Reports_Model_Mysql4_Order_Collection
{
    /**
     * @param string $range
     * @param mixed $customStart
     * @param mixed $customEnd
     * @param int $isFilter
     * @return $this|Mage_Reports_Model_Resource_Order_Collection
     */
    public function prepareSummary($range, $customStart, $customEnd, $isFilter = 0)
    {
        $this->setMainTable('sales/quote');

        /**
         * Reset all columns, because result will group only by 'created_at' field
         */
        $this->getSelect()->reset(Zend_Db_Select::COLUMNS);

        $dateRange = $this->getDateRange($range, $customStart, $customEnd);

//        $tzRangeOffsetExpression = $this->_getTZRangeOffsetExpression(
//            $range, 'created_at', $dateRange['from'], $dateRange['to']
//        );

        $tzRangeOffsetExpression = $this->_getRangeExpressionForAttribute($range, 'created_at');

        $this->getSelect()
            ->columns(array(
                'quantity' => 'COUNT(main_table.entity_id)',
                'range' => $tzRangeOffsetExpression,
            ))
            ->where('main_table.abcart_status IN (?)', Mage::getSingleton('mageworx_abcart/cart')->getAbandonedStatuses(2))
            ->order('range', Zend_Db_Select::SQL_ASC)
            ->group($tzRangeOffsetExpression);

        $this->addFieldToFilter('created_at', $dateRange);

        return $this;
    }

    /**
     * @param $customerId
     * @return $this
     */
    public function addCustomerFilter($customerId)
    {
        $this->getSelect()->where('main_table.customer_id = ?', $customerId);
        return $this;
    }
}