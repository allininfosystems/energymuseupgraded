<?php
/**
 * MageWorx
 * Abandoned Cart Recovery
 *
 * @category   MageWorx
 * @package    MageWorx_AbCart
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_AbCart_Block_Adminhtml_Alert_Log_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('mageworx_abcart_log_grid');
        $this->setDefaultSort('alert_id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
    }

    /**
     * @return $this|Mage_Adminhtml_Block_Widget_Grid
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getModel('mageworx_abcart/alert')->getCollection();
        $collection->addQuoteData()
            ->addQuoteItems()
            ->addLogData()
            ->addOrderId()
            ->addCartStatus();

        $collection->addStatusFilter(
            array(
                MageWorx_AbCart_Model_Alert::ALERT_STATUS_SENT,
                MageWorx_AbCart_Model_Alert::ALERT_STATUS_DELETED
            )
        );

        $this->setCollection($collection);

        parent::_prepareCollection();
        return $this;
    }

    /**
     * @return $this
     */
    protected function _prepareColumns()
    {
        $this->addColumn('alert_id', array(
            'header'    => Mage::helper('catalog')->__('ID'),
            'align'     =>'right',
            'width'     => '50px',
            'index'     => 'alert_id',
        ));

        $this->addColumn('abandoned_at', array(
            'header'    => Mage::helper('mageworx_abcart')->__('Abandoned At'),
            'align'     =>'left',
            'index'     => 'abandoned_at',
            'type' => 'datetime',
            'width' => '160px',
        ));

        $this->addColumn('sent_at', array(
            'header'    => Mage::helper('mageworx_abcart')->__('Sent At'),
            'align'     =>'left',
            'index'     => 'executed_at',
            'type' => 'datetime',
            'width' => '160px',
        ));

        $this->addColumn('recovered_at', array(
            'header'    => Mage::helper('mageworx_abcart')->__('Recovered At'),
            'align'     =>'left',
            'index'     => 'recovered_at',
            'type' => 'datetime',
            'width' => '160px',
        ));

        $this->addColumn('customer_name', array(
            'header'    => Mage::helper('mageworx_abcart')->__('Customer Name'),
            'index'     => 'customer_name',
        ));

        $this->addColumn('customer_email', array(
            'header'    => Mage::helper('mageworx_abcart')->__('Customer Email'),
            'index'     => 'customer_email',
        ));

        $this->addColumn('customer_group', array(
            'header'    => Mage::helper('catalog')->__('Customer Group'),
            'type' => 'options',
            'options' => Mage::getResourceModel('customer/group_collection')->toOptionHash(),
            'index'     => 'customer_group_id',
        ));

        $this->addColumn('rule', array(
            'header'    => Mage::helper('mageworx_abcart')->__('Rule'),
            'type' => 'options',
            'options' => Mage::getResourceModel('mageworx_abcart/alert_rule_collection')->toOptionHash(),
            'index'     => 'rule_id',
        ));

        $this->addColumn('cart_total', array(
            'header'    => Mage::helper('mageworx_abcart')->__('Cart Total'),
            'type'      => 'currency',
            'currency_code'  => Mage::app()->getStore()->getBaseCurrencyCode(),
            'index'     => 'base_subtotal',
        ));

        $this->addColumn('order_id', array(
            'header'    => Mage::helper('mageworx_abcart')->__('Order #'),
            'index'     => 'increment_id',
        ));

        $this->addColumn('products', array(
            'header'    => Mage::helper('catalog')->__('Products'),
            'index'     => 'product_names',
            'renderer'  => 'mageworx_abcart/adminhtml_alert_grid_renderer_products',
            'filter'    => false
        ));

        $this->addColumn('status', array(
            'header'    => Mage::helper('mageworx_abcart')->__('Alert Status'),
            'type' => 'options',
            'options' => Mage::getModel('mageworx_abcart/alert')->getStatusOptionArray(false),
            'index'     => 'status',
        ));

        $this->addColumn('cart_status', array(
            'header'    => Mage::helper('mageworx_abcart')->__('Cart Status'),
            'type' => 'options',
            'options' => Mage::getModel('mageworx_abcart/cart')->getStatusOptionArray(false),
            'index'     => 'cart_status'
        ));

        parent::_prepareColumns();
        return $this;
    }

    /**
     * @param $collection
     * @param $column
     */
    protected function _gridFilter($collection, $column)
    {
        $filter = $column->getFilter()->getCondition();
        $cond = $column->getFilter()->getCondition();
    }

    /**
     * @return $this|Mage_Adminhtml_Block_Widget_Grid
     */
    protected function _prepareMassaction()
    {
        return $this;
    }

    /**
     * @param $row
     * @return bool|string
     */
    public function getRowUrl($row)
    {
        return false;
    }

}
