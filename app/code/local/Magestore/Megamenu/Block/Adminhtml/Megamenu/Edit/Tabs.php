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

class Magestore_Megamenu_Block_Adminhtml_Megamenu_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    /**
     * Magestore_Megamenu_Block_Adminhtml_Megamenu_Edit_Tabs constructor.
     */
    public function __construct(){
		parent::__construct();
		$this->setId('megamenu_tabs');
		$this->setDestElementId('edit_form');
		$this->setTitle(Mage::helper('megamenu')->__('Menu Information'));
                
	}

    /**
     * @return mixed
     */
    protected function _beforeToHtml(){
		$this->addTab('form_section', array(
			'label'	 => Mage::helper('megamenu')->__('General Information'),
			'title'	 => Mage::helper('megamenu')->__('General    Information'),
			'content'	 => $this->getLayout()->createBlock('megamenu/adminhtml_megamenu_edit_tab_form')->toHtml(),
		));
        $this->addTab('content_section', array(
			'label'	 => Mage::helper('megamenu')->__('Content'),
			'title'	 => Mage::helper('megamenu')->__('Content'),
            'content'=> $this->getLayout()->createBlock('megamenu/adminhtml_megamenu_edit_tab_content')->toHtml(),
		));

		
		return parent::_beforeToHtml();
	}
}
