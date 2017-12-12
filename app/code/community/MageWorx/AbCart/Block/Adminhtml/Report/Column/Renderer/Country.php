<?php
/**
 * MageWorx
 * Abandoned Cart Recovery
 *
 * @category   MageWorx
 * @package    MageWorx_AbCart
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_AbCart_Block_Adminhtml_Report_Column_Renderer_Country extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    /**
     * @param Varien_Object $row
     * @return array|string
     */
    public function render(Varien_Object $row)
    {
        $countryCode = $row->getData($this->getColumn()->getIndex());

        if (empty($countryCode)) {
            return Mage::helper('mageworx_abcart')->__("n/a");
        }

        return Mage::app()->getLocale()->getCountryTranslation($countryCode);
    }
}