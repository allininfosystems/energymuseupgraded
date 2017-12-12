<?php

class Magestore_Faq_Model_Category extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('faq/category');
    }
    public function load($id, $field = null) {
        $storeId = $this->getStoreId();
        parent::load($id, $field);
        if ($storeId) {
            $this->loadStoreValue($storeId);
        }
        return $this;
    }
    public function loadStoreValue($storeId = null) {
        if (!$storeId)
            $storeId = $this->getStoreId(); //zend_debug::dump($storeId);die('22');
        if (!$storeId)
            return $this;

        $storeValues = Mage::getModel('faq/categoryvalue')->getCollection()
                ->addFieldToFilter('category_id', $this->getId())
                ->addFieldToFilter('store_id', $storeId);
        foreach ($storeValues as $value) {
            $this->setData($value->getAttributeCode() . '_in_store', true);
            $this->setData($value->getAttributeCode(), $value->getValue());
        }

        return $this;
    }
}