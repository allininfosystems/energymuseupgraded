<?php
class Scommerce_Heardaboutus_Model_Source_Heardaboutus extends Mage_Eav_Model_Entity_Attribute_Source_Abstract
{
	public function getAllOptions()
	{
		$arr_data = array();
		if (Mage::helper('heardaboutus')->isLicenseValid()) {
			$options = explode(";",Mage::getStoreConfig("customer/heardaboutus/dropdown"));
			$intCtr = 0;
			
				
			foreach ($options as $option):
				array_push($arr_data,array("label" => Mage::helper("eav")->__($option),
								"value" =>  (string)$option));
			endforeach;
		}
		$this->_options = $arr_data;
		
	    return $this->_options;
	}
}