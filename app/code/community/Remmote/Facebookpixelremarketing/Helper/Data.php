<?php 
/**
 * @extension   Remmote_Facebookpixelremarketing
 * @author      Remmote    
 * @copyright   2016 - Remmote.com
 * @descripion  Helper
 */
class Remmote_Facebookpixelremarketing_Helper_Data extends Mage_Core_Helper_Abstract
{

    //Config paths
    const MODULE_ENABLED            = 'remmote_facebookpixelremarketing/general/enabled';
    const PIXEL_ID                  = 'remmote_facebookpixelremarketing/general/pixel_id';
    const VIEW_CONTENT              = 'remmote_facebookpixelremarketing/events/view_content';
    const SEARCH                    = 'remmote_facebookpixelremarketing/events/search';
    const ADD_TO_CART               = 'remmote_facebookpixelremarketing/events/add_to_cart';
    const ADD_TO_WISHLIST           = 'remmote_facebookpixelremarketing/events/add_to_wishlist';
    const ADD_PAYMENT_INFO          = 'remmote_facebookpixelremarketing/events/add_payment_info';
    const INITIATE_CHECKOUT         = 'remmote_facebookpixelremarketing/events/initiate_checkout';
    const PURCHASE                  = 'remmote_facebookpixelremarketing/events/purchase';
    const LEAD                      = 'remmote_facebookpixelremarketing/events/lead';
    const COMPLETE_REGISTRATION     = 'remmote_facebookpixelremarketing/events/complete_registration';
    
    /**
     * Check if module is enabled and Pixel ID is set
     * @param  [type]     $store
     * @return boolean
     * @author edudeleon
     * @date   2016-10-10
     */
	public function isEnabled($store = null)
    {
        $pixelId = $this->getPixelId($store);
        return $pixelId && Mage::getStoreConfig(self::MODULE_ENABLED, $store);
    }

    /**
     * Get Pixel ID
     * @param  [type]     $store
     * @return [type]
     * @author edudeleon
     * @date   2016-10-10
     */
	public function getPixelId($store = null)
    {
        return Mage::getStoreConfig(self::PIXEL_ID, $store);
    }

    /**
     * Check if viewContent event is enabled
     * @param  [type]     $store
     * @return [type]
     * @author edudeleon
     * @date   2016-10-11
     */
    public function viewContentEnabled($store = null){
        return Mage::getStoreConfig(self::VIEW_CONTENT, $store);
    }

    /**
     * Check if Search event is enabled
     * @param  [type]     $store
     * @return [type]
     * @author edudeleon
     * @date   2016-10-11
     */
    public function searchEnabled($store = null){
        return Mage::getStoreConfig(self::SEARCH, $store);
    }

    /**
     * Check if AddToCart event is enabled
     * @param  [type]     $store
     * @author edudeleon
     * @date   2016-10-11
     */
    public function addToCartEnabled($store = null){
        return Mage::getStoreConfig(self::ADD_TO_CART, $store);
    }

    /**
     * Check if AddToWhislist event is enabled
     * @param  [type]     $store
     * @author edudeleon
     * @date   2016-10-11
     */
    public function addToWhishlistEnabled($store = null){
        return Mage::getStoreConfig(self::ADD_TO_WISHLIST, $store);
    }

    /**
     * Check if AddPaymentInfo event is enabled
     * @param  [type]     $store
     * @author edudeleon
     * @date   2016-10-11
     */
    public function addPaymentInfoEnabled($store = null){
        return Mage::getStoreConfig(self::ADD_PAYMENT_INFO, $store);
    }

    /**
     * Check if InitiateCheckout event is enabled
     * @param  [type]     $store
     * @return [type]
     * @author edudeleon
     * @date   2016-10-11
     */
    public function initiateCheckoutEnabled($store = null){
        return Mage::getStoreConfig(self::INITIATE_CHECKOUT, $store);
    }

    /**
     * Check if Purchase event is enabled
     * @param  [type]     $store
     * @return [type]
     * @author edudeleon
     * @date   2016-10-11
     */
    public function purchaseEnabled($store = null){
        return Mage::getStoreConfig(self::PURCHASE, $store);
    }

    /**
     * check if Lead event is enabled
     * @param  [type]     $store
     * @return [type]
     * @author edudeleon
     * @date   2016-10-11
     */
    public function leadEnabled($store = null){
        return Mage::getStoreConfig(self::LEAD, $store);
    }

    /**
     * check if CompleteRegistration event is enabled
     * @param  [type]     $store
     * @return [type]
     * @author edudeleon
     * @date   2016-10-11
     */
    public function completeRegistrationEnabled($store = null){
        return Mage::getStoreConfig(self::COMPLETE_REGISTRATION, $store);
    }
}