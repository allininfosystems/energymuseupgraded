<?php
/**
 * MageWorx
 * Abandoned Cart Recovery
 *
 * @category   MageWorx
 * @package    MageWorx_AbCart
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_AbCart_RecoveryController extends Mage_Core_Controller_Front_Action
{
    protected function _getSession()
    {
        return Mage::getSingleton('checkout/session');
    }

    public function recoverAction()
    {
        $helper = Mage::helper('mageworx_abcart');
        try {
            $token = $this->getRequest()->getParam('token');
            $alertModel = Mage::getModel('mageworx_abcart/alert');
            $quote = $alertModel->getQuoteByToken($token);

            if ($helper->isEnabledLoginCustomer() && ($quote->getCustomerId() != null && $quote->getIsActive() !== 0)) {
                $session = Mage::getSingleton('customer/session');
                $customer = Mage::getModel('customer/customer')->load($quote->getCustomerId());
                $session->setCustomerAsLoggedIn($customer);
            }

            $abCart = Mage::getModel('mageworx_abcart/cart');
            if (!$quote || !$quote->getId() || !in_array($quote->getAbcartStatus(), $abCart->getAbandonedStatuses())) {
                throw new Exception($this->__('Quote cannot be recovered'));
            }

            $quote->setData('new_abcart_status', $abCart::CART_STATUS_LINK_CLICKED);
            $cancellationRuleId = Mage::getModel('mageworx_abcart/alert_rule')->getCancellationRuleId();
            if ($cancellationRuleId) {
                $alertModel->deleteScheduledAlert($quote->getId(), $cancellationRuleId);
            }
            $cart = Mage::getSingleton('checkout/cart');
            $cart->init()
                ->setQuote($quote)
                ->save();

            $this->_getSession()->addSuccess($this->__('Your cart was successfully recovered'));
        } catch (Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        }

        $this->_redirect('checkout/cart/index');
    }

    public function testAction()
    {
        Mage::getModel('mageworx_abcart/observer')->checkAbandonedCarts();
        Mage::getModel('mageworx_abcart/observer')->scheduleAlerts();
        Mage::getModel('mageworx_abcart/observer')->sendAlerts();
        exit('stop');
    }
}