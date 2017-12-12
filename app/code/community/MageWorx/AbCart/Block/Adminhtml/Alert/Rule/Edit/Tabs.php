<?php
/**
 * MageWorx
 * Abandoned Cart Recovery
 *
 * @category   MageWorx
 * @package    MageWorx_AbCart
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_AbCart_Block_Adminhtml_Alert_Rule_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('alert_rule_edit_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('mageworx_abcart')->__('Abandoned Carts Alert Rules'));
    }

    /**
     * @return Mage_Core_Block_Abstract
     */
    protected function _beforeToHtml()
    {
        $this->addTab('main_section', array(
            'label'     => Mage::helper('catalog')->__('General Information'),
            'content'   => $this->getLayout()->createBlock('mageworx_abcart/adminhtml_alert_rule_edit_tab_general')->toHtml(),
            'active'    => true
        ));

        $this->addTab('conditions_section', array(
            'label'     => Mage::helper('salesrule')->__('Conditions'),
            'content'   => $this->getLayout()->createBlock('mageworx_abcart/adminhtml_alert_rule_edit_tab_conditions')->toHtml(),
//            'url'   => $this->getUrl('*/*/getConditions', array('_current' => true)),
//            'class' => 'ajax',
        ));

        $this->addTab('actions_section', array(
            'label'     => Mage::helper('salesrule')->__('Email settings'),
            'content'   => $this->getLayout()->createBlock('mageworx_abcart/adminhtml_alert_rule_edit_tab_email')->toHtml(),
        ));

        return parent::_beforeToHtml();
    }

}
