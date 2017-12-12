<?php
/**
 * MageWorx
 * Abandoned Cart Recovery
 *
 * @category   MageWorx
 * @package    MageWorx_AbCart
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_AbCart_Block_Adminhtml_Alert_Rule_Edit_Tab_Email extends Mage_Adminhtml_Block_Widget_Form
{
    const PRICE_RULES_NO_COUPON = 1;

    /**
     * @return Mage_Adminhtml_Block_Widget_Form
     */
    protected function _prepareForm()
    {
        $model = Mage::registry('current_alert_rule');

        $form = new Varien_Data_Form();
        $form->setHtmlIdPrefix('rule_');

        $fieldset = $form->addFieldset('email_fieldset', array('legend'=>Mage::helper('mageworx_abcart')->__('Email Settings')));

        if ($model->getId()) {
            $fieldset->addField('rule_id', 'hidden', array(
                'name' => 'rule_id',
            ));
        };

        $templates = Mage::getResourceModel('core/email_template_collection')->load();
        $fieldset->addField('email_template', 'select', array(
            'name'      => 'email_template',
            'label'     => Mage::helper('mageworx_abcart')->__('Email Template'),
            'title'     => Mage::helper('mageworx_abcart')->__('Email Template'),
            'required'  => true,
            'values'    => $templates->toOptionArray(),
        ));

        $fieldset->addField('time_delay', 'text', array(
            'name' => 'time_delay',
            'label' => Mage::helper('salesrule')->__('Send In (hours)'),
            'title' => Mage::helper('salesrule')->__('Send In (hours)'),
            'required' => true,
        ));

        $fieldset->addField('coupon_id', 'select', array(
            'name'      => 'coupon_id',
            'label'     => Mage::helper('mageworx_abcart')->__('Coupon'),
            'title'     => Mage::helper('mageworx_abcart')->__('Coupon'),
            'required'  => true,
            'values'    => $this->_getAvailableCoupons(),
        ));


        $form->setValues($model->getData());

        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * @return array
     */
    protected function _getAvailableCoupons()
    {
        $couponCollection = Mage::getResourceModel('salesrule/rule_collection');
        $coupons = array(
            0 => array(
                'value' => 0,
                'label' => Mage::helper('mageworx_abcart')->__('No Coupon')
            )
        );

        foreach ($couponCollection->getItems() as $item) {
            if ($item->getCouponType() != self::PRICE_RULES_NO_COUPON) {
                $coupons[] = array(
                    'value' => $item->getId(),
                    'label' => $item->getName()
                );
            }
        }

        return $coupons;
    }
}
