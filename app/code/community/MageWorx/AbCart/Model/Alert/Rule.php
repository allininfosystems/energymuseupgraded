<?php
/**
 * MageWorx
 * Abandoned Cart Recovery
 *
 * @category   MageWorx
 * @package    MageWorx_AbCart
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_AbCart_Model_Alert_Rule extends Mage_SalesRule_Model_Rule
{
    const CANCELLATION_EVENT_ORDER_PLACED = 0;
    const CANCELLATION_EVENT_LINK_CLICKED = 1;

    protected $_eventPrefix = 'abcart_alert_rule';

    protected function _construct()
    {
        parent::_construct();
        $this->_init('mageworx_abcart/alert_rule');
    }

    /**
     * @return Object
     */
    public function getResourceCollection()
    {
        return Mage::getResourceModel('mageworx_abcart/alert_rule_collection');
    }

    /**
     * @return $this|Mage_Rule_Model_Abstract
     */
    protected function _beforeSave()
    {
        parent::_beforeSave();

        if (is_array($this->getWebsiteIds())) {
            $this->setWebsiteIds(implode(',', $this->getWebsiteIds()));
        }

        if (is_array($this->getCustomerGroupIds())) {
            $this->setCustomerGroupIds(implode(',', $this->getCustomerGroupIds()));
        }

        return $this;
    }

    /**
     * @param $quote
     * @return bool
     */
    public function canBeApplied($quote)
    {
        if (!$this->getStatus()) {
            return false;
        }

        $groups = explode(',', $this->getCustomerGroupIds());
        if (!in_array($quote->getCustomerGroupId(), $groups)) {
            return false;
        }

        $websites = explode(',', $this->getWebsiteIds());
        $quoteWebsite = Mage::app()->getStore($quote->getStoreId())->getWebsiteId();
        if (!in_array($quoteWebsite, $websites)) {
            return false;
        }

        $conditions = unserialize($this->getConditionsSerialized());
        if ($conditions) {
            $conditionModel = Mage::getModel($conditions['type'])->setPrefix('conditions')->loadArray($conditions);
            $result = $conditionModel->validate($quote->getShippingAddress());
            if (!$result) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param $quote
     * @return bool
     */
    public function isAlreadyApplied($quote)
    {
        $alerts = Mage::getModel('mageworx_abcart/alert')->getCollection();
        $alerts->getSelect()
            ->where('quote_id = ?', $quote->getEntityId())
            ->where('rule_id = ?', $this->getRuleId());

        if (count($alerts->getItems()) > 0) {
            return true;
        }

        return false;
    }

    /**
     * @return mixed
     */
    public function getCancellationRuleId()
    {
        $rule = $this->getCollection()
            ->addStatusFilter()
            ->addFieldToFilter('cancellation_event',
                array('eq' => self::CANCELLATION_EVENT_LINK_CLICKED))
            ->addExpressionFieldToSelect('rule_id','MIN({{rule_id}})', 'rule_id')->getFirstItem();
        return $rule->getRuleId();
    }

    /**
     * @return array
     */
    public function getCancellationEventArray() {
        return array(
            self::CANCELLATION_EVENT_ORDER_PLACED => Mage::helper('mageworx_abcart')->__('Order Placed'),
            self::CANCELLATION_EVENT_LINK_CLICKED => Mage::helper('mageworx_abcart')->__('Link Clicked'),
        );
    }
}