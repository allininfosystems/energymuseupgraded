<?php
/**
 * MageWorx
 * Abandoned Cart Recovery
 *
 * @category   MageWorx
 * @package    MageWorx_AbCart
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_AbCart_Model_Resource_Report_Cart_Collection extends Mage_Sales_Model_Mysql4_Quote_Collection
{
    protected $_resourceModel = 'sales/quote';
    protected $_storeIds = false;
    protected $_isTotals = false;

    /**
     * @param null $flag
     * @return $this|bool
     */
    public function isTotals($flag = null)
    {
        if (is_null($flag)) {
            return $this->_isTotals;
        }
        $this->_isTotals = $flag;
        return $this;
    }

    /**
     * @param $status
     * @param $isCount
     * @return string
     */
    protected function _getColumnSumExpr($status, $isCount)
    {
        $adapter = $this->getConnection();
        $text = 'SUM(';

        $trueResult = $isCount ? 1 : 'main_table.base_subtotal';
        $cond = $adapter->quoteInto('main_table.abcart_status IN(?)', $status);

        $text .= new Zend_Db_Expr("IF((".$cond."),".$trueResult.",0)");

        $text .= ')';

        return $text;
    }

    /**
     * @return array
     */
    protected function _getSelectedColumns()
    {
        $abandonedStatus = Mage::getSingleton('mageworx_abcart/cart')->getAbandonedStatuses(2);
        $recoveredStatus = Mage::getSingleton('mageworx_abcart/cart')->getAbandonedStatuses(1);

        if (!$this->isTotals()) {
            $this->_selectedColumns = array(
                'started_carts'   => 'COUNT(entity_id)',
                'abandoned_carts' => $this->_getColumnSumExpr($abandonedStatus, true),
                'abandoned_revenue' => $this->_getColumnSumExpr($abandonedStatus, false),
                'recovered_carts' => $this->_getColumnSumExpr($recoveredStatus, true),
                'recovered_revenue' => $this->_getColumnSumExpr($recoveredStatus, false),
            );
        }

        if ($this->isTotals()) {
            $this->_selectedColumns = $this->getAggregatedColumns();
        }

        return $this->_selectedColumns;
    }

    /**
     * @return $this
     */
    public function addCustomerGroupData()
    {
        $this->getSelect()
            ->joinLeft(array('group' => $this->getTable('customer/customer_group')),
                'main_table.customer_group_id = group.customer_group_id',
                array('customer_group'    => 'group.customer_group_code'));

        if (!$this->isTotals()) {
            $this->getSelect()->group('main_table.customer_group_id');
        }

        return $this;
    }

    /**
     * @return $this
     */
    public function addStoreviewData()
    {
        $this->getSelect()
            ->joinLeft(array('store' => $this->getTable('core/store')),
            'main_table.store_id = store.store_id',
            array('store_name'    => 'store.name'));

        if (!$this->isTotals()) {
            $this->getSelect()->group('main_table.store_id');
        }

        return $this;
    }

    /**
     * @return $this
     */
    public function addDeviceTypeData()
    {
        $this->getSelect()->columns(array('device_type' => 'IFNULL(main_table.device_type, "not detected type")'));

        if (!$this->isTotals()) {
            $this->getSelect()->group('main_table.device_type');
        }

        return $this;
    }

    /**
     * @return $this
     */
    public function addBillingAddressData()
    {
        $this->getSelect()
            ->joinLeft(array('billing_address' => $this->getTable('sales/quote_address')),
                'billing_address.quote_id = main_table.entity_id AND billing_address.address_type = "billing"',
                array())
            ->joinLeft(array('shipping_address' => $this->getTable('sales/quote_address')),
                'shipping_address.quote_id = main_table.entity_id AND shipping_address.address_type = "shipping"',
                array('country_code' => 'IFNULL(billing_address.country_id, shipping_address.country_id)'))
            ->order('country_code DESC');

        if (!$this->isTotals()) {
            $this->getSelect()->group('IFNULL(billing_address.country_id, shipping_address.country_id)');
        }

        return $this;
    }

    /**
     * @return $this|Mage_Core_Model_Resource_Db_Collection_Abstract
     */
    protected function _initSelect()
    {
        $this->getSelect()
            ->from(array('main_table' => $this->getResource()->getMainTable()), array())
            ->columns($this->_getSelectedColumns());

        return $this;
    }

    /**
     * @return $this
     */
    public function addOrderStatusFilter()
    {
        return $this;
    }

    public function setPeriod()
    {
        return $this;
    }

    /**
     * @param $from
     * @param $to
     * @return $this
     */
    public function setDateRange($from, $to)
    {
        $this->getSelect()->where('main_table.created_at BETWEEN "'.$from.' 00:00:00" AND "'.$to.' 23:59:59"');
        return $this;
    }

    /**
     * @param $storeIds
     * @return $this
     */
    public function addStoreFilter($storeIds)
    {
        $storeIds = implode(',', $storeIds);
        if (!$storeIds) {
            return $this;
        }

        $this->getSelect()->where('main_table.store_id IN('.$storeIds.')');
        return $this;
    }

    /**
     * @param array $columns
     * @return $this
     */
    public function setAggregatedColumns(array $columns)
    {
        $this->_aggregatedColumns = $columns;
        return $this;
    }
}