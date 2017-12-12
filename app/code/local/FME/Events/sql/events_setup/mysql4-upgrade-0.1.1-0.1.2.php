<?php
/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

$installer->getConnection()
        ->addColumn(
            $this->getTable('events/events'), 'skip_date', array(
            'type' => Varien_Db_Ddl_Table::TYPE_DATE,
            'nullable' => false,
            'comment' => 'Fme Events table item'
            )
        );

$installer->getConnection()
        ->addColumn(
            $this->getTable('events/events'), 'recurring_on', array(
            'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
            'nullable' => false,
            'comment' => 'Fme Events table item'
            )
        );

// add new column to previous version recurring table
$installer->getConnection()
        ->addColumn(
            $this->getTable('events/recurring'), 'recurring_end_dates', array(
            'type' => Varien_Db_Ddl_Table::TYPE_DATETIME,
            'nullable' => false,
            'comment' => 'Fme Events recurring table item'
            )
        );

$installer->endSetup();

