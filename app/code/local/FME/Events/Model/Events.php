<?php

class FME_Events_Model_Events extends Mage_Core_Model_Abstract
{

    public function _construct() 
    {
        parent::_construct();
        $this->_init('events/events');
    }

    /* for admin purpose only
      @return unknown
     */

    public function getImageList() 
    {
        if (!$this->hasData('images_all')) {
            $_object = $this->_getResource()->loadImage();
        }

        return $this->getData('images_all');
    }

    /**
     * Retrieve related products
     * @return array
     */
    public function getEventsRelatedProducts($eventsId) 
    {
        $events_productTable = Mage::getSingleton('core/resource')->getTableName('events_product');
        $collection = Mage::getModel('events/events')->getCollection()
                ->addEventsFilter($eventsId);
        //echo '<pre>';print_r($collection);exit;
        $collection->getSelect()
                ->joinLeft(
                    array('related' => $events_productTable), 'main_table.event_id = related.eventid'
                )
                ->order('main_table.event_id');

        return $collection->getData();
    }

    public function getEventProducts($eventid) 
    {
        $resource = Mage::getSingleton('core/resource');
        $_read = $resource->getConnection('core_read');
        $select = $_read->select()
                ->from(array('p' => $resource->getTableName('events_product')), 'p.product_id')
                ->where('p.eventid = (?)', $eventid);
        //$query = "SELECT products_id FROM " . $resource->getTableName('events_products')." WHERE eventsid ={$eventid}";
        $productIds = $_read->fetchOne($select);

        return $productIds;
    }

    public function loadByPrefix($prefix) 
    {
        $this->_getResource()->loadByPrefix($this, $prefix);

        return $this;
    }

    public function loadRecurringInfo($repeat) 
    {
        
        $this->_getResource()->loadRecurringInfo($this, $repeat);
        
        return $this;
    }
    
    public function getRecurringEvent($date, $store = null) 
    {

        if ($store == null) {
            $store = Mage::app()->getStore()->getId();
        }

        $resource = Mage::getSingleton('core/resource');
        $table = $resource->getTableName('events/recurring');
        $read = $resource->getConnection('core_read');

        $select = $read->select()
                ->from(array('r' => $table))
                ->join(array('e' => $resource->getTableName('events/events')), 'r.event_id = e.event_id', array('e.event_title', 'e.event_url_prefix', 'e.event_start_date', 'e.skip_date'))
                ->join(array('es' => $resource->getTableName('events/events_store')), 'e.event_id = es.event_id', array())
                ->where('es.store_id IN (?)', array(0, $store))
                //->where('r.recurring_start_dates LIKE ("?")', new Zend_DB_Expr('%' . $date . '%'))
                //->where('DATE(e.skip_date) NOT IN (?)', array($date))
                ->where('DATE(e.event_start_date) != ?', $date)
                ->where("'$date' BETWEEN DATE(r.recurring_start_dates) AND DATE(r.recurring_end_dates) OR DATE(r.recurring_start_dates) = {$date}")
                //->orWhere('DATE(r.recurring_start_dates) = (?)', $date)
                //->where('DATE(r.recurring_end_dates) = (?)', $date)
                //->where('DATE(e.event_start_date) != ?', $date)
                ->where('e.event_status = (?)', 1)
                ->where('e.is_recurring = (?)', 1);
//echo $select;
        return $read->fetchAll($select);
    }

}
