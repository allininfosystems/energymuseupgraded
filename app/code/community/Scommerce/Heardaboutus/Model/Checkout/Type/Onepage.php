<?php

class Scommerce_Heardaboutus_Model_Checkout_Type_Onepage extends Mage_Checkout_Model_Type_Onepage
{
	public function saveBilling($data, $customerAddressId)
    {
		if (Mage::helper('heardaboutus')->isLicenseValid()) {
			$heardaboutus = $data['heardaboutus'];
			$heardaboutus_other = $data['heardaboutusother'];
			if (strpos(strtolower($heardaboutus),"other")===false){
				$heardaboutus_other="";
			}
			if (isset($heardaboutus) && !empty($heardaboutus)){
				
				$this->getCheckout()->setHeardAboutUs($heardaboutus);
				if (isset($heardaboutus_other) && !empty($heardaboutus_other)){
					$heardaboutus=$heardaboutus_other;
				}
				$this->getCheckout()->setHeardAboutUsOther($heardaboutus_other);
				$this->getQuote()->setHeardAboutUs((string)$heardaboutus);
				
			}
		}
        return parent::saveBilling($data, $customerAddressId);
    }
}
