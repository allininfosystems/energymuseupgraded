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

class Magestore_Megamenu_Model_System_Config_Source_Topmenutransform

{

    /**
     * list effect type for fontend
     * @return array
     */
    public function toOptionArray(){
        return array(
            array('value'=>'none', 'label'=>'Normal'),
            array('value'=>'uppercase', 'label'=>'Uppercase'),
            array('value'=>'lowercase', 'label'=>'Lowercase'),
            array('value'=>'capitalize', 'label'=>'Capitalize'),
        );
    }
}