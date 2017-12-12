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

class Magestore_Megamenu_Block_Adminhtml_Megamenu_Edit_Tab_Content_Featureditem_Products extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * Magestore_Megamenu_Block_Adminhtml_Megamenu_Edit_Tab_Content_Featureditem_Products constructor.
     */
    public function __construct(){
        parent::__construct();
        $this->setId('featuredproductGrid');
        $this->setDefaultSort('entity_id');
        $this->setDefaultDir('ASC');
        $this->setUseAjax(true);
        if ($this->getProgram() && $this->getProgram()->getId()){
        	$this->setDefaultFilter(array('in_products' => 1));
        }
    }

    /**
     * @param $column
     * @return $this
     */
    protected function _addColumnFilterToCollection($column){
    	if ($column->getId() == 'in_products'){
    		$productIds = $this->_getSelectedProducts();
    		if (empty($productIds)) $productIds = 0;
    		if ($column->getFilter()->getValue())
    			$this->getCollection()->addFieldToFilter('entity_id', array('in'=>$productIds));
    		elseif ($productIds)
    			$this->getCollection()->addFieldToFilter('entity_id', array('nin'=>$productIds));
    		return $this;
    	}
    	return parent::_addColumnFilterToCollection($column);
    }

    /**
     * @return mixed
     */
    protected function _prepareCollection(){
    	$collection = Mage::getModel('catalog/product')->getCollection()
    		->addAttributeToSelect('*');
    	
    	if ($storeId = $this->getRequest()->getParam('store', 0))
    		$collection->addStoreFilter($storeId);
    	
		$this->setCollection($collection);
		return parent::_prepareCollection();
    }

    /**
     *
     */
    protected function _prepareColumns(){
    	$this->addColumn('in_products', array(
            'header_css_class'  => 'a-center',
			'type'              => 'checkbox',
			'name'              => 'in_products',
			'values'            => $this->_getSelectedProducts(),
			'align'             => 'center',
			'index'             => 'entity_id',
			'use_index'			=> true,
        ));
 
		$this->addColumn('entity_id', array(
            'header'    => Mage::helper('catalog')->__('ID'),
            'sortable'  => true,
            'width'     => '60',
            'index'     => 'entity_id'
        ));
        
        $this->addColumn('product_name', array(
			'header'    => Mage::helper('catalog')->__('Name'),
			'align'     => 'left',
			'index'     => 'name',
		));
		
		$this->addColumn('product_status',array(
            'header'=> Mage::helper('catalog')->__('Status'),
            'width' => '90px',
            'index' => 'status',
            'type'  => 'options',
            'options' => Mage::getSingleton('catalog/product_status')->getOptionArray(),
        ));
        
        $this->addColumn('product_sku', array(
            'header'    => Mage::helper('catalog')->__('SKU'),
            'width'     => '80px',
            'index'     => 'sku'
        ));
		
        $this->addColumn('product_price', array(
            'header'    => Mage::helper('catalog')->__('Price'),
            'type'  	=> 'currency',
            'currency_code' => (string) Mage::getStoreConfig(Mage_Directory_Model_Currency::XML_PATH_CURRENCY_BASE),
            'index'     => 'price'
        ));
    }
    
 /*   public function getRowUrl($row){
		return $this->getUrl('adminhtml/catalog_product/edit', array(
			'id' 	=> $row->getId(),
			'store'	=>$this->getRequest()->getParam('store')
		));
	}*/

    /**
     * @return mixed
     */
    public function getGridUrl(){
        return $this->getUrl('*/*/featuredproductGrid',array(
        	'_current'	=>true,
        	'id'		=>$this->getRequest()->getParam('id'),
        	'store'		=>$this->getRequest()->getParam('store')
    	));
    }

    /**
     * @return array
     */
    protected function _getSelectedProducts(){
    	$products = $this->getProducts();
    	if (!is_array($products))
    		$products = array_keys($this->getSelectedRelatedProducts());
    	return $products;
    }

    /**
     * @return array
     */
    public function getSelectedRelatedProducts(){
    	$products = array();
    	$id = $this->getRequest()->getParam('id');
        if($id){
            $item = Mage::getModel('megamenu/megamenu')->load($id);
            $productIds = $item->getFeaturedProductIds();
            foreach ($productIds as $productId)
                $products[$productId] = array('position' => 0);
        }
    	return $products;
    }
	
  
	/**
	 * get currrent store
	 *
	 * @return Mage_Core_Model_Store
	 */
	public function getStore(){
		$storeId = (int) $this->getRequest()->getParam('store', 0);
		return Mage::app()->getStore($storeId);
	}
}