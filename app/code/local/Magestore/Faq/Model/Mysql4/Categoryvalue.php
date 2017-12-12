<?php

class Magestore_Faq_Model_Mysql4_Categoryvalue extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        // Note that the faq_id refers to the key field in your database table.
        $this->_init('faq/categoryvalue', 'category_value_id');
    }
}