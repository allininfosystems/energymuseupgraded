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

class Magestore_Megamenu_Block_Adminhtml_Megamenu_Edit_Tab_Content_Maincontent_Grid extends Mage_Adminhtml_Block_Widget_Grid {


    /**
     * @var
     */
    protected $_selectedProducts;

    /**
     * Magestore_Megamenu_Block_Adminhtml_Megamenu_Edit_Tab_Content_Maincontent_Grid constructor.
     */
    public function __construct() {
		parent::__construct ();
		$this->setUseAjax ( true );
	}
	
        /**
     * Grid Row JS Callback
     *
     * @return string
     */
    public function getRowClickCallback()
    {
        $js = "  
            function (grid, event) {
                var trElement = Event.findElement(event, 'tr');
                var isInput = Event.element(event).tagName == 'INPUT';
                var input = $('".$this->getInput()."');
                if (trElement) {
                    var checkbox = Element.select(trElement, 'input');
                    if (checkbox[0]) {
                        var checked = isInput ? checkbox[0].checked : !checkbox[0].checked;
                        if(checked){
                            if(input.value == '')
                                input.value = checkbox[0].value;
                            else
                                input.value = input.value + ', '+checkbox[0].value;
                                
                        }else{
                            var vl = checkbox[0].value;
                            if(input.value.search(vl) == 0){
                                if(input.value == vl) input.value = '';
                                input.value = input.value.replace(vl+', ','');
                            }else{
                                input.value = input.value.replace(', '+ vl,'');
                            }
                        }
                        checkbox[0].checked =  checked;
                        grid.reloadParams['selected[]'] = input.value.split( ', ');
                    }
                }
            }
        ";
        return $js;
    }

    /**
     * @return string
     */
    public function getCheckboxCheckCallback(){
        $js = ' function (grid, element, checked) {
        var input = $("'.$this->getInput().'");
        if (checked) {
            $$("#'.$this->getId().' input[type=checkbox][class=checkbox]").each(function(e){
                if(e.name != "check_all"){
                    if(!e.checked){
                        if(input.value == "")
                            input.value = e.value;
                        else
                            input.value = input.value + ", "+e.value;
                        e.checked = true;
                        grid.reloadParams["selected[]"] = input.value.split(", ");
                    }
                }
            });
        }else{
            $$("#'.$this->getId().' input[type=checkbox][class=checkbox]").each(function(e){
                if(e.name != "check_all"){
                    if(e.checked){
                        var vl = e.value;
                        if(input.value.search(vl) == 0){
                            if(input.value == vl) input.value = "";
                            input.value = input.value.replace(vl+", ","");
                        }else{
                            input.value = input.value.replace(", "+ vl,"");
                        }
                        e.checked = false;
                        grid.reloadParams["selected[]"] = input.value.split(", ");
                    }
                }
            });
                            
        }
    } ';
        return $js;
    }

    /**
     * @return string
     */
    public function getRowInitCallback(){
        $js =' function (grid, row) {
            grid.reloadParams["selected[]"] = $("'.$this->getInput().'").value.split(", ");
        } ';
        return $js;
    }


    /**
     * @return mixed
     */
    protected function _prepareCollection() {
	$collection = Mage::getModel('catalog/product')->getCollection()->addAttributeToSelect('name')
            ->addAttributeToFilter('visibility', array('in'=>Mage::getSingleton('catalog/product_visibility')->getVisibleInSiteIds()))
            ->addAttributeToFilter('status', array('in'=>Mage::getSingleton('catalog/product_status')->getVisibleStatusIds()));
	$this->setCollection($collection);
	return parent::_prepareCollection();
    }

    /**
     * @return mixed
     */
    protected function _prepareColumns() {
        $this->addColumn('in_products', array(
            'header_css_class'  => 'a-center',
            'type'              => 'checkbox',
            'field_name'              => 'in_products[]',
            'values'            => $this->getRequest()->getParam('selected'),
            'align'             => 'center',	
            'index'             => 'entity_id'
        ));
                
        $this->addColumn('entity_id', array(
            'header'    => Mage::helper('catalog')->__('ID'),
            'sortable'  => true,
            'width'     => '60px',
            'index'     => 'entity_id'
        ));
        $this->addColumn('sku', array(
            'header' => Mage::helper('catalog')->__('SKU'),
            'align' => 'right',
            'column_css_class' => 'small_width',
            'index' => 'sku',
        ));
        $this->addColumn('name', array(
            'header' => Mage::helper('catalog')->__('Product Name'),
            'align' => 'right',
            'column_css_class' => 'small_width',
            'index' => 'name',
        ));
        return parent::_prepareColumns();
    }

    /**
     * @return mixed
     */
    public function getGridUrl(){
        return $this->getUrl('*/*/'.$this->getGridUrlCall(), array(
            '_current'          => true,
            'selected'   => $this->getRequest()->getParam('selected'),
            'collapse'          => null
        ));
    }
}

