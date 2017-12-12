<?php
/**
 * MageWorx
 * Abandoned Cart Recovery
 *
 * @category   MageWorx
 * @package    MageWorx_AbCart
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_AbCart_Model_Cart_Log extends Mage_Core_Model_Abstract
{
    protected function _construct()
    {
        $this->_init('mageworx_abcart/cart_log');
    }

    /**
     * @return $this|Mage_Core_Model_Abstract
     */
    protected function _beforeSave()
    {
        parent::_beforeSave();
        $this->setData('changed_at', Mage::getSingleton('core/date')->gmtDate());
        return $this;
    }
}