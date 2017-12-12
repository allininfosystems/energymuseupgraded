<?php
/**
 * MageWorx
 * Abandoned Cart Recovery
 *
 * @category   MageWorx
 * @package    MageWorx_AbCart
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_AbCart_Model_Alert extends Mage_Core_Model_Abstract
{
    const ALERT_STATUS_PENDING = 'pending';
    const ALERT_STATUS_DELETED = 'deleted';
    const ALERT_STATUS_SENT = 'sent';
    const ALERT_STATUS_ACTIVE = 1;
    const ALERT_STATUS_INACTIVE = 0;

    protected function _construct()
    {
        $this->_init('mageworx_abcart/alert');
    }

    /**
     * @param $quoteId
     * @param $ruleId
     * @return string
     */
    public function generateToken($quoteId, $ruleId) {
        $token = md5((string)$quoteId.$ruleId);
        return $token;
    }

    /**
     * @param bool $includePending
     * @return array
     */
    public function getStatusOptionArray($includePending = true)
    {
        $options = array(
            self::ALERT_STATUS_DELETED => Mage::helper('mageworx_abcart')->__('Deleted'),
            self::ALERT_STATUS_SENT => Mage::helper('mageworx_abcart')->__('Sent'),
        );

        if ($includePending) {
            $options[self::ALERT_STATUS_PENDING] = Mage::helper('catalog')->__('Pending');
        }

        return $options;
    }

    /**
     * @return $this
     */
    public function sendAlert()
    {
        $mailer = Mage::getModel('core/email_template_mailer');
        $emailInfo = Mage::getModel('core/email_info');
        $emailInfo->addTo($this->getCustomerEmail(), $this->getCustomerName());
        $mailer->addEmailInfo($emailInfo);

        $quote = Mage::getModel('mageworx_abcart/cart')->loadByIdWithoutStore($this->getQuoteId());

        if (!in_array($quote->getAbcartStatus(), $this->getSendStatuses())) {
            $this->setStatus(self::ALERT_STATUS_DELETED)->save();
            return $this;
        }

        $itemsBlock = Mage::app()->getLayout()->createBlock('mageworx_abcart/email_cart_items');
        $itemsBlock->setItems($quote->getAllVisibleItems());

        $couponAmount = 0;
        $expiresOn = '';
        $coupon = Mage::helper('mageworx_abcart')->getSalesRuleByCoupon($this->getCouponCode());
        if ($coupon && $coupon->getId() && $coupon->getRuleId()) {
            $rule = Mage::getModel('salesrule/rule')->load($coupon->getRuleId());
            if ($rule->getSimpleAction() == 'by_percent') {
                $couponAmount = round($rule->getDiscountAmount()) . '%';
            } else {
                $couponAmount = Mage::app()->getStore($this->getStoreId())->formatPrice($rule->getDiscountAmount(), false);
            }

            $expiresOn = date('m/d/Y', strtotime($coupon->getExpirationDate()));
        }

        // Set all required params and send emails
        $mailer->setSender(Mage::getStoreConfig(Mage_Sales_Model_Order::XML_PATH_EMAIL_IDENTITY, $quote->getStoreId()));
        $mailer->setStoreId($quote->getStoreId());
        $mailer->setTemplateId($this->getEmailTemplate());
        $mailer->setTemplateParams(array(
            'cart' => $this,
            'recovery_url' => Mage::app()->getStore($quote->getStoreId())->getUrl('abcart/recovery/recover', array('token' => $this->getToken())),
            'cart_items' => $itemsBlock->toHtml(),
            'coupon_code' => $this->getCouponCode(),
            'coupon_amount' => $couponAmount,
            'coupon_expiration' => $expiresOn,
            'store' => Mage::app()->getStore($quote->getStoreId())
        ));

        $mailer->send();

        $this->setStatus(self::ALERT_STATUS_SENT)
            ->setExecutedAt(Mage::getSingleton('core/date')->gmtDate())
            ->save();

        return $this;
    }

    /**
     * @param $token
     * @return Mage_Core_Model_Abstract
     */
    public function getQuoteByToken($token) {
        $alert = $this->getCollection()
            ->getItemsByColumnValue('token', $token);
        $quote = Mage::getModel('sales/quote')->load($alert[0]->getQuoteId());
        return $quote;
    }

    /**
     * @param $quoteId
     * @param $cancelationRuleId
     */
    public function deleteScheduledAlert($quoteId, $cancelationRuleId) {
        $alertCollection = Mage::getModel('mageworx_abcart/alert')->getCollection();
        foreach ($alertCollection as $alert) {
            if ($alert->getQuoteId() == $quoteId && $alert->getRuleId() > $cancelationRuleId) {
                $alert->setStatus(MageWorx_AbCart_Model_Alert::ALERT_STATUS_DELETED)->save();
            }
        }
    }

    /**
     * @return array
     */
    public function getSendStatuses()
    {
        return array(
            MageWorx_AbCart_Model_Cart::CART_STATUS_ABANDONED,
            MageWorx_AbCart_Model_Cart::CART_STATUS_ABANDONED_AGAIN
        );
    }

    /**
     * @return array
     */
    public function getAlertStatusArray() {
        return array(
            self::ALERT_STATUS_ACTIVE => Mage::helper('salesrule')->__('Active'),
            self::ALERT_STATUS_INACTIVE => Mage::helper('salesrule')->__('Inactive')
        );
    }
}