<?php
/**
 *
 *  Magestore
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magestore.com license that is
 * available through the world-wide-web at this URL:
 * http://www.magestore.com/license-agreement.html
 *
 * DISCLAIMER
 *
 *  Do not edit or add to this file if you wish to upgrade this extension to newer
 *  version in the future.
 *
 *  @category    Magestore
 *  @package     Magestore_Megamenu
 *  @module   Megamenu
 *  @author   Magestore Developer
 *
 *  @copyright   Copyright (c) 2016 Magestore (http://www.magestore.com/)
 *  @license     http://www.magestore.com/license-agreement.html
 */

class Magestore_Megamenu_Model_System_Config_Source_Effecttype

{
    /**
     *
     */
    const  Fade = 1;
    /**
     *
     */
    const  Slide = 2;
    /**
     *
     */
    const  Toggle = 3;

    /**
     * @return array
     */
    public function toOptionArray(){
        return array(
            array('value'=>self::Fade, 'label'=>'Fade'),
            array('value'=>self::Slide, 'label'=>'Slide'),
            array('value'=>self::Toggle, 'label'=>'Toggle')
        );
    }
}