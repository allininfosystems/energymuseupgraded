<?php

class Magestore_Faq_Model_Faqvalue extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('faq/faqvalue');
    }
	public function loadByFaqIdStore($faqid,$store_id,$attribute_code)
	{
            $collection = $this->getCollection()
                ->addFieldToFilter('faq_id',$faqid)
                ->addFieldToFilter('store_id',$store_id)
                ->addFieldToFilter('attribute_code',$attribute_code)
                ;
            return $collection->getFirstItem();
	}
	protected function _prepareLoadData($collection)
	{
            return $collection;				
	}
}