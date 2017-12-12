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

class Magestore_Megamenu_Block_Adminhtml_Megamenu_Edit_Tab_Categories extends Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Categories
{
    /**
     * @var
     */
    protected $_categoryIds;
    /**
     * @var null
     */
    protected $_selectedNodes = null;

    /**
     * Magestore_Megamenu_Block_Adminhtml_Megamenu_Edit_Tab_Categories constructor.
     */
    public function __construct()
    {
        parent::__construct();
		$this->_withProductCount = true;
        $this->setTemplate('ms/megamenu/categories.phtml');
    }

    /**
     * @return Mage_Catalog_Model_Resource_Eav_Mysql4_Category_Collection
     */
    public function getCategoryCollection()
    {
        $storeId = $this->getRequest()->getParam('store', $this->_getDefaultStoreId());
        $collection = $this->getData('category_collection');
        if (is_null($collection)) {
            $collection = Mage::getModel('catalog/category')->getCollection();

            /* @var $collection Mage_Catalog_Model_Resource_Eav_Mysql4_Category_Collection */
            $collection->addAttributeToSelect('name')
                ->addAttributeToSelect('is_active')
                ->setProductStoreId($storeId)
                ->setLoadProductCount($this->_withProductCount)
                ->setStoreId($storeId);

            $this->setData('category_collection', $collection);
        }
        return $collection;
    }

    /**
     * Checks when this block is readonly
     *
     * @return boolean
     */
    public function isReadonly()
    {
        return false;
    }

    /**
     * @return array
     */
    protected function getCategoryIds()
    {
        return explode(',',$this->getIdsString());
    }

    /**
     * @return string
     */
    public function getIdsString()
    {
		if(Mage::registry('megamenu_categories')){
			return Mage::registry('megamenu_categories');
		}
        return '';
    }
}