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

class Magestore_Megamenu_Model_Status extends Varien_Object
{
    /**
     *
     */
    const STATUS_ENABLED	= 1;
    const STATUS_DISABLED	= 2;

    /**
     * @return array
     */
    static public function getOptionArray(){
        return array(
            self::STATUS_ENABLED	=> Mage::helper('megamenu')->__('Enabled'),
            self::STATUS_DISABLED   => Mage::helper('megamenu')->__('Disabled')
        );
    }

    /**
     * @return array
     */
    static public function getOptionHash(){
        $options = array();
        foreach (self::getOptionArray() as $value => $label)
            $options[] = array(
                'value'	=> $value,
                'label'	=> $label
            );
        return $options;
    }

    /**
     * @return array
     */
    static public function getStyleShow(){
        /*$arr[] = array('value' => 0, 'label' => Mage::helper('megamenu')->__('Name only')); 
        $arr[] = array('value' => 1, 'label' => Mage::helper('megamenu')->__('Content'));
        $arr[] = array('value' => 2, 'label' => Mage::helper('megamenu')->__('Products'));
        $arr[] = array('value' => 3, 'label' => Mage::helper('megamenu')->__('Products & Content'));
        $arr[] = array('value' => 4, 'label' => Mage::helper('megamenu')->__('Categories'));*/
        $arr[] = array('value' => 1, 'label' => Mage::helper('megamenu')->__('Content only')); 
        $arr[] = array('value' => 2, 'label' => Mage::helper('megamenu')->__('Product Listing'));
        $arr[] = array('value' => 3, 'label' => Mage::helper('megamenu')->__('Category Listing'));
        $arr[] = array('value' => 4, 'label' => Mage::helper('megamenu')->__('Product & Category Listing'));
        $arr[] = array('value' => 5, 'label' => Mage::helper('megamenu')->__('Submit Form'));
        return $arr;
    }
}