<?php
/**
 * MageWorx
 * Abandoned Cart Recovery
 *
 * @category   MageWorx
 * @package    MageWorx_AbCart
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_AbCart_Model_Resource_Alert extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init('mageworx_abcart/alert', 'alert_id');
    }
}