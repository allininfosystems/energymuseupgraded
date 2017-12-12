<?php
class Magestore_Faq_Block_Tag extends Mage_Core_Block_Template
{
	public function _prepareLayout()
    {
		return parent::_prepareLayout();
    }

    public function getAllTags(){
        return Mage::getModel('faq/faq')->getCollection()
                ->setStoreId(Mage::app()->getStore()->getId())
                ->addFieldToFilter('status',1)
                ->getAllTags();
    }

}