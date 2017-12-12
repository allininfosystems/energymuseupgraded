<?php

class FME_Events_Adminhtml_FmeController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction()
    {
        $this->loadLayout()->_setActiveMenu('events/items')->_addBreadcrumb(Mage::helper('adminhtml')->__('Events Manager'), Mage::helper('adminhtml')->__('Events Manager'));
        $this->renderLayout();
    }

    protected function _isAllowed()
    {
        return true;
    }
}
