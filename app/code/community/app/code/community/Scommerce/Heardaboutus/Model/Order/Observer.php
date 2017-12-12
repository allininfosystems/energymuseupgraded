<?php
class Scommerce_Heardaboutus_Model_Order_Observer
{
	public function saveOrder(Varien_Event_Observer $observer)
    {
		if (Mage::helper('heardaboutus')->isLicenseValid()) {
			$lastOrderId = Mage::getSingleton('checkout/session')->getLastOrderId();
			$heardaboutus = Mage::getSingleton('checkout/session')->getHeardAboutUs(); 
			$heardaboutus_other = Mage::getSingleton('checkout/session')->getHeardAboutUsOther();
			
			$order = Mage::getModel("sales/order")->load($lastOrderId);
			$customer_id = $order->getCustomerId();
			
			if (strlen($customer_id)){
				$customer = Mage::getModel("customer/customer")->load($customer_id);
				if ($customer){
					$customer->setHeardaboutus((string)$heardaboutus)
							->setHeardaboutusother((string)$heardaboutus_other)
							->save();
				}
			}
		}
		return $this;
    }
}