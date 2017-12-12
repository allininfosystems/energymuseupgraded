<?php
class Magestore_Faq_Model_Config_Source_Style
{

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array('value' => 1, 'label'=>Mage::helper('adminhtml')->__('Expand')),
            array('value' => 0, 'label'=>Mage::helper('adminhtml')->__('Jump Down')),
        );
    }

}
