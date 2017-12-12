<?php
/**
 * MageWorx
 * Abandoned Cart Recovery
 *
 * @category   MageWorx
 * @package    MageWorx_AbCart
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */

$installer = $this;

$installer->startSetup();

// 1.0.0
if (!$installer->getConnection()->tableColumnExists($installer->getTable('sales/quote'), 'abcart_status')) {
    $installer->getConnection()->addColumn(
        $installer->getTable('sales/quote'),
        'abcart_status',
        "VARCHAR (30) null"
    );
}
if (!$installer->getConnection()->tableColumnExists($installer->getTable('sales/quote'), 'device_type')) {
    $installer->getConnection()->addColumn(
        $installer->getTable('sales/quote'),
        'device_type',
        "VARCHAR (30) null"
    );
}

if ($installer->tableExists($this->getTable('abandoned_cart_log')) && !$installer->tableExists($this->getTable('mageworx_abcart/cart_log'))) {
    $installer->run("RENAME TABLE {$this->getTable('abandoned_cart_log')} TO {$this->getTable('mageworx_abcart/cart_log')};");
}
if ($installer->tableExists($this->getTable('abandoned_cart_alert_rule')) && !$installer->tableExists($this->getTable('mageworx_abcart/alert_rule'))) {
    $installer->run("RENAME TABLE {$this->getTable('abandoned_cart_alert_rule')} TO {$this->getTable('mageworx_abcart/alert_rule')};");
}
if ($installer->tableExists($this->getTable('abandoned_cart_alert')) && !$installer->tableExists($this->getTable('mageworx_abcart/alert'))) {
    $installer->run("RENAME TABLE {$this->getTable('abandoned_cart_alert')} TO {$this->getTable('mageworx_abcart/alert')};");
}

// 1.1.0
if (!$installer->getConnection()->tableColumnExists($installer->getTable('mageworx_abcart/alert_rule'), 'cancellation_event')) {
    $installer->getConnection()
        ->addColumn($installer->getTable('mageworx_abcart/alert_rule'), 'cancellation_event', array(
            'type' => Varien_Db_Ddl_Table::TYPE_INTEGER,
            'length' => 2,
            'nullable' => false,
            'default'  => 0,
            'comment' => 'Cancellation Event'
        ));
}

if (!$installer->getConnection()->tableColumnExists($installer->getTable('mageworx_abcart/alert'), 'token')) {
    $installer->getConnection()
        ->addColumn($installer->getTable('mageworx_abcart/alert'), 'token', array(
            'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
            'length' => 255,
            'nullable' => false,
            'default'  => '',
            'comment' => 'Token'
        ));
}

if ($installer->getConnection()->tableColumnExists($installer->getTable('mageworx_abcart/alert'), 'scheduled_at')) {
    $installer->getConnection()
        ->modifyColumn($installer->getTable('mageworx_abcart/alert'), 'scheduled_at', array(
            'type' => Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
            'nullable' => false,
        ))
        ->modifyColumn($installer->getTable('mageworx_abcart/alert'), 'executed_at', array(
            'type' => Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
            'nullable' => false,
        ));
}

if ($installer->getConnection()->tableColumnExists($installer->getTable('mageworx_abcart/cart_log'), 'changed_at')) {
    $installer->getConnection()
        ->modifyColumn($installer->getTable('mageworx_abcart/cart_log'), 'changed_at', array(
            'type' => Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
            'nullable' => false,
        ));
}

$installer->endSetup();