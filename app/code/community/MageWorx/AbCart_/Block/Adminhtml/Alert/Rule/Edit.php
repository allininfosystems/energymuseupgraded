<?php
/**
 * MageWorx
 * Abandoned Cart Recovery
 *
 * @category   MageWorx
 * @package    MageWorx_AbCart
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_AbCart_Block_Adminhtml_Alert_Rule_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{

    public function __construct()
    {
        $this->_objectId = 'id';
        $this->_blockGroup = 'mageworx_abcart';
        $this->_controller = 'adminhtml_alert_rule';

        parent::__construct();

        $this->_updateButton('save', 'label', Mage::helper('salesrule')->__('Save Rule'));
        $this->_updateButton('delete', 'label', Mage::helper('salesrule')->__('Delete Rule'));

        $rule = $this->getRule();
    }

    /**
     * @return mixed
     */
    public function getRule(){
        return Mage::registry('current_alert_rule');
    }

    /**
     * @return string
     */
    public function getHeaderText()
    {
        $rule = $this->getRule();
        if ($rule->getRuleId()) {
            return Mage::helper('salesrule')->__("Edit Rule '%s'", $this->htmlEscape($rule->getName()));
        }
        else {
            return Mage::helper('salesrule')->__('New Rule');
        }
    }

    /**
     * @return string
     */
    public function getProductsJson()
    {
        return '{}';
    }
}
