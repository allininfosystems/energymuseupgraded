<?php

class Magestore_Faq_Model_Optionarray_Listposition {
    /**/

    public function toOptionArray() {
        $positionarray = array(
            'no-display' => Mage::helper('faq')->__('No'),
            'sidebar-right' => Mage::helper('faq')->__('Right sidebar'),
            'sidebar-left' => Mage::helper('faq')->__('Left sidebar'),
        );

        return $positionarray;
    }

}
