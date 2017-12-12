<?php

class Magestore_Faq_Model_Mysql4_Faqvalue extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        $this->_init('faq/faqvalue', 'faq_value_id');
    }
	
}