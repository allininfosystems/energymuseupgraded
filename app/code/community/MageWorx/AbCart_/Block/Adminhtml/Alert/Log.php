<?php
/**
 * MageWorx
 * Abandoned Cart Recovery
 *
 * @category   MageWorx
 * @package    MageWorx_AbCart
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_AbCart_Block_Adminhtml_Alert_Log extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_controller = 'adminhtml_alert_log';
        $this->_blockGroup = 'mageworx_abcart';
        $this->_headerText = Mage::helper('mageworx_abcart')->__('Alerts Log');
        parent::__construct();

        $this->removeButton('add');
    }
}
