<?php
/**
 * MageWorx
 * Abandoned Cart Recovery
 *
 * @category   MageWorx
 * @package    MageWorx_AbCart
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_AbCart_Helper_Data extends Mage_Core_Helper_Abstract
{
    const XML_PATH_SEND_ENABLED = 'mageworx_abcart/main_settings/send_enabled';
    const XML_PATH_CART_LIFETIME = 'mageworx_abcart/main_settings/cart_lifetime';
    const XML_PATH_LOGIN_CUSTOMER = 'mageworx_abcart/main_settings/login_customer';
    const XML_PATH_FOREVER_ABANDONED_TIME = 'mageworx_abcart/main_settings/forever_abandoned_time';
    const XML_PATH_MAX_EMAIL_LATENESS = 'mageworx_abcart/main_settings/max_email_lateness';

    /**
     * @return bool
     */
    public function isAllowSendEmails()
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_SEND_ENABLED);
    }

    /**
     * @return mixed
     */
    public function getCartLifetime()
    {
        $lifetime = Mage::getStoreConfig(self::XML_PATH_CART_LIFETIME); // time in minutes
        return $lifetime * 60; // return in seconds
    }

    /**
     * @return bool
     */
    public function isEnabledLoginCustomer() {
        return Mage::getStoreConfigFlag(self::XML_PATH_LOGIN_CUSTOMER);
    }

    /**
     * @param $couponCode
     * @return bool
     */
    public function getSalesRuleByCoupon($couponCode)
    {
        if (!$couponCode) {
            return false;
        }

        $rulesCollection = Mage::getModel('salesrule/coupon')->getCollection();
        $rulesCollection->getSelect()->where('code = ?', $couponCode);

        return $rulesCollection->getFirstItem();
    }

    /**
     * @return mixed
     */
    public function getForeverAbandonedTime()
    {
        $time = Mage::getStoreConfig(self::XML_PATH_FOREVER_ABANDONED_TIME);
        return $time;
    }

    /**
     * @return mixed
     */
    public function getMaxEmailLateness()
    {
        $time = Mage::getStoreConfig(self::XML_PATH_MAX_EMAIL_LATENESS);
        return $time;
    }
}