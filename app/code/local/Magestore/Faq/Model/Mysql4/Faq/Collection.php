<?php

class Magestore_Faq_Model_Mysql4_Faq_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    protected $_store_id = null;
    protected $_storeField = array(
        'title',
        'status',
        'description',
        'ordering',
        'tag'
    );
    public function _construct()
    {
        parent::_construct();
        $this->_init('faq/faq');
    }
    public function setStoreId($storeId,$array = null){
        $this->_store_id = $storeId;
        if($this->_store_id){
            $storeField = (isset($array)&&count($array))?$array:$this->_storeField;
            foreach ($storeField as $value) {
                $faqValue = Mage::getModel('faq/faqvalue')->getCollection()
                    ->addFieldToFilter('store_id',$storeId)
                    ->addFieldToFilter('attribute_code',$value)
                    ->getSelect()
                    ->assemble();
                $this->getSelect()
                    ->joinLeft(
                        array(
                            'faq_value_'.$value => new Zend_Db_Expr("($faqValue)")), 
                            'main_table.faq_id = faq_value_'.$value.'.faq_id',
                            array($value => 'IF(faq_value_'.$value.'.value IS NULL,main_table.'.$value.',faq_value_'.$value.'.value)'));
            }
        }
        return $this;
    } 
	public function getStoreId(){
		return $this->_store_id;
	}
        public function addFieldToFilter($field, $condition=null) {
            $attributes = array(
                'title',
                'status',
                'description',
                'ordering',
                'tag'
            );

            $storeId = $this->getStoreId();
            //if(!$storeId) $storeId = Mage::app()->getStore()->getStoreId();
            if (in_array($field, $attributes) && $storeId) {
                $this->getSelect()->where("IF(faq_value_$field.value IS NULL, main_table.$field, faq_value_$field.value) = $condition");
                return $this;
                // return parent::addFieldToFilter("IF(faq_value_$field.value IS NULL, main_table.$field, faq_value_$field.value)", $condition);
            }
            return parent::addFieldToFilter($field, $condition);
        }
        
        //Duy Tuan
        public function getAllTags(){
            $tags = array();
            $tags_temp = array();
            $idsSelect = clone $this->getSelect();
            $idsSelect->reset(Zend_Db_Select::ORDER);
            $idsSelect->reset(Zend_Db_Select::LIMIT_COUNT);
            $idsSelect->reset(Zend_Db_Select::LIMIT_OFFSET);
            $idsSelect->reset(Zend_Db_Select::COLUMNS);
             if($this->_store_id){
            $idsSelect->columns('IF(faq_value_tag.value IS NULL, main_table.tag, faq_value_tag.value)');
             }else{
                 $idsSelect->columns('main_table.tag');
             }
            $idsSelect->resetJoinLeft();
            $array_tags = $this->getConnection()->fetchCol($idsSelect);

            foreach($array_tags as $data){
                $temp = explode(", ",$data);
                foreach($temp as $item) {

                    if(!in_array($item,$tags)&& $item!=null){
                        $tags[]=$item;
                        $tags_temp[$item] = 1;
                    }else{
                        if($item!=null)
                        $tags_temp[$item] += 1;
                    }
                }
            }
            for($i=0;$i<count($tags);$i++){
                for($j=$i+1;$j<count($tags);$j++){
                    if($tags_temp[$tags[$j]]>$tags_temp[$tags[$i]]){
                        $a = $tags[$i];
                        $tags[$i] = $tags[$j];
                        $tags[$j] = $a;
                    }
                }
            }
            return $tags;
        }
}
