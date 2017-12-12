<?php

class FME_Events_Block_CalendarEvents extends Mage_Core_Block_Template
{

    public function _prepareLayout() 
    {
        if ($headBlock = $this->getLayout()->getBlock('head')) {
            $month = (int) (@$this->getRequest()->getParam('month') ? @$this->getRequest()->getParam('month') : date('m'));
            //echo strtotime($month);
            $year = (int) (@$this->getRequest()->getParam('year') ? $this->getRequest()->getParam('year') : date('Y'));
            $headBlock->setTitle("Events Calendar For " . date("F", mktime(0, 0, 0, $month, 10)) . ' - ' . date('Y', strtotime($year)));
        }

        if (Mage::helper('events')->isEnableBreadcrumbs()) {
            $breadcrumbs = $this->getLayout()->getBlock('breadcrumbs');
            $breadcrumbs->addCrumb(
                'events', array(
                'label' => Mage::helper('cms')->__(Mage::helper('events')->linkTitleHeader()),
                'title' => Mage::helper('cms')->__(Mage::helper('events')->linkTitleHeader()),
                'link' => Mage::helper('events')->clientUrl()
                )
            );
            $breadcrumbs->addCrumb(
                'events_cal', array(
                'label' => Mage::helper('cms')->__('Events Calendar'),
                'title' => Mage::helper('cms')->__('Events Calendar'),
                'link' => false
                )
            );
        }

        return parent::_prepareLayout();
    }

}
