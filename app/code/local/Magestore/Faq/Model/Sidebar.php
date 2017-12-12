<?php

class Magestore_Faq_Model_Faqstore extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('faq/faqstore');
    }
    public function getUrlKey(){
		$store_id = $this->getStoreId() ? $this->getStoreId() : 0;
		$urlrewrite = Mage::getModel("faq/urlrewrite")->load("faq/".$store_id."/".$this->getFaqId(),"id_path");
		return $urlrewrite->getData('request_path');
	}
}