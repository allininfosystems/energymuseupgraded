<?php
/**
 * MageWorx
 * Abandoned Cart Recovery
 *
 * @category   MageWorx
 * @package    MageWorx_AbCart
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_AbCart_Block_Adminhtml_Alert_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('mageworx_abcart_alert_grid');
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
        $collection->addStatusFilter(MageWorx_AbCart_Model_Alert::ALERT_STATUS_PENDING)
            ->addQuoteData()
            ->addQuoteItems()
            ->addRuleData()
            ->addStatusData();

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
            'header'    => Mage::helper('salesrule')->__('Abandoned At'),
            'align'     =>'left',
            'index'     => 'abandoned_at',
            'type' => 'datetime',
            'width' => '160px',
        ));

        $this->addColumn('scheduled_at', array(
            'header'    => Mage::helper('salesrule')->__('Scheduled At'),
            'align'     =>'left',
            'index'     => 'scheduled_at',
            'type' => 'datetime',
            'width' => '160px',
        ));

        $this->addColumn('rule_name', array(
            'header'    => Mage::helper('salesrule')->__('Rule'),
            'index'     => 'rule_name',
        ));

        $this->addColumn('customer_name', array(
            'header'    => Mage::helper('salesrule')->__('Customer Name'),
            'index'     => 'customer_name',
        ));

        $this->addColumn('customer_email', array(
            'header'    => Mage::helper('salesrule')->__('Customer Email'),
            'index'     => 'customer_email',
        ));

        $this->addColumn('customer_group', array(
            'header'    => Mage::helper('catalog')->__('Customer Group'),
            'type' => 'options',
            'options' => Mage::getResourceModel('customer/group_collection')->toOptionHash(),
            'index'     => 'customer_group_id',
        ));

        $this->addColumn('products', array(
            'header'    => Mage::helper('catalog')->__('Products'),
            'index'     => 'product_names',
            'renderer'  => 'mageworx_abcart/adminhtml_alert_grid_renderer_products',
            'filter' => false,
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
        $this->setMassactionIdField('alert_id');
        $this->getMassactionBlock()->setFormFieldName('alert_ids');

        $this->getMassactionBlock()->addItem('delete_alert', array(
            'label'=> Mage::helper('catalog')->__('Remove'),
            'url'  => $this->getUrl('*/*/massDelete'),
        ));

        return $this;
    }

}
