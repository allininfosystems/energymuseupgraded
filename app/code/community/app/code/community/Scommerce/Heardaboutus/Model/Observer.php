<?php
class Scommerce_Heardaboutus_Model_Observer
{
	/**
    * @param Varien_Event_Observer $observer
    * @return Scommerce_Heardaboutus_Model_Observer
    */
	public function updateOrder(Varien_Event_Observer $observer)
    {
		$billing_data = Mage::app()->getRequest()->getPost('billing');
		
		if (Mage::helper('heardaboutus')->isLicenseValid()) {
			if (!empty($billing_data)){
			
				$heardaboutus = $billing_data['heardaboutus'];
				$heardaboutus_other = $billing_data['heardaboutusother'];
				if (strpos(strtolower($heardaboutus),"other")===false){
					$heardaboutus_other="";
				}
				if (isset($heardaboutus) && !empty($heardaboutus)){
					
					Mage::getSingleton('checkout/session')->setHeardAboutUs($heardaboutus);
					if (isset($heardaboutus_other) && !empty($heardaboutus_other)){
						$heardaboutus=$heardaboutus_other;
					}
					Mage::getSingleton('checkout/session')->setHeardAboutUsOther($heardaboutus_other);
					$observer->getEvent()->getQuote()->setHeardAboutUs((string)$heardaboutus);
				}
			}
			
			if ($heardaboutus = $observer->getEvent()->getQuote()->getHeardAboutUs()) {
				$observer->getEvent()->getOrder()
					->setHeardAboutUs((string)$heardaboutus);
			}
		}
        return $this;
    }
	
	/**
    * @param Varien_Event_Observer $observer
    * @return Scommerce_Heardaboutus_Model_Observer
    */
	public function updateCustomer(Varien_Event_Observer $observer)
    {
    	 $customer = $observer->getCustomer();
		 $heardaboutus = $customer->getHeardaboutus();
		 $heardaboutus_other = $customer->getHeardaboutusother();
		 
		 Mage::getSingleton('checkout/session')->setHeardAboutUs($heardaboutus);
		 Mage::getSingleton('checkout/session')->setHeardAboutUsOther($heardaboutus_other);
		 
		 return $this;
    }
	
    /**
    * @param Varien_Event_Observer $observer
    * @return Scommerce_Heardaboutus_Model_Observer
    */
    public function adminhtmlSalesOrderCreateProcessData(Varien_Event_Observer $observer) 
    {
		if (Mage::helper('heardaboutus')->isLicenseValid() && isset($requestData['order'])) {
			try {
				$requestData = $observer->getEvent()->getRequest();
				$heardaboutus = $requestData['order']['account']['heardaboutus'];
				$heardaboutus_other = $requestData['order']['account']['heardaboutusother'];
				$customer_id = $observer->getEvent()->getOrderCreateModel()->getQuote()->getCustomerId();
				if ($customer_id){
					$customer = Mage::getModel("customer/customer")->load($customer_id);
					if ($customer){
						$customer->setHeardaboutus((string)$heardaboutus)
								->setHeardaboutusother((string)$heardaboutus_other)
								->save();
					}
				}
				if (strpos(strtolower($heardaboutus),"other")===false){
					$heardaboutus_other="";
				}
				if (isset($heardaboutus) && !empty($heardaboutus)){
					if (isset($heardaboutus_other) && !empty($heardaboutus_other)){
						$heardaboutus=$heardaboutus_other;
					}
					$observer->getEvent()->getOrderCreateModel()->getQuote()
							->setHeardAboutUs((string)$heardaboutus)
							->save();
				}
				
			} catch (Exception $e) {
				Mage::logException($e);
			}
		}
        return $this;
    }
}