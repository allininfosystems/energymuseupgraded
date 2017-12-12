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

class Magestore_Megamenu_Block_Adminhtml_Template_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    /**
     * Magestore_Megamenu_Block_Adminhtml_Template_Edit constructor.
     */
    public function __construct(){

            parent::__construct();

            $this->_objectId = 'id';
            $this->_blockGroup = 'megamenu';
            $this->_controller = 'adminhtml_template';

            $this->_updateButton('save', 'label', Mage::helper('megamenu')->__('Save Template'));
            $this->_updateButton('delete', 'label', Mage::helper('megamenu')->__('Delete Template'));

            $this->_addButton('saveandcontinue', array(
                    'label'		=> Mage::helper('adminhtml')->__('Save And Continue Edit'),
                    'onclick'	=> 'saveAndContinueEdit()',
                    'class'		=> 'save',
            ), -100);

            $this->_formScripts[] = "
                    function toggleEditor() {
                            if (tinyMCE.getInstanceById('megamenu_content') == null)
                                    tinyMCE.execCommand('mceAddControl', false, 'megamenu_content');
                            else
                                    tinyMCE.execCommand('mceRemoveControl', false, 'megamenu_content');
                    }

                    function saveAndContinueEdit(){
                            editForm.submit($('edit_form').action+'back/edit/');
                    }
            ";
    }

    /**
     * @return mixed
     */
    public function getHeaderText(){
            if(Mage::registry('template_data') && Mage::registry('template_data')->getId())
                    return Mage::helper('megamenu')->__("Edit Template");
            return Mage::helper('megamenu')->__('Add Template');
    }
}