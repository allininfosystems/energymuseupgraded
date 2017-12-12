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

class Magestore_Megamenu_Block_Adminhtml_Megamenu_Edit_Tab_Content extends Mage_Adminhtml_Block_Widget_Form {

    /**
     * Magestore_Megamenu_Block_Adminhtml_Megamenu_Edit_Tab_Content constructor.
     */
    public function __construct() {
        parent::__construct();
        $this->setTemplate('ms/megamenu/content.phtml');
    }

    /**
     * @return mixed
     */
    protected function _prepareLayout() {
        $this->setChild('load_button', $this->getLayout()->createBlock('adminhtml/widget_button')
                        ->setData(
                                array(
                                    'label' => Mage::helper('adminhtml')->__('Load Template'),
                                    'onclick' => 'templateControl.load();',
                                    'type' => 'button',
                                    'class' => 'save'
                                )
                        )
        );
      
        
        $this->setChild('header_and_footer', $this->getLayout()->createBlock('megamenu/adminhtml_megamenu_edit_tab_content_headerfooter')
        );


        $this->setChild('main_content', $this->getLayout()->createBlock('megamenu/adminhtml_megamenu_edit_tab_content_maincontent')
                        ->setData(
                                array(
                                    'label' => Mage::helper('adminhtml')->__('Main Content'),
                                    'onclick' => 'templateControl.load();',
                                    'type' => 'button',
                                    'class' => 'save'
                                )
                        )
        );

        $this->setChild('featured_item', $this->getLayout()->createBlock('megamenu/adminhtml_megamenu_edit_tab_content_featureditem')
                        ->setData(
                                array(
                                    'label' => Mage::helper('adminhtml')->__('Featured Item'),
                                    'onclick' => 'templateControl.load();',
                                    'type' => 'button',
                                    'class' => 'save'
                                )
                        )
        );

        return parent::_prepareLayout();
    }

    /**
     * @return mixed
     */
    public function getLoadButtonHtml() {
        return $this->getChildHtml('load_button');
    }

    /**
     * @return mixed
     */
    public function getLoadUrl() {
        return $this->getUrl('*/*/gettemplate');
    }

    /**
     * @return mixed
     */
    public function getSaveUrl() {
        return $this->getUrl('*/*/save');
    }

    /**
     * @return mixed
     */
    public function getFormData() {
        if (Mage::getSingleton('adminhtml/session')->getMegamenuData()) {
            $data = Mage::getSingleton('adminhtml/session')->getMegamenuData();
            Mage::getSingleton('adminhtml/session')->setMegamenuData(null);
        } elseif (Mage::registry('megamenu_data'))
            $data = Mage::registry('megamenu_data')->getData();
        return $data;
    }

}