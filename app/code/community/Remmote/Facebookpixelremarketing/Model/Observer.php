<?php
/**
 * @extension   Remmote_Facebookpixelremarketing
 * @author      Remmote    
 * @copyright   2016 - Remmote.com
 * @descripion  Facebook Pixel Events observer
 */
class Remmote_Facebookpixelremarketing_Model_Observer
{
    /**
     * Set a flag in session when a product is added to the cart
     * @param  [type]     $observer
     * @return [type]
     * @author edudeleon
     * @date   2016-10-12
     */
    public function logPixelAddToCart($observer) {
        //Logging event
        Mage::getModel('core/session')->setPixelAddToCart(true);

        //Logging product ID
        $product = $observer->getEvent()->getProduct();
        Mage::getModel('core/session')->setPixelAddToCartProductId($product->getId());
    }

    /**
     * Set a flag in session when a product is added to the wishlist
     * @param  [type]     $observer
     * @return [type]
     * @author edudeleon
     * @date   2016-10-12
     */
    public function logPixelAddToWishlist($observer) {
        //Logging event
        Mage::getModel('core/session')->setPixelAddToWishlist(true);

        //Logging product ID
        $product = $observer->getEvent()->getProduct();
        Mage::getModel('core/session')->setPixelAddToWishlistProductId($product->getId());
    }

    /**
     * Set a flag in session when a customer selects the payment method
     * @param  [type]     $observer
     * @return [type]
     * @author edudeleon
     * @date   2016-10-12
     */
    public function logPixelPaymentInfo($observer){
        //Logging event
        Mage::getModel('core/session')->setPixelPaymentInfo(true);
    }

    /**
     * Set a flag in session when a purchase is made
     * @param  [type]     $observer
     * @return [type]
     * @author edudeleon
     * @date   2016-10-12
     */
    public function logPixelPurchase($observer){
        //Logging event
        Mage::getModel('core/session')->setPixelPurchase(true);
    }

    /**
     * Set a flag in session when a customer creates an account
     * @param  [type]     $observer
     * @return [type]
     * @author edudeleon
     * @date   2016-10-12
     */
    public function logPixelCompleteRegistration($observer){
        //Logging event
        Mage::getModel('core/session')->setPixelCompleteRegistration(true);
    }

    /**
     * Set a flag in session when a customer signup to the newsletter
     * @param  [type]     $observer
     * @return [type]
     * @author edudeleon
     * @date   2016-10-12
     */
    public function logPixelCompleteRegistrationNewsletter($observer) {
        $subscriber     = $observer->getEvent()->getSubscriber();
        $statusChanged  = $subscriber->getIsStatusChanged();
        if($subscriber->getStatus() == Mage_Newsletter_Model_Subscriber::STATUS_SUBSCRIBED && $statusChanged) {
            Mage::getModel('core/session')->setPixelCompleteRegistration(true);

            //Set flag for Lead Event
            Mage::getModel('core/session')->setPixelLead(true);
        }
    }
}