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

class Magestore_Megamenu_Model_Service_CacheService
{
    /**
     * clean cache of mega menu items
     * 
     * @return Magestore_Megamenu_Model_Service_CacheService
     */
    public function clean()
    {
        Mage::app()->getCacheInstance()->cleanType('block_html');        
        $collection = Mage::getModel('megamenu/megamenu')->getCollection();
        foreach ($collection as $item) {
            $item->saveItem();
        }
        Mage::app()->getCacheInstance()->cleanType('block_html');

        return $this;
    }
    
    /**
     * 
     * @param Magestore_Megamenu_Model_Megamenu $item
     * @return bool
     */
    public function cacheHit($item)
    {
        if($item->getData('featured_type') == Magestore_Megamenu_Model_Megamenu::FEATURED_PRODUCTS) {
            return false;
        }
        return true;
    }
    
}