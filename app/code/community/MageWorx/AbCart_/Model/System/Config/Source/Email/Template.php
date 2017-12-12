<?php
/**
 * MageWorx
 * Abandoned Cart Recovery
 *
 * @category   MageWorx
 * @package    MageWorx_AbCart
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_AbCart_Model_System_Config_Source_Email_Template extends Mage_Adminhtml_Model_System_Config_Source_Email_Template
{
    /**
     * Config xpath to email template node
     *
     */
    const TEMPLATE_NODE_PATTERN = '*[@module="mageworx_abcart"]';

    /**
     * Generate list of email templates
     *
     * @return array
     */
    public function toOptionArray()
    {
        $options = parent::toOptionArray();

        $allConfigTemplates = Mage::app()->getConfig()->getNode('global/template/email');
        foreach ($allConfigTemplates->asArray() as $id => $template) {
            if (strpos($id, 'mageworx_abcart_') === false) {
                continue;
            }
            $options[] = array('value' => $id, 'label' => $template['label']);
        }

        foreach ($options as $k => $_opt) {
            if (empty($_opt['value'])) {
                unset($options[$k]);
            }
        }

        return $options;
    }
}