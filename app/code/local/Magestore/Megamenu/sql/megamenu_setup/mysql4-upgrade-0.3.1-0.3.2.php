<?php
/**
 *
 *  Magestore
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magestore.com license that is
 * available through the world-wide-web at this URL:
 * http://www.magestore.com/license-agreement.html
 *
 * DISCLAIMER
 *
 *  Do not edit or add to this file if you wish to upgrade this extension to newer
 *  version in the future.
 *
 *  @category    Magestore
 *  @package     Magestore_Megamenu
 *  @module   Megamenu
 *  @author   Magestore Developer
 *
 *  @copyright   Copyright (c) 2016 Magestore (http://www.magestore.com/)
 *  @license     http://www.magestore.com/license-agreement.html
 */

$installer = $this;

$installer->startSetup();
$installer->getConnection()->addColumn($installer->getTable('megamenu/megamenu'), 'product_label', 'text');
$installer->getConnection()->addColumn($installer->getTable('megamenu/megamenu'), 'product_label_color', 'text NOT NULL');
$installer->getConnection()->addColumn($installer->getTable('megamenu/megamenu'), 'products_using_label', 'text');
$installer->getConnection()->addColumn($installer->getTable('megamenu/megamenu'), 'featured_title', 'text');
$installer->getConnection()->addColumn($installer->getTable('megamenu/megamenu'), 'view_all', 'int(3) NOT NULL default "0"');
$installer->getConnection()->addColumn($installer->getTable('megamenu/megamenu'), 'number_products', 'int(11) NOT NULL default "0"');
$installer->endSetup();