<?php

class FME_Events_Block_Adminhtml_Renderer_Image extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{

    public function render(Varien_Object $row) 
    {
        $a = $this->__('No image');
        if ($row['event_thumb_image']) {
            $a = "<img src ='" . Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) . $row['event_thumb_image'] . "' height ='100px'/>";
        }

        return $a;
    }

}
