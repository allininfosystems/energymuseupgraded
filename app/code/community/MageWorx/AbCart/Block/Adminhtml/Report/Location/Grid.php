<?php
/**
 * MageWorx
 * Abandoned Cart Recovery
 *
 * @category   MageWorx
 * @package    MageWorx_AbCart
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_AbCart_Block_Adminhtml_Report_Location_Grid extends Mage_Adminhtml_Block_Report_Grid_Abstract
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
        return 'mageworx_abcart/report_cart_collection';
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

        $collection->setDateRange($filter->getFrom(), $filter->getTo())
            ->addBillingAddressData()
            ->addStoreFilter($storeIds);

        if ($this->getCountTotals()) {
            $totalsCollection = Mage::getResourceModel($this->getResourceCollectionName())
                ->isTotals(true)
                ->addBillingAddressData()
                ->setDateRange($filter->getData('from', null), $filter->getData('to', null));

            foreach ($totalsCollection as $item) {
                if ($item->getOrdersCount() > 0) {
                    $this->setTotals($item);
                    break;
                }
            }
        }

        $this->setCollection($collection);
    }

    /**
     * @return $this
     */
    protected function _prepareColumns()
    {
        $this->addColumn('country_code', array(
            'header'        => Mage::helper('core')->__('Country'),
            'index'         => 'country_code',
            'width'         => 200,
            'sortable'      => false,
            'totals_label'  => Mage::helper('mageworx_abcart')->__('Total'),
            'html_decorators' => array('nobr'),
            'renderer' => 'mageworx_abcart/adminhtml_report_column_renderer_country'
        ));

        $this->addColumn('started_carts', array(
            'header'    => Mage::helper('mageworx_abcart')->__('Carts Started'),
            'index'     => 'started_carts',
            'type'      => 'number',
            'sortable'  => false
        ));

        $this->addColumn('abandoned_carts', array(
            'header'    => Mage::helper('mageworx_abcart')->__('Carts Abandoned'),
            'index'     => 'abandoned_carts',
            'type'      => 'number',
            'sortable'  => false
        ));

        $currencyCode = $this->getCurrentCurrencyCode();

        $this->addColumn('abandoned_revenue', array(
            'header'    => Mage::helper('mageworx_abcart')->__('Abandoned Revenue'),
            'index'     => 'abandoned_revenue',
            'type'      => 'currency',
            'currency_code'  => $currencyCode,
            'sortable'  => false
        ));

        $this->addColumn('abandoned_rate', array(
            'width'     => '100px',
            'header'    => Mage::helper('mageworx_abcart')->__('Abandoned Rate'),
            'renderer'  => 'mageworx_abcart/adminhtml_report_column_renderer_percent',
            'sortable'  => false
        ));

        $this->addColumn('recovered_carts', array(
            'header'    => Mage::helper('mageworx_abcart')->__('Carts Recovered'),
            'index'     => 'recovered_carts',
            'type'      => 'number',
            'sortable'  => false
        ));

        $this->addColumn('recovered_revenue', array(
            'header'    => Mage::helper('mageworx_abcart')->__('Recovered Revenue'),
            'index'     => 'recovered_revenue',
            'type'      => 'currency',
            'currency_code'  => $currencyCode,
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
            /*$chart = $this->getLayout()->createBlock('mageworx_abcart/adminhtml_report_chart_type_country');
            $chart->setChartData($this->getCollection()->getData());
            $html .= $chart->toHtml();*/
            Mage::getSingleton('core/session')->setLocationChartData($this->getCollection()->getData());
            $html .= '<iframe id="map_frame" src="'.Mage::getModel('adminhtml/url')->getUrl('adminhtml/mageworx_abcart_report/locationMap').'"></iframe>';
        }

        $html .= parent::_toHtml();

        return $html;
    }
}
