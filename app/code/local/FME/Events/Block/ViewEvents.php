<?php

class FME_Events_Block_ViewEvents extends Mage_Catalog_Block_Product_Abstract
{

    public function _prepareLayout() 
    {
        
        $params = $this->getRequest()->getParams();
        $prefix = $params['pfx'];
        $repeat = ($this->getRequest()->getParam('repeat'))? $params['repeat'] : '';

        
        $dets = Mage::getModel('events/events')
                ->setStoreId(Mage::app()->getStore()->getId())
                ->load($prefix, 'event_url_prefix');
       
        //echo '<pre>';print_r($dets->getData());exit;
        /* loading info with custom method */
        if ($head = $this->getLayout()->getBlock('head')) {
            if ($dets->getEventTitle() != '') {
                $head->setTitle($dets->getEventPageTitle());
            }

            if ($dets->getEventMetaKeywords() != '') {
                $head->setKeywords($dets->getEventMetaKeywords());
            }

            if ($dets->getEventMetaDescription() != '') {
                $head->setDescription($dets->getEventMetaDescription());
            }
        }

        /* setting event particular seo info */
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
                'events_view', array(
                'label' => Mage::helper('cms')->__($dets->getEventTitle()),
                'title' => Mage::helper('cms')->__($dets->getEventTitle()),
                'link' => false
                )
            );
        }

        return parent::_prepareLayout();
    }

    public function getEventProduct() 
    {
        $prefix = $this->getRequest()->getParam('pfx');
        $events = Mage::getModel('events/events')
                ->setStoreId(Mage::app()->getStore()->getId())
                ->load($prefix, 'event_url_prefix');
        $eventId = (int) $events->getEventId();
        $product = $events->getEventProducts($eventId);

        return $product;
    }

    public function getEvents() 
    {
        
        $params = $this->getRequest()->getParams();
        $prefix = $params['pfx'];
        $repeat = ($this->getRequest()->getParam('repeat'))? $params['repeat'] : '';

        
        if ($prefix == '') {
            $this->_forward('noRoute');
        }

        
        $events = Mage::getModel('events/events')
                ->setStoreId(Mage::app()->getStore()->getId())
                ->load($prefix, 'event_url_prefix');

        if (strtotime($repeat)) { 
            $events->loadRecurringInfo($repeat);
        }
        
        $events = $events->getData();
        return $events;
    }

    public function getEventGallery($store = null) 
    {
        if ($store == null) {
            $store = Mage::app()->getStore()->getId();
        }

        $prefix = $this->getRequest()->getParam('pfx');

        return Mage::helper('events')->fetchGallery($prefix, $store);
    }

}
