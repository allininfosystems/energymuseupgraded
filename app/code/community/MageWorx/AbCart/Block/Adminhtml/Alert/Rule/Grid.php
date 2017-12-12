<?php
/**
 * MageWorx
 * Abandoned Cart Recovery
 *
 * @category   MageWorx
 * @package    MageWorx_AbCart
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_AbCart_Block_Adminhtml_Alert_Rule_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('mageworx_abcart_rule_grid');
        $this->setDefaultSort('sort_order');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
    }

    /**
     * @return $this|Mage_Adminhtml_Block_Widget_Grid
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getModel('mageworx_abcart/alert_rule')->getCollection();
        $collection->addRecoveredRevenue();
        $this->setCollection($collection);

        parent::_prepareCollection();
        return $this;
    }

    /**
     * Add grid columns
     *
     * @return Mage_Adminhtml_Block_Promo_Quote_Grid
     */
    protected function _prepareColumns()
    {
        $this->addColumn('rule_id', array(
            'header'    => Mage::helper('catalog')->__('ID'),
            'align'     =>'right',
            'width'     => '50px',
            'index'     => 'rule_id',
        ));

        $this->addColumn('name', array(
            'header'    => Mage::helper('salesrule')->__('Rule Name'),
            'align'     =>'left',
            'index'     => 'name',
        ));

        $currency = Mage::app()->getStore()->getBaseCurrencyCode();
        $this->addColumn('recovered_revenue', array(
            'header'    => Mage::helper('salesrule')->__('Recovered Revenue'),
            'align'     =>'left',
            'index'     => 'revenue',
            'filter_condition_callback' => array($this, 'filterByRevenue'),
            'type' => 'currency',
            'currency_code' => $currency,
        ));

        if (!Mage::app()->isSingleStoreMode()) {
            $this->addColumn('websites', array(
                'header'    => Mage::helper('catalog')->__('Website'),
                'align'     =>'left',
                'index'     => 'website_ids',
                'type'      => 'options',
                'sortable'  => false,
                'options'   => Mage::getSingleton('adminhtml/system_store')->getWebsiteOptionHash(),
                'width'     => 200,
            ));
        }

        $this->addColumn('status', array(
            'header'    => Mage::helper('catalog')->__('Status'),
            'align'     => 'left',
            'width'     => '80px',
            'index'     => 'status',
            'type'      => 'options',
            'options'   => array(
                1 => $this->__('Active'),
                0 => $this->__('Inactive'),
            ),
        ));

        parent::_prepareColumns();
        return $this;
    }

    /**
     * @param $collection
     * @param $column
     */
    public function filterByRevenue($collection, $column)
    {
        $filter = $column->getFilter()->getCondition();

        $cond = '';
        if(!empty($filter['from'])){
            $cond .= 'SUM(quote.base_subtotal) >= ' . $filter['from'];
            if(!empty($filter['to'])){
                $cond .= ' AND ';
            }
        }
        if(!empty($filter['to'])){
            $cond .= 'SUM(quote.base_subtotal) <= ' . $filter['to'];
        }

        $collection->getSelect()
            ->having($cond);
    }

    /**
     * Retrieve row click URL
     *
     * @param Varien_Object $row
     *
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('id' => $row->getRuleId()));
    }

    /**
     * @return $this|Mage_Adminhtml_Block_Widget_Grid
     */
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('rule_id');
        $this->getMassactionBlock()->setFormFieldName('rule_ids');

        $this->getMassactionBlock()->addItem('delete_rule', array(
            'label'=> Mage::helper('catalog')->__('Delete'),
            'url'  => $this->getUrl('*/*/massDelete'),
        ));

        $statuses = array(
            array('value' => 1, 'label' => $this->__('Active')),
            array('value' => 0, 'label' => $this->__('Inactive')),
        );

        array_unshift($statuses, array('label'=>'', 'value'=>''));
        $this->getMassactionBlock()->addItem('status', array(
            'label'=> Mage::helper('catalog')->__('Change status'),
            'url'  => $this->getUrl('*/*/massStatus', array('_current'=>true)),
            'additional' => array(
                'visibility' => array(
                    'name' => 'status',
                    'type' => 'select',
                    'class' => 'required-entry',
                    'label' => Mage::helper('catalog')->__('Status'),
                    'values' => $statuses
                )
            )
        ));

        return $this;
    }
}
