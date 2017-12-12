<?php
/**
 * MageWorx
 * Abandoned Cart Recovery
 *
 * @category   MageWorx
 * @package    MageWorx_AbCart
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_AbCart_Model_Resource_Alert_Rule_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    /**
     * Map of fields
     *
     * @var array
     */
    protected $_map = array('fields' => array(
        'rule_id'                   => 'main_table.rule_id',
        'name'                      => 'main_table.name',
        'website_ids'               => 'main_table.website_ids',
        'customer_group_ids'        => 'main_table.customer_group_ids',
        'status'                    => 'main_table.status'
    ));

    protected function _construct()
    {
        $this->_init('mageworx_abcart/alert_rule');
    }

    /**
     * @return array
     */
    public function toOptionHash()
    {
        return parent::_toOptionHash('rule_id', 'name');
    }

    /**
     * @return $this
     */
    public function addRecoveredRevenue()
    {
        $this->getSelect()
            ->joinLeft(array('alert' => $this->getTable('mageworx_abcart/alert')),
                'alert.rule_id = main_table.rule_id',
                array())
            ->joinLeft(array('quote' => $this->getTable('sales/quote')),
                'quote.entity_id = alert.quote_id AND quote.abcart_status = "'.MageWorx_AbCart_Model_Cart::CART_STATUS_RECOVERED.'"',
                array('revenue' => 'SUM(quote.base_subtotal)', 'base_currency_code'))
            ->group('main_table.rule_id');
        return $this;
    }

    /**
     * @return $this
     */
    public function addStatusFilter()
    {
        $this->getSelect()
            ->where('main_table.status = 1');

        return $this;
    }

    /**
     * @return Varien_Db_Select
     */
    public function getSelectCountSql()
    {
        $countSelect = parent::getSelectCountSql();
        $countSelect->reset(Zend_Db_Select::ORDER);
        $countSelect->reset(Zend_Db_Select::LIMIT_COUNT);
        $countSelect->reset(Zend_Db_Select::LIMIT_OFFSET);
        $countSelect->reset(Zend_Db_Select::HAVING);
        $countSelect->reset(Zend_Db_Select::GROUP);
        $countSelect->resetJoinLeft();
        return $countSelect;
    }
}