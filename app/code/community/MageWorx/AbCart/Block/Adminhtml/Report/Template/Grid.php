<?php
/**
 * MageWorx
 * Abandoned Cart Recovery
 *
 * @category   MageWorx
 * @package    MageWorx_AbCart
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_AbCart_Block_Adminhtml_Report_Template_Grid extends Mage_Adminhtml_Block_Report_Grid_Abstract
{
    public function __construct()
    {
        parent::__construct();
        $this->setCountTotals(true);
    }

    /**
     * @return string
     */
    public function getResourceCollectionName()
    {
        return 'mageworx_abcart/alert_collection';
    }

    /**
     * @return $this|Mage_Adminhtml_Block_Widget_Grid
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel($this->getResourceCollectionName());

        $filter = $this->getFilterData();

        if(!$filter->getFrom() || !$filter->getTo()){
            $this->setCountTotals(false);
            $this->setCountSubTotals(false);
            return $this;
        }

        $storeIds = $filter->getStoreIds() ? explode(',', $filter->getStoreIds()) : array();

        $collection->getTemplateCollection()
            ->setDateRange($filter->getFrom(), $filter->getTo())
            ->addStoreFilter($storeIds);

        if ($this->getCountTotals()) {
            $totalsCollection = Mage::getResourceModel($this->getResourceCollectionName())
                ->isTotals(true)
                ->getTemplateCollection();

            $totalsCollection->getSelect()->reset('group');
            $this->setTotals($totalsCollection->getFirstItem());
        }

        $this->setCollection($collection);
    }

    /**
     * @return $this
     */
    protected function _prepareColumns()
    {
        $this->addColumn('template', array(
            'header'        => Mage::helper('catalog')->__('Template'),
            'index'         => 'template_name',
            'width'         => 200,
            'sortable'      => false,
            'totals_label'  => Mage::helper('mageworx_abcart')->__('Total'),
            'html_decorators' => array('nobr'),
        ));

        $this->addColumn('sent', array(
            'header'    => Mage::helper('mageworx_abcart')->__('Emails Sent'),
            'index'     => 'sent',
            'type'      => 'number',
            'sortable'  => false
        ));

        $this->addColumn('clicked', array(
            'header'    => Mage::helper('mageworx_abcart')->__('Link Clicked'),
            'index'     => 'clicked',
            'type'      => 'number',
            'sortable'  => false
        ));

        $this->addColumn('recovered', array(
            'header'    => Mage::helper('mageworx_abcart')->__('Recovered'),
            'index'     => 'recovered',
            'type'      => 'number',
            'sortable'  => false
        ));

        $this->addColumn('recovered_revenue', array(
            'header'    => Mage::helper('mageworx_abcart')->__('Recovered Revenue'),
            'index'     => 'recovered_revenue',
            'type'      => 'currency',
            'currency_code'  => 'USD',
            'sortable'  => false
        ));

        return parent::_prepareColumns();
    }

    /**
     * @return string
     */
    protected function _toHtml()
    {
        $html = '';

        if ($this->getCollection() && $this->getCollection()->getSize()) {
            $chart = $this->getLayout()->createBlock('mageworx_abcart/adminhtml_report_chart_template');
            $chart->setChartData($this->getCollection()->getData());
            $html .= $chart->getChartHtml();
        }

        $html .= parent::_toHtml();

        return $html;
    }
}
