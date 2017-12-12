<?php

/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    FME
 * @package     FME_Events
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Image block
 *
 * @category   FME
 * @package    FME_Events
 * @author     Dr.Rao <mr.rao@zoho.com>
 */
class FME_Events_Block_Adminhtml_Events_Edit_Tab_Image extends Mage_Adminhtml_Block_Widget {

    protected $_uploaderType = 'uploader/multiple';
    protected function _prepareForm() {
        $data = $this->getRequest()->getPost(); //echo '<pre>';print_r($data);echo '</pre>';
        $form = new Varien_Data_Form();
        $form->setValues($data);
        $this->setForm($form);

        return parent::_prepareForm();
    }

    public function __construct() {
        parent::__construct();
        $this->setTemplate('events/edit/tab/image.phtml');
        $this->setId('media_gallery_content');
        $this->setHtmlId('media_gallery_content');
    }

    protected function _prepareLayout() {
        if(Mage::getVersion() >= '1.9.3'): 
         $this->setChild('uploader',
            $this->getLayout()->createBlock($this->_uploaderType)
        );

        $this->getUploader()->getUploaderConfig()
            ->setFileParameterName('image')
            ->setTarget(Mage::getModel('adminhtml/url')->addSessionParam()->getUrl('*/*/image'));

        $browseConfig = $this->getUploader()->getButtonConfig();
        $browseConfig
            ->setAttributes(array(
                'accept' => $browseConfig->getMimeTypesByExtensions('gif, png, jpeg, jpg')
            ));
        else:
            $this->setChild('uploader',
            $this->getLayout()->createBlock('adminhtml/media_uploader')
        );

        $this->getUploader()->getConfig()
            ->setUrl(Mage::getModel('adminhtml/url')->addSessionParam()->getUrl('*/*/image'))
            ->setFileField('image')
            ->setFilters(array(
                'images' => array(
                    'label' => Mage::helper('adminhtml')->__('Images (.gif, .jpg, .png)'),
                    'files' => array('*.gif', '*.jpg','*.jpeg', '*.png')
                )
            ));
        
         $this->setChild(
            'delete_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->addData(array(
                    'id'      => '{{id}}-delete',
                    'class'   => 'delete',
                    'type'    => 'button',
                    'label'   => Mage::helper('adminhtml')->__('Remove'),
                    'onclick' => $this->getJsObjectName() . '.removeFile(\'{{fileId}}\')'
                ))
        ); 
        endif;    

        return parent::_prepareLayout();
    }


    /**
     * Retrive uploader block
     *
     * @return Mage_Adminhtml_Block_Media_Uploader
     */
    public function getUploader() {
        return $this->getChild('uploader');
    }

    /**
     * Retrive uploader block html
     *
     * @return string
     */
    public function getUploaderHtml() {
        return $this->getChildHtml('uploader');
    }

    public function getJsObjectName() {
        return $this->getHtmlId() . 'JsObject';
    }

    public function getAddImagesButton() {
        return $this->getButtonHtml(
                        Mage::helper('catalog')->__('Add New Images'), $this->getJsObjectName() . '.showUploader()', 'add', $this->getHtmlId() . '_add_images_button'
        );
    }

    public function getImagesJson() {
        $_model = Mage::registry('events_data'); //echo '<pre>';print_r($_model);exit;
        $_data = $_model->getImagesAll(); //echo '<pre>';print_r($_data);exit;
        if (is_array($_data) and sizeof($_data) > 0) {
            $_result = array();
            foreach ($_data as &$_item) {
                $_result[] = array(
                    'value_id' => $_item['image_id'],
                    'url' => Mage::getSingleton('events/config')->getBaseMediaUrl() . $_item['image_file'],
                    'file' => $_item['image_file'],
                    'label' => $_item['image_name'],
                    'position' => $_item['image_order'],
                    'disabled' => $_item['image_status']);
            }
            return Zend_Json::encode($_result);
        }
        return '[]';
    }

    public function getImagesValuesJson() {
        $values = array();

        return Zend_Json::encode($values);
    }

    /**
     * Enter description here...
     *
     * @return array
     */
    public function getMediaAttributes() {
        
    }

    public function getImageTypes() {
        $type = array();
        $type['gallery']['label'] = "events";
        $type['gallery']['field'] = "events";

        $imageTypes = array();

        return $type;
    }

    public function getImageTypesJson() {
        return Zend_Json::encode($this->getImageTypes());
    }

    public function getCustomRemove() {
        return $this->setChild(
                        'delete_button', $this->getLayout()->createBlock('adminhtml/widget_button')
                                ->addData(array(
                                    'id' => '{{id}}-delete',
                                    'class' => 'delete',
                                    'type' => 'button',
                                    'label' => Mage::helper('adminhtml')->__('Remove'),
                                    'onclick' => $this->getJsObjectName() . '.removeFile(\'{{fileId}}\')'
                                ))
        );
    }

    public function getDeleteButtonHtml() {
        return $this->getChildHtml('delete_button');
    }

    public function getCustomValueId() {
        return $this->setChild(
                        'value_id', $this->getLayout()->createBlock('adminhtml/widget_button')
                                ->addData(array(
                                    'id' => '{{id}}-value',
                                    'class' => 'value_id',
                                    'type' => 'text',
                                    'label' => Mage::helper('adminhtml')->__('ValueId'),
                                ))
        );
    }

    public function getValueIdHtml() {
        return $this->getChildHtml('value_id');
    }

}