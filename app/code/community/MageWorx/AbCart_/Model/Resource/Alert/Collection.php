<?php
/**
 * MageWorx
 * Abandoned Cart Recovery
 *
 * @category   MageWorx
 * @package    MageWorx_AbCart
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_AbCart_Model_Resource_Alert_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    /**
     * Map of fields
     *
     * @var array
     */
    protected $_map = array('fields' => array(
        'abandoned_at'      => 'abandoned.changed_at',
        'recovered_at'      => 'recovered.changed_at',
        'customer_name'     => 'CONCAT(quote.customer_firstname, " ", quote.customer_lastname)',
        'customer_email'    => 'quote.customer_email',
        'customer_group_id'    => 'quote.customer_group_id',
        'rule_id'           => 'main_table.rule_id',
        'base_subtotal'     => 'quote.base_subtotal',
        'status'            => 'main_table.status',
        'cart_status'       => 'main_table.status',
        'rule_name'         => 'rule.name',
    ));

    protected function _construct()
    {
        $this->_init('mageworx_abcart/alert');
    }

    /**
     * @return array
     */
    protected function _getQuoteFields()
    {
        return array(
            'customer_name' => 'CONCAT(quote.customer_firstname, " ", quote.customer_lastname)',
            'customer_email',
            'customer_group_id',
            'base_subtotal',
            'store_id'
        );
    }



    /**
     * @return $this
     */
    public function addRuleData()
    {
        $this->getSelect()
            ->joinLeft(array('rule' => $this->getTable('mageworx_abcart/alert_rule')),
                'rule.rule_id = main_table.rule_id',
                 array('rule_name' => 'rule.name'));

        return $this;
    }

    /**
     * @return $this
     */
    public function addQuoteData()
    {
        $this->getSelect()
            ->joinLeft(array('quote' => $this->getTable('sales/quote')),
                'quote.entity_id = main_table.quote_id',
                $this->_getQuoteFields())
            ->where('quote.customer_email IS NOT NULL');

        return $this;
    }

    /**
     * @return $this
     */
    public function addOrderId()
    {
        $this->getSelect()
            ->joinLeft(array('order' => $this->getTable('sales/order')),
            'order.quote_id = main_table.quote_id',
            array('increment_id'));

        return $this;
    }

    /**
     * @param $status
     * @return $this
     */
    public function addStatusFilter($status)
    {
        $this->getSelect()
            ->where('main_table.status IN(?)', $status);

        return $this;
    }

    /**
     * @todo Remove if not used
     *
     * @return MageWorx_AbCart_Model_Resource_Alert_Collection
     */
    public function addStatusData()
    {
        $this->getSelect()
            ->joinLeft(array('abandoned' => $this->getTable('mageworx_abcart/cart_log')),
                'abandoned.quote_id = main_table.quote_id',
                array('abandoned_at' => 'abandoned.changed_at'))
            ->where('abandoned.status = ?', MageWorx_AbCart_Model_Cart::CART_STATUS_ABANDONED);

        return $this;
    }

    /**
     * @return $this
     */
    public function addLogData()
    {
        $this->getSelect()
            ->joinLeft(array('abandoned' => $this->getTable('mageworx_abcart/cart_log')),
                'abandoned.quote_id = main_table.quote_id and abandoned.status = "'.MageWorx_AbCart_Model_Cart::CART_STATUS_ABANDONED.'"',
                array('abandoned_at' => 'abandoned.changed_at'))
            ->joinLeft(array('recovered' => $this->getTable('mageworx_abcart/cart_log')),
                'recovered.quote_id = main_table.quote_id and recovered.status = "'.MageWorx_AbCart_Model_Cart::CART_STATUS_RECOVERED.'"',
                array('recovered_at' => 'recovered.changed_at'));

        return $this;
    }

    /**
     * @return $this
     */
    public function addCouponData()
    {
        $this->getSelect()
            ->joinLeft(array('coupon' => $this->getTable('salesrule/coupon')),
                'coupon.rule_id = rule.coupon_id',
                array('coupon_code' => 'coupon.code'));

        return $this;
    }

    /**
     * @return $this
     */
    public function addQuoteItems()
    {
        $this->getSelect()
            ->joinLeft(array('quote_item' => $this->getTable('sales/quote_item')),
                'quote_item.quote_id = main_table.quote_id AND quote_item.parent_item_id IS NULL',
                array('product_names' => new Zend_Db_Expr('GROUP_CONCAT(quote_item.`name` SEPARATOR "%new_product%")')
                ))
            ->group('quote_item.quote_id')
            ->group('main_table.alert_id');


        return $this;
    }

    /**
     * @return $this
     */
    public function addCartStatus()
    {
        $subSelect = clone $this->getSelect();
        $subSelect->reset();
        $subSelect->from(array('log' => $this->getTable('mageworx_abcart/cart_log')))
                ->order('changed_at DESC');

        $select = clone $subSelect;
        $select->reset()
            ->from(array('t1' => $subSelect))
            ->group('t1.quote_id');

        $this->getSelect()
            ->joinLeft(array('cart_status_tbl' => $select),
                'cart_status_tbl.quote_id = main_table.quote_id',
                array('cart_status' => 'cart_status_tbl.status'));

        return $this;
    }

    /**
     * @return $this
     */
    public function getTemplateCollection()
    {
        $clickedSelect = clone $this->getSelect();
        $clickedSelect->reset()
            ->from(array('t1' => $this->getTable('mageworx_abcart/cart_log')))
            ->where('t1.status = "link_clicked"')
            ->group('t1.quote_id');

        $this->getSelect()
            ->reset('columns')
            ->columns(array('sent' => 'COUNT(main_table.status)', 'main_table.email_template', 'template_name' => 'template.template_code'))
            ->joinLeft(array('clicked_log' => $clickedSelect),
                "clicked_log.quote_id = main_table.quote_id",
                array('clicked' => 'SUM(IF(ISNULL(clicked_log.status), 0, 1))'))
            ->joinLeft(array('recovered_log' => $this->getTable('mageworx_abcart/cart_log')),
                "recovered_log.quote_id = main_table.quote_id and recovered_log.status = 'recovered'",
                array('recovered' => 'SUM(IF(ISNULL(recovered_log.status), 0, 1))'))
            ->joinLeft(array('quote' => $this->getTable('sales/quote')),
                "main_table.quote_id = quote.entity_id and recovered_log.status = 'recovered'",
                array('recovered_revenue' => 'SUM(IFNULL(quote.base_subtotal, 0))'))
            ->joinLeft(array('template' => $this->getTable('core/email_template')),
                'main_table.email_template = template.template_id')
            ->where('main_table.status = "sent"')
            ->group('main_table.email_template');

        return $this;
    }

    /**
     * @param $from
     * @param $to
     * @return $this
     */
    public function setDateRange($from, $to)
    {
        $this->getSelect()->where('main_table.executed_at BETWEEN "'.$from.' 00:00:00" AND "'.$to.' 23:59:59"');
        return $this;
    }

    public function addOrderStatusFilter()
    {
        return $this;
    }

    public function setPeriod()
    {
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

    /**
     * @param null $flag
     * @return $this
     */
    public function isTotals($flag = null)
    {
        if (is_null($flag)) {
            return $this->_isTotals;
        }
        $this->_isTotals = $flag;
        return $this;
    }

    public function addAlertsToSend() {
        $this->getSelect()->where("UNIX_TIMESTAMP(main_table.scheduled_at) < UNIX_TIMESTAMP('".Mage::getSingleton('core/date')->gmtDate()."')");
        return $this;
    }

    /**
     * @return Varien_Db_Select
     */
    public function getSelectCountSql() {
        $countSelect = parent::getSelectCountSql();
        $countSelect->reset(Zend_Db_Select::GROUP);
        return $countSelect;
    }
}