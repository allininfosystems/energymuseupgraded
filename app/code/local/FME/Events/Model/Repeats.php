<?php

class FME_Events_Model_Repeats extends Varien_Object
{

    const REPEATS_DAILY = 'Daily';
    const REPEATS_WEEKLY = 'Weekly';
    const REPEATS_MONTHLY = 'Monthly';
    const REPEATS_YEARLY = 'Yearly';

    // weekdays
    const REPEAT_SUNDAY = 'sunday';
    const REPEAT_MONDAY = 'monday';
    const REPEAT_TUESDAY = 'tuesday';
    const REPEAT_WEDNESDAY = 'wednesday';
    const REPEAT_THURSDAY = 'thursday';
    const REPEAT_FRIDAY = 'friday';
    const REPEAT_SATURDAY = 'saturday';
    
    static public function getOptionArray() 
    {
        return array(
            self::REPEATS_DAILY => Mage::helper('events')->__('Daily'),
            self::REPEATS_WEEKLY => Mage::helper('events')->__('Weekly'),
            self::REPEATS_MONTHLY => Mage::helper('events')->__('Monthly'),
            self::REPEATS_YEARLY => Mage::helper('events')->__('Yearly'),
        );
    }
    
    static public function getWeekDaysArray() 
    {
        return array(
            self::REPEAT_SUNDAY => Mage::helper('events')->__('Sunday'),
            self::REPEAT_MONDAY => Mage::helper('events')->__('Monday'),
            self::REPEAT_TUESDAY => Mage::helper('events')->__('Tuesday'),
            self::REPEAT_WEDNESDAY => Mage::helper('events')->__('Wednesday'),
            self::REPEAT_THURSDAY => Mage::helper('events')->__('Thursday'),
            self::REPEAT_FRIDAY => Mage::helper('events')->__('Friday'),
            self::REPEAT_SATURDAY => Mage::helper('events')->__('Saturday'),
        );
    }

}