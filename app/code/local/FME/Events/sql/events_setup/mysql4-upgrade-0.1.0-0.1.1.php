<?php
/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();
$installer->getConnection()->dropTable($this->getTable('events/recurring'));
$eventsRecurring = $installer->getConnection()
        ->newTable($this->getTable('events/recurring'))
        ->addColumn(
            'item_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'unsigned' => true,
            'nullable' => false,
            'primary' => true,
            'identity' => true,
            ), 'table ID'
        )
        ->addColumn(
            'event_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'nullable' => false,
            ), 'Events id'
        )
        ->addColumn(
            'recurring_start_dates', Varien_Db_Ddl_Table::TYPE_DATETIME, null, array(
            'nullable' => false,
            ), 'Events calculate recurring start date for an event to use in calendar'
        )
        ->addColumn(
            'difference_days', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'nullable' => false,
            ), 'Events calculate difference of days between start and end date'
        )
        ->addForeignKey($installer->getFkName('events/recurring', 'event_id', 'events/events', 'event_id'), 'event_id', $installer->getTable('events/events'), 'event_id', Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
        ->setComment('Recurring table items linked to events table by event_id');
$installer->getConnection()->createTable($eventsRecurring);

$installer->getConnection()
        ->addColumn(
            $this->getTable('events/events'), 'is_recurring', array(
            'type' => Varien_Db_Ddl_Table::TYPE_SMALLINT,
            'nullable' => false,
            'comment' => 'Fme Events table item'
            )
        );

$installer->getConnection()
        ->addColumn(
            $this->getTable('events/events'), 'recurring_by', array(
            'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
            'nullable' => false,
            'comment' => 'Fme Events table item'
            )
        );

$installer->getConnection()
        ->addColumn(
            $this->getTable('events/events'), 'recurring_intervals', array(
            'type' => Varien_Db_Ddl_Table::TYPE_INTEGER,
            'nullable' => false,
            'comment' => 'Fme Events table item'
            )
        );

$installer->getConnection()
        ->addColumn(
            $this->getTable('events/events'), 'recurring_occurrences', array(
            'type' => Varien_Db_Ddl_Table::TYPE_INTEGER,
            'nullable' => false,
            'comment' => 'Fme Events table item'
            )
        );

$installer->endSetup();

