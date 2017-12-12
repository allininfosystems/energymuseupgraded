<?php
/**
 * MageWorx
 * Abandoned Cart Recovery
 *
 * @category   MageWorx
 * @package    MageWorx_AbCart
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_AbCart_Model_Device extends Mage_Core_Model_Abstract
{
    const DEVICE_TYPE_MOBILE  = 'mobile';
    const DEVICE_TYPE_TABLET  = 'tablet';
    const DEVICE_TYPE_DESKTOP = 'desktop';

    /**
     * @return string
     */
    public function getDeviceType()
    {
        $type = self::DEVICE_TYPE_DESKTOP;

        $device = Mage::helper('mageworx_abcart/mobiledetect');
        if ($device->isTablet()) {
            $type = self::DEVICE_TYPE_TABLET;
        } elseif($device->isMobile()) {
            $type = self::DEVICE_TYPE_MOBILE;
        }

        return $type;
    }

    /**
     * @param $device
     * @return string
     */
    public function getDeviceLabel($device)
    {
        switch ($device) {
            case self::DEVICE_TYPE_DESKTOP:
                $label = Mage::helper('mageworx_abcart')->__('Desktop');
                break;
            case self::DEVICE_TYPE_MOBILE:
                $label = Mage::helper('mageworx_abcart')->__('Mobile');
                break;
            case self::DEVICE_TYPE_TABLET:
                $label = Mage::helper('mageworx_abcart')->__('Tablet');
                break;
            default:
                $label = Mage::helper('mageworx_abcart')->__('n/a');
        }

        return $label;
    }
}