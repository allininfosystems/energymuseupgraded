<?php
/**
 * MageWorx
 * Abandoned Cart Recovery
 *
 * @category   MageWorx
 * @package    MageWorx_AbCart
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_AbCart_Model_Observer
{
    /**
     * @param $observer
     * @return $this
     */
    public function logStartedCart($observer)
    {
        $quote = $observer->getQuote();
        if (!$quote || !$quote->getId()) {
            return $this;
        }

        $newStatus = false;
        if ($quote->getIsActive() == false) {
            if ($quote->getAbcartStatus() == MageWorx_AbCart_Model_Cart::CART_STATUS_LINK_CLICKED) {
                $newStatus = MageWorx_AbCart_Model_Cart::CART_STATUS_RECOVERED;
            } elseif($quote->getAbcartStatus() == MageWorx_AbCart_Model_Cart::CART_STATUS_ABANDONED) {
                $newStatus = MageWorx_AbCart_Model_Cart::CART_STATUS_COMPLETED_MANUALLY;
            } else {
                $newStatus = MageWorx_AbCart_Model_Cart::CART_STATUS_COMPLETED;
            }
        } elseif ($quote->getNewAbcartStatus()) {
            $newStatus = $quote->getNewAbcartStatus();
        } elseif (!$quote->getAbcartStatus()) {
            $newStatus = MageWorx_AbCart_Model_Cart::CART_STATUS_ACTIVE;
        }

        if ($newStatus && $newStatus != $quote->getAbcartStatus()) {
            $quote->setAbcartStatus($newStatus);
            Mage::getModel('mageworx_abcart/cart')->logStatusChange($quote->getEntityId(), $newStatus);
        }

        if (!$quote->getDeviceType()) {
            $quote->setDeviceType(Mage::getModel('mageworx_abcart/device')->getDeviceType());
        }

        return $this;
    }

    /**
     * @param $observer
     * @return $this
     */
    public function addCartOverviewBlock($observer)
    {
        $block = $observer->getBlock();
        $transport = $observer->getTransport();
        if (!$block || !$transport || !($block instanceof Mage_Adminhtml_Block_Dashboard_Sales)) {
            return $this;
        }

        $html = $transport->getHtml();

        $html .= $block->getLayout()->createBlock('mageworx_abcart/adminhtml_dashboard_overview')->toHtml();

        $transport->setHtml($html);

        return $this;
    }

    /**
     * @param $observer
     * @return $this
     */
    public function addDashboardTab($observer)
    {
        $block = $observer->getBlock();
        if (!($block instanceof Mage_Adminhtml_Block_Dashboard_Diagrams)) {
            return $this;
        }

        $block->addTab('abandoned_carts', array(
            'label'     => Mage::helper('mageworx_abcart')->__('Abandoned Carts'),
            'content'   => $block->getLayout()->createBlock('mageworx_abcart/adminhtml_dashboard_tab_abandoned')->toHtml()
        ));

        return $this;
    }

    /**
     * @param $observer
     * @return $this
     */
    public function updateDashboardTab($observer)
    {
        $action = $observer->getControllerAction();
        if ($action->getRequest()->getParam('block', false) != 'tab_abandoned_carts') {
            return $this;
        }

        $output = $action->getLayout()->createBlock('mageworx_abcart/adminhtml_dashboard_tab_abandoned')->toHtml();
        $action->getResponse()->setBody($output);

        return $this;
    }

    /**
     * @return $this
     */
    public function checkAbandonedCarts()
    {
        $cartCollection = Mage::getModel('mageworx_abcart/cart')->getNewCardCollection();
        Mage::log('cartCollection count: ' . count($cartCollection), null, 'mylogfile.log');
        $cartCollection->setPageSize('100');
        $lifetime = Mage::helper('mageworx_abcart')->getCartLifetime();

        for ($page = 1; $page <= $cartCollection->getLastPageNumber(); $page++) {
            $cartCollection->setCurPage($page);
            foreach ($cartCollection->getItems() as $abCart) {
                $lastChange = strtotime(Mage::getSingleton('core/date')->gmtDate()) - strtotime($abCart->getUpdatedAt());
                if ($lastChange <= $lifetime || $abCart->getItemsQty() < 1) {
                    continue;
                }

                $newStatus = false;
                if ($abCart->getAbcartStatus() == MageWorx_AbCart_Model_Cart::CART_STATUS_ACTIVE) {
                    $newStatus = MageWorx_AbCart_Model_Cart::CART_STATUS_ABANDONED;
                } elseif($abCart->getAbcartStatus() == MageWorx_AbCart_Model_Cart::CART_STATUS_LINK_CLICKED) {
                    $newStatus = MageWorx_AbCart_Model_Cart::CART_STATUS_ABANDONED_AGAIN;
                } elseif($abCart->getAbcartStatus() == NULL) {
                    $newStatus = MageWorx_AbCart_Model_Cart::CART_STATUS_ABANDONED;
                } else {
                    continue;
                }

                if ($newStatus != $abCart->getAbcartStatus()) {
                    $abCart->setAbcartStatus($newStatus)->save();
                    Mage::getModel('mageworx_abcart/cart')->logStatusChange($abCart->getEntityId(), $newStatus);
                }
            }
        }

        return $this;
    }

    /**
     * @return $this
     */
    public function scheduleAlerts()
    {
        $abCarts = Mage::getModel('mageworx_abcart/cart')->getAbandonedCarts();
        $ruleModel = Mage::getModel('mageworx_abcart/alert_rule');
        $ruleItems = $ruleModel->getCollection()->addStatusFilter();

        foreach ($abCarts as $cart) {

            if (!$cart->getCustomerEmail()) {
                continue;
            }

            $abandonedAt = Mage::getSingleton('mageworx_abcart/cart')->getAbandonedAt($cart->getEntityId());
            foreach ($ruleItems as $rule) {
                if (!$rule->canBeApplied($cart) || $rule->isAlreadyApplied($cart)) {
                    continue;
                }
                $newAlert = Mage::getModel('mageworx_abcart/alert');
                $token = $newAlert->generateToken($cart->getEntityId(), $rule->getId());
                $newAlert->setQuoteId($cart->getEntityId())
                    ->setRuleId($rule->getId())
                    ->setEmailTemplate($rule->getEmailTemplate())
                    ->setStatus(MageWorx_AbCart_Model_Alert::ALERT_STATUS_PENDING)
                    ->setScheduledAt(new Zend_Db_Expr('FROM_UNIXTIME(UNIX_TIMESTAMP("'.$abandonedAt.'") + '.($rule->getTimeDelay()*3600).')'))
                    ->setToken($token)
                    ->save();

            }
        }
        return $this;
    }

    /**
     * @return $this
     */
    public function sendAlerts()
    {
        if (!Mage::helper('mageworx_abcart')->isAllowSendEmails()) {
            return $this;
        }

        $time =  Mage::helper('mageworx_abcart')->getMaxEmailLateness();
        $collection = Mage::getModel('mageworx_abcart/alert')->getCollection();
        $collection->addStatusFilter(MageWorx_AbCart_Model_Alert::ALERT_STATUS_PENDING)
            ->addFieldToFilter(
                'scheduled_at',
                array(
                    'gt' =>  date("Y-m-d H:i:s", strtotime('-'.$time.' days', strtotime('now')))
                )
            )
            ->addQuoteData()
            ->addRuleData()
            ->addCouponData()
            ->addAlertsToSend();
        foreach ($collection->getItems() as $item) {
            $item->sendAlert();
        }

        return $this;
    }

    /**
     * @param $observer
     */
    public function clearQuotesBefore($observer)
    {
        $fields = array(
            'updated_at' => array('to'=>date("Y-m-d", time()- (3600 * 24 * 365 * 3)))
        );

        $observer->getSalesObserver()->getExpireQuotesAdditionalFilterFields($fields);
    }
}