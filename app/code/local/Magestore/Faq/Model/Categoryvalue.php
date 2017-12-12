<?php

class Magestore_Faq_Model_Categoryvalue extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('faq/categoryvalue');
    }
	public function loadByCatIdStore($catid,$store_id,$attribute_code)
	{//zend_debug::dump($catid.','.$store_id.','.$attribute_code);die('1');
	//zend_debug::dump($this->getCollection());die('ggg');
		$collection = $this->getCollection()
							->addFieldToFilter('category_id',$catid)
							->addFieldToFilter('store_id',$store_id)
							->addFieldToFilter('attribute_code',$attribute_code)
							;
		return $collection->getFirstItem();
	}
}