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

if ($installer->getConnection()->showTableStatus($installer->getTable('mageworx_abcart/cart_log')) === false) {
    $table = $installer->getConnection()
        ->newTable($installer->getTable('mageworx_abcart/cart_log'))
        ->addColumn('log_id', Varien_Db_Ddl_Table::TYPE_INTEGER, 10, array(
            'identity'  => true,
            'unsigned'  => true,
            'nullable'  => false,
            'primary'   => true,
        ), 'Id')
        ->addColumn('quote_id', Varien_Db_Ddl_Table::TYPE_INTEGER, 15, array(
            'nullable'  => true,
        ), 'Quote Id')
        ->addColumn('status', Varien_Db_Ddl_Table::TYPE_VARCHAR, 30, array(
            'nullable'  => false,
            'default'   => '',
        ), 'Status')
        ->addColumn('changed_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
            'nullable'  => false,
            'default'   => '',
        ), 'Changed At');

    $installer->getConnection()->createTable($table);
}

if ($installer->getConnection()->showTableStatus($installer->getTable('mageworx_abcart/alert_rule')) === false) {
    $table = $installer->getConnection()
        ->newTable($installer->getTable('mageworx_abcart/alert_rule'))
        ->addColumn('rule_id', Varien_Db_Ddl_Table::TYPE_INTEGER, 5, array(
            'identity'  => true,
            'unsigned'  => true,
            'nullable'  => false,
            'primary'   => true,
        ), 'Id')
        ->addColumn('name', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
            'nullable'  => false,
            'default'   => ''
        ), 'Name')
        ->addColumn('website_ids', Varien_Db_Ddl_Table::TYPE_VARCHAR, 100, array(
            'nullable'  => false,
            'default'   => '',
        ), 'WebsiteIds')
        ->addColumn('customer_group_ids', Varien_Db_Ddl_Table::TYPE_VARCHAR, 100, array(
            'nullable'  => false,
            'default'   => '',
        ), 'Customer Group Ids')
        ->addColumn('status', Varien_Db_Ddl_Table::TYPE_INTEGER, 2, array(
            'nullable'  => false,
            'default'   => 0,
        ), 'Status')
        ->addColumn('conditions_serialized', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
            'nullable'  => false,
            'default'   => '',
        ), 'Conditions Serialized')
        ->addColumn('email_template', Varien_Db_Ddl_Table::TYPE_VARCHAR, 150, array(
            'nullable'  => false,
            'default'   => '',
        ), 'Email Template')
        ->addColumn('time_delay', Varien_Db_Ddl_Table::TYPE_VARCHAR, 15, array(
            'nullable'  => false,
            'default'   => '',
        ), 'Time Delay')
        ->addColumn('coupon_id', Varien_Db_Ddl_Table::TYPE_VARCHAR, 10, array(
            'nullable'  => false,
            'default'   => '',
        ), 'Coupon Id')
        ->addColumn('cancellation_event', Varien_Db_Ddl_Table::TYPE_INTEGER, 2, array(
            'nullable' => false,
            'default'  => 0,
        ), 'Cancellation Event');

    $installer->getConnection()->createTable($table);
}

if ($installer->getConnection()->showTableStatus($installer->getTable('mageworx_abcart/alert')) === false) {
    $table = $installer->getConnection()
        ->newTable($installer->getTable('mageworx_abcart/alert'))
        ->addColumn('alert_id', Varien_Db_Ddl_Table::TYPE_INTEGER, 10, array(
            'identity'  => true,
            'unsigned'  => true,
            'nullable'  => false,
            'primary'   => true,
        ), 'Id')
        ->addColumn('quote_id', Varien_Db_Ddl_Table::TYPE_INTEGER, 15, array(
            'nullable'  => false,
        ), 'Quote Id')
        ->addColumn('rule_id', Varien_Db_Ddl_Table::TYPE_INTEGER, 15, array(
            'nullable'  => false,
        ), 'Rule Id')
        ->addColumn('email_template', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
            'nullable'  => false,
            'default'   => '',
        ), 'Email Template')
        ->addColumn('status', Varien_Db_Ddl_Table::TYPE_VARCHAR, 30, array(
            'nullable'  => false,
            'default'   => '',
        ), 'Status')
        ->addColumn('scheduled_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
            'nullable'  => false,
            'default'   => '',
        ), 'Scheduled At')
        ->addColumn('executed_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
            'nullable'  => false,
            'default'   => '',
        ), 'Executed At')
        ->addColumn('token', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
            'nullable' => false,
            'default'  => '',
        ), 'Token');

    $installer->getConnection()->createTable($table);
}

$installer->endSetup();