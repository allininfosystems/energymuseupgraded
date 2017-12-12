<?php
$installer = $this;

$installer->startSetup();

//$setup = new Mage_Sales_Model_Resource_Setup();

$customer_setup =  new Mage_Customer_Model_Entity_Setup('customer_setup');

$attribute  = array(
	"type"     => "varchar",
    "backend"  => "",
    "label"    => "Where did you hear about us?",
    "input"    => "select",
    "source"   => "heardaboutus/source_heardaboutus",
    "visible"  => true,
    "required" => false,
    "default" => "",
    "frontend" => "",
    "unique"     => false,
    "note"       => ""
);

$customer_setup->addAttribute("customer", "heardaboutus", $attribute);

$attribute   = Mage::getSingleton("eav/config")->getAttribute("customer", "heardaboutus");

$used_in_forms=array();

$used_in_forms[]="adminhtml_customer";
$used_in_forms[]="checkout_register";
$used_in_forms[]="customer_account_create";
$used_in_forms[]="customer_account_edit";
$used_in_forms[]="adminhtml_checkout";
        $attribute->setData("used_in_forms", $used_in_forms)
		->setData("is_used_for_customer_segment", true)
		->setData("is_system", 0)
		->setData("is_user_defined", 1)
		->setData("is_visible", 1)
		->setData("sort_order", 100);
		
        $attribute->save();
        
        
$attribute  = array(
	"type"     => "varchar",
    "backend"  => "",
    "label"    => "Others",
    "input"    => "text",
    "source"   => "",
    "visible"  => true,
    "required" => false,
    "default" => "",
    "frontend" => "",
    "unique"     => false,
    "note"       => ""
);

$customer_setup->addAttribute("customer", "heardaboutusother", $attribute);

$attribute   = Mage::getSingleton("eav/config")->getAttribute("customer", "heardaboutusother");

$used_in_forms=array();

$used_in_forms[]="adminhtml_customer";
$used_in_forms[]="checkout_register";
$used_in_forms[]="customer_account_create";
$used_in_forms[]="customer_account_edit";
$used_in_forms[]="adminhtml_checkout";
        $attribute->setData("used_in_forms", $used_in_forms)
		->setData("is_used_for_customer_segment", true)
		->setData("is_system", 0)
		->setData("is_user_defined", 1)
		->setData("is_visible", 1)
		->setData("sort_order", 100);
		
        $attribute->save();
        	
$installer->endSetup();
 