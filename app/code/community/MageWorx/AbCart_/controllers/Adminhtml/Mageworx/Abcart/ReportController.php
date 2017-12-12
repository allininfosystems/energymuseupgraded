<?php
/**
 * MageWorx
 * Abandoned Cart Recovery
 *
 * @category   MageWorx
 * @package    MageWorx_AbCart
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_AbCart_Adminhtml_Mageworx_AbCart_ReportController extends  Mage_Adminhtml_Controller_Action
{
    public function _initAction()
    {
        $this->loadLayout()
            ->_addBreadcrumb(Mage::helper('reports')->__('Reports'), Mage::helper('reports')->__('Reports'));
        return $this;
    }

    public function customerGroupAction()
    {
        $this->_initAction();
        $this->_prepareReportAction('group', $this->__('Customer Group'));
        $this->renderLayout();
    }

    public function storeviewAction()
    {
        $this->_initAction();
        $this->_prepareReportAction('storeview', $this->__('Store View'));
        $this->renderLayout();
    }

    public function locationAction()
    {
        $this->_initAction();
        $this->_prepareReportAction('location', $this->__('Customer Location'));
        $this->renderLayout();
    }

    public function locationMapAction()
    {
        $chart = $this->getLayout()->createBlock('mageworx_abcart/adminhtml_report_chart_type_country');
        $data =  Mage::getSingleton('core/session')->getLocationChartData();
        $chart->setChartData($data);

        $this->getResponse()->setBody($chart->toHtml());
    }

    public function deviceAction()
    {
        $this->_initAction();
        $this->_prepareReportAction('device', $this->__('Device Type'));
        $this->renderLayout();
    }

    public function templateAction()
    {
        $this->_initAction();
        $this->_prepareReportAction('template', $this->__('Email Templates'));
        $this->renderLayout();
    }

    protected function _prepareReportAction($reportId, $pageName)
    {
        $this->_title($this->__('Reports'))->_title($this->__('Abandoned Carts'))->_title($pageName);
        $this->_setActiveMenu('report/shopcart');

        $gridBlock = $this->getLayout()->getBlock('adminhtml_report_'.$reportId.'.grid');
        $filterFormBlock = $this->getLayout()->getBlock('grid.filter.form');

        $this->_initReportAction(array(
            $gridBlock,
            $filterFormBlock
        ));
    }

    public function _initReportAction($blocks)
    {
        if (!is_array($blocks)) {
            $blocks = array($blocks);
        }

        $requestData = Mage::helper('adminhtml')->prepareFilterString($this->getRequest()->getParam('filter'));
        $requestData = $this->_filterDates($requestData, array('from', 'to'));
        $requestData['store_ids'] = $this->getRequest()->getParam('store_ids');
        $params = new Varien_Object();

        foreach ($requestData as $key => $value) {
            if (!empty($value)) {
                $params->setData($key, $value);
            }
        }

        foreach ($blocks as $block) {
            if ($block) {
                $block->setPeriodType($params->getData('period_type'));
                $block->setFilterData($params);
            }
        }

        return $this;
    }

    protected function _isAllowed()
    {
        $action = strtolower($this->getRequest()->getActionName());
        switch ($action) {
            case 'customergroup':
                return Mage::getSingleton('admin/session')->isAllowed('report/shopcart/mageworx_abcart_customer_group');
                break;
            case 'storeview':
                return Mage::getSingleton('admin/session')->isAllowed('report/shopcart/mageworx_abcart_customer_storeview');
                break;
            case 'location':
            case 'locationmap':
                return Mage::getSingleton('admin/session')->isAllowed('report/shopcart/mageworx_abcart_customer_location');
                break;
            case 'device':
                return Mage::getSingleton('admin/session')->isAllowed('report/shopcart/mageworx_abcart_device_type');
                break;
            case 'template':
                return Mage::getSingleton('admin/session')->isAllowed('report/shopcart/mageworx_abcart_email_template');
                break;
            default:
                return Mage::getSingleton('admin/session')->isAllowed('report/shopcart');
                break;
        }
    }
}