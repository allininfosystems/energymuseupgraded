<?php

/**
 * Class Magestore_Megamenu_Block_Megamenu
 */
class Magestore_Megamenu_Block_Megamenu extends Mage_Core_Block_Template
{

    /**
     * @return mixed
     */
    public function _prepareLayout()
    {
        return parent::_prepareLayout();
    }

    /**
     * @return mixed
     */
    public function getStoreId()
    {
        return Mage::app()->getStore()->getId();
    }

    /**
     * @return mixed
     */
    public function getTopMenuCollection()
    {
        $storeId = $this->getStoreId();
        $collection = Mage::getModel('megamenu/megamenu')->getCollection()
                ->addFieldToFilter('megamenu_type', Magestore_Megamenu_Model_Megamenu::TOP_MENU)
                ->addFieldToFilter('stores', array(array('finset' => $storeId), array('finset' => 0)))
                ->addFieldToFilter('status', Magestore_Megamenu_Model_Status::STATUS_ENABLED)
                ->setOrder('sort_order', 'ASC');
        return $collection;
    }

    /**
     * @return mixed
     */
    public function getLeftMenuCollection()
    {
        $storeId = $this->getStoreId();
        $collection = Mage::getModel('megamenu/megamenu')->getCollection()
                ->addFieldToFilter('megamenu_type', Magestore_Megamenu_Model_Megamenu::LEFT_MENU)
                ->addFieldToFilter('stores', array(array('finset' => $storeId), array('finset' => 0)))
                ->addFieldToFilter('status', Magestore_Megamenu_Model_Status::STATUS_ENABLED)
                ->setOrder('sort_order', 'ASC');
        return $collection;
    }

    /**
     * @param $item
     * @return mixed
     */
    public function getContent($item)
    {
        $cacheService = Mage::getSingleton('megamenu/service_cacheService');
        $storeId = $this->getStoreId();
        $staticBlock = Mage::getModel('cms/block')->load('mega_item_' . $item->getId() . '_' . $storeId, 'identifier');
        $processor = Mage::helper('cms')->getBlockTemplateProcessor();
        $cacheHit = Mage::getStoreConfig('megamenu/general/cache') ? true : false;
        $cacheHit = $cacheHit ? $cacheService->cacheHit($item) : false;
        
        if ($cacheHit) {
            return $processor->filter($staticBlock->getContent());
        } else {
            return $processor->filter($this->getLayout()->createBlock('megamenu/item')
                                    ->setStore($storeId)
                                    ->setItem($item)
                                    ->setTemplate('ms/megamenu/templates/item.phtml')
                                    ->toHtml());
        }
    }

    /**
     * @param $items
     * @return string
     */
    public function getSubMenuWidth($items)
    {
        $array = array();
        foreach ($items as $item) {
            if ($item->getMenuType() != Magestore_Megamenu_Model_Megamenu::ANCHOR_TEXT)
                $array[] = $item->getSubmenuWidth();
        }
        return json_encode($array);
    }

    /**
     * @return string
     */
    public function getEffect()
    {
        $storeId = $this->getStoreId();
        $array = array(Mage::getStoreConfig('megamenu/general/menu_effect', $storeId), Mage::getStoreConfig('megamenu/mobile_menu/mobile_effect', $storeId));
        return json_encode($array);
    }

    /**
     * @param $name
     * @return string
     */
    public function getClassMenuType($name)
    {
        $storeId = $this->getStoreId();
        if ($name == 'topmenu') {
            $menu_type = Mage::getStoreConfig('megamenu/top_menu/responsive', $storeId);
        } else {
            $menu_type = Mage::getStoreConfig('megamenu/left_menu/responsive', $storeId);
        }

        switch ($menu_type) {
            case Magestore_Megamenu_Model_Megamenu::NO_RESPONSIVE:
                return 'no-responsive';
            default:
                return '';
        }
    }
    
    /**
     * 
     * @param Magestore_Megamenu_Model_Megamenu $item
     * @return string
     */
    public function getItemLink($item)
    {
        if(strpos($item->getLink(), 'http://') !== false || strpos($item->getLink(), 'https://') !== false) {
            return $item->getLink();
        }
        return $this->getBaseUrl() . $item->getLink();
    }

}
