<?php

class Magestore_Faq_Model_Mysql4_Category_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    protected $_store_id = null;
    protected $_addedTable = array();
    protected $_storeField = array(
        'name',
        'status',
        'ordering'
    );
    public function _construct()
    {
        parent::_construct();
        $this->_init('faq/category');
    }
    public function getStoreId(){
        return $this->_store_id;
    }
    public function setStoreId($storeId,$array = null){
        $this->_store_id = $storeId;
        if($this->_store_id){
            $storeField = (isset($array)&&count($array))?$array:$this->_storeField;
            foreach ($storeField as $value) {
                $faqValue = Mage::getModel('faq/categoryvalue')->getCollection()
                    ->addFieldToFilter('store_id',$storeId)
                    ->addFieldToFilter('attribute_code',$value)
                    ->getSelect()
                    ->assemble();
                $this->getSelect()
                    ->joinLeft(
                        array(
                            'faq_category_value_'.$value => new Zend_Db_Expr("($faqValue)")), 
                            'main_table.category_id = faq_category_value_'.$value.'.category_id',
                            array($value => 'IF(faq_category_value_'.$value.'.value IS NULL,main_table.'.$value.',faq_category_value_'.$value.'.value)'));
            }
        }
        return $this;
    } 
    public function addFieldToFilter($field, $condition=null) {
        $attributes = array(
            'name',
            'status',
            'ordering'
        );
        $storeId = $this->getStoreId();
        if (in_array($field, $attributes) && $storeId) {
            // $select = $this->_select;
            // return $select->where("1=1");
            $this->getSelect()->where("IF(faq_category_value_$field.value IS NULL, main_table.$field, faq_category_value_$field.value) = $condition");
            return $this;
            // return parent::addFieldToFilter("IF(faq_category_value_$field.value IS NULL, main_table.$field, faq_category_value_$field.value)", $condition);
        }
        return parent::addFieldToFilter($field, $condition);
    }
}