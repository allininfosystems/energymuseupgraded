<?php
/**
 * MageWorx
 * Abandoned Cart Recovery
 *
 * @category   MageWorx
 * @package    MageWorx_AbCart
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_AbCart_Block_Adminhtml_Alert extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_controller = 'adminhtml_alert';
        $this->_blockGroup = 'mageworx_abcart';
        $this->_headerText = Mage::helper('salesrule')->__('Pending Alerts');
        parent::__construct();

        $this->removeButton('add');
    }
}
