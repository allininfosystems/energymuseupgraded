<?php
/**
 * MageWorx
 * Abandoned Cart Recovery
 *
 * @category   MageWorx
 * @package    MageWorx_AbCart
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_AbCart_Block_Adminhtml_Report_Column_Renderer_Percent extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    /**
     * @param Varien_Object $row
     * @return string
     */
    public function render(Varien_Object $row)
    {
        if (!$row->getStartedCarts()) {
            return 'n/a';
        }

        $percent = $row->getAbandonedCarts() / $row->getStartedCarts() * 100;
        return round($percent, 2) . '%';
    }
}