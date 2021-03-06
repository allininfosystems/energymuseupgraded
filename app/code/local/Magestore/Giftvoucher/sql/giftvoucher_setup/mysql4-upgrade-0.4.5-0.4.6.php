<?php
/**
 * Magestore
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magestore.com license that is
 * available through the world-wide-web at this URL:
 * http://www.magestore.com/license-agreement.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Magestore
 * @package     Magestore_Giftvoucher
 * @module     Giftvoucher
 * @author      Magestore Developer
 *
 * @copyright   Copyright (c) 2016 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 *
 */

$installer = Mage::getResourceModel('catalog/setup','catalog_setup');
/* @var $installer Mage_Catalog_Model_Resource_Eav_Mysql4_Setup */

$installer->startSetup();

$data = array();
$data[0]['template_name'] = Mage::helper('giftvoucher')->__('Amazon Gift Card Style');
$data[0]['style_color'] = '#DC8C71';
$data[0]['text_color'] = '#949392';
$data[0]['caption'] = Mage::helper('giftvoucher')->__('Gift Card');
$data[0]['notes'] = '';
$data[0]['images'] = 'default.png,giftcard_amazon_01.png,giftcard_amazon_02.png,giftcard_amazon_03.png,'
    . 'giftcard_amazon_04.png,giftcard_amazon_05.png,giftcard_amazon_06.png,giftcard_amazon_07.png,'
    . 'giftcard_amazon_08.png,giftcard_amazon_09.png,giftcard_amazon_10.png,giftcard_amazon_11.png,'
    . 'giftcard_amazon_12.png,giftcard_amazon_13.png,giftcard_amazon_14.png,giftcard_amazon_15.png,'
    . 'giftcard_amazon_16.png,giftcard_amazon_17.png,giftcard_amazon_18.png';
$data[0]['design_pattern'] = Magestore_Giftvoucher_Model_Designpattern::PATTERN_AMAZON;
$model = Mage::getModel('giftvoucher/gifttemplate');
foreach ($data as $template) {
    $model->setData($template);
    try {
        $model->save();
    } catch (Exception $exc) {

    }
}

$installer->run("

DROP TABLE IF EXISTS {$this->getTable('giftvoucher_giftcodeset')};

CREATE TABLE {$this->getTable('giftvoucher_giftcodeset')} (
  `set_id` int(11) unsigned NOT NULL auto_increment,
  `set_name` varchar(45) NOT NULL default '',
  `set_qty` int(11) NULL default 0,
  PRIMARY KEY (`set_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

    ");
$installer->getConnection()->addColumn(
    $this->getTable('giftvoucher/giftvoucher'), 'set_id', "int(11) NULL");
$installer->getConnection()->addColumn(
    $this->getTable('giftvoucher/giftvoucher'), 'used', "smallint(1) NULL");
$data = array(
    'group' => 'General',
    'type' => 'varchar',
    'input' => 'select',
    'label' => 'Select The Gift Code Sets ',
    'backend' => '',
    'frontend' => '',
    'source' => 'Magestore_Giftvoucher_Model_GiftCodeSetOptions',
    'visible' => 1,
    'required' => 0,
    'user_defined' => 1,
    'used_for_price_rules' => 1,
    'position' => 2,
    'unique' => 0,
    'default' => '',
    'sort_order' => 100,
    'apply_to' => 'giftvoucher',
    'is_global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
    'is_required' => 0,
    'is_configurable' => 1,
    'is_searchable' => 0,
    'is_visible_in_advanced_search' => 0,
    'is_comparable' => 0,
    'is_filterable' => 0,
    'is_filterable_in_search' => 1,
    'is_used_for_promo_rules' => 1,
    'is_html_allowed_on_front' => 0,
    'is_visible_on_front' => 0,
    'used_in_product_listing' => 1,
    'used_for_sort_by' => 0,
);
$installer->addAttribute('catalog_product', 'gift_code_sets', $data);

Mage::getConfig()->saveConfig('giftvoucher/email/show_only_simple_template', 0);
$installer->endSetup();
