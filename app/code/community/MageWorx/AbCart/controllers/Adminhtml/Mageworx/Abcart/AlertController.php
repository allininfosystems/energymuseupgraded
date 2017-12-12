<?php
/**
 * MageWorx
 * Abandoned Cart Recovery
 *
 * @category   MageWorx
 * @package    MageWorx_AbCart
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_AbCart_Adminhtml_Mageworx_Abcart_AlertController extends  Mage_Adminhtml_Controller_Action
{
    protected function _init()
    {
        $this->loadLayout()
            ->_setActiveMenu('sales/mageworx_abcart');

        $this->_title($this->__('Sales'))
            ->_title($this->__('Abandoned Cart Recovery'));
    }

    public function indexAction()
    {
        $this->_init();
        $this->_title($this->__('Pending Alerts'));

        $this->renderLayout();
    }

    public function logAction()
    {
        $this->_init();
        $this->_title($this->__('Alerts Log'));

        $this->renderLayout();
    }

    public function massDeleteAction()
    {
        $alertIds = $this->getRequest()->getParam('alert_ids');
        if (!is_array($alertIds)) {
            $this->_getSession()->addError($this->__('Please select alert(s) to delete.'));
        } else {
            if (!empty($alertIds)) {
                try {
                    foreach ($alertIds as $alertId) {
                        $alert = Mage::getSingleton('mageworx_abcart/alert')->load($alertId);
                        $alert->setStatus(MageWorx_AbCart_Model_Alert::ALERT_STATUS_DELETED)->save();
                    }
                    $this->_getSession()->addSuccess(
                        $this->__('Total of %d alert(s) have been deleted.', count($alertIds))
                    );
                } catch (Exception $e) {
                    $this->_getSession()->addError($e->getMessage());
                }
            }
        }
        $this->_redirect('*/*/index');
    }

    protected function _isAllowed()
    {
        $action = strtolower($this->getRequest()->getActionName());
        switch ($action) {
            case 'log':
                return Mage::getSingleton('admin/session')->isAllowed('sales/mageworx_abcart/alert_log');
                break;
            default:
                return Mage::getSingleton('admin/session')->isAllowed('sales/mageworx_abcart/alert');
                break;
        }
    }
}