<?php
/**
 * MageWorx
 * Abandoned Cart Recovery
 *
 * @category   MageWorx
 * @package    MageWorx_AbCart
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_AbCart_Block_Adminhtml_Alert_Rule_Edit_Tab_General extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * @return Mage_Adminhtml_Block_Widget_Form
     */
    protected function _prepareForm()
    {
        $model = Mage::registry('current_alert_rule');

        $form = new Varien_Data_Form();
        $form->setHtmlIdPrefix('rule_');

        $fieldset = $form->addFieldset('base_fieldset', array('legend'=>Mage::helper('catalog')->__('General Information')));

        if ($model->getId()) {
            $fieldset->addField('rule_id', 'hidden', array(
                'name' => 'rule_id',
            ));
        };

        $fieldset->addField('name', 'text', array(
            'name' => 'name',
            'label' => Mage::helper('salesrule')->__('Rule Name'),
            'title' => Mage::helper('salesrule')->__('Rule Name'),
            'required' => true,
        ));

        if (!Mage::app()->isSingleStoreMode()) {
            $fieldset->addField('website_ids', 'multiselect', array(
                'name'      => 'website_ids[]',
                'label'     => Mage::helper('catalog')->__('Websites'),
                'title'     => Mage::helper('catalog')->__('Websites'),
                'required'  => true,
                'values'    => Mage::getSingleton('adminhtml/system_config_source_website')->toOptionArray(),
            ));
        }
        else {
            $model->setWebsiteIds(Mage::app()->getStore(true)->getId());
            $fieldset->addField('website_ids', 'hidden', array(
                'name'      => 'website_ids',
                'value'     => Mage::app()->getStore(true)->getId()
            ));
        }

        $fieldset->addField('customer_group_ids', 'multiselect', array(
            'name'      => 'customer_group_ids[]',
            'label'     => Mage::helper('salesrule')->__('Customer Groups'),
            'title'     => Mage::helper('salesrule')->__('Customer Groups'),
            'required'  => true,
            'values'    => Mage::getResourceModel('customer/group_collection')->toOptionArray(),
        ));

        $fieldset->addField('cancellation_event', 'select', array(
            'label'     => Mage::helper('mageworx_abcart')->__('Cancellation Event'),
            'title'     => Mage::helper('mageworx_abcart')->__('Cancellation Event'),
            'name'      => 'cancellation_event',
            'required'  => true,
            'options'   => Mage::getModel('mageworx_abcart/alert_rule')->getCancellationEventArray()
        ));

        $fieldset->addField('status', 'select', array(
            'label'     => Mage::helper('catalog')->__('Status'),
            'title'     => Mage::helper('catalog')->__('Status'),
            'name'      => 'status',
            'required'  => true,
            'options'   => Mage::getModel('mageworx_abcart/alert')->getAlertStatusArray()
        ));

        $form->setValues($model->getData());

        $this->setForm($form);

        return parent::_prepareForm();
    }
}
