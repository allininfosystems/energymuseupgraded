<?php
$installer = $this;

$installer->startSetup();

$sales_setup =  new Mage_Sales_Model_Mysql4_Setup('sales_setup');

$attribute  = array(
	'type'          => 'varchar',
	'label'         => 'Where did you hear about us?',
	'default'       => '',
	'visible'       => false,
	'required'      => false,
	'user_defined'  => true,
	'searchable'    => false,
	'filterable'    => false,
	'comparable'    => false );

$installer->getConnection()->addColumn(
        $installer->getTable('sales_flat_quote'),
        'heard_about_us',
        'varchar(255) NULL DEFAULT NULL'
    );    

$sales_setup->addAttribute('quote', 'heard_about_us', $attribute);

$installer->getConnection()->addColumn(
        $installer->getTable('sales_flat_order'),
        'heard_about_us',
        'varchar(255) NULL DEFAULT NULL'
    );
	
$sales_setup->addAttribute('order', 'heard_about_us', $attribute);

$installer->endSetup();