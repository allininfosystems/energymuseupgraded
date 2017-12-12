<?php
/**
 * MageWorx
 * Abandoned Cart Recovery
 *
 * @category   MageWorx
 * @package    MageWorx_AbCart
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_AbCart_Block_Adminhtml_Report_Filter_Form_Date extends Mage_Adminhtml_Block_Report_Filter_Form
{
    /**
     * @return $this|Mage_Adminhtml_Block_Report_Filter_Form
     */
    protected function _prepareForm()
    {
        parent::_prepareForm();

        $fieldset = $this->getForm()->getElement('base_fieldset');

        if (is_object($fieldset) && $fieldset instanceof Varien_Data_Form_Element_Fieldset) {
            $fieldset->removeField('report_type');
            $fieldset->removeField('period_type');
            $fieldset->removeField('show_empty_rows');
        }

        return $this;
    }
}