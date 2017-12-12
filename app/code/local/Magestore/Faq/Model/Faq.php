<?php

class Magestore_Faq_Model_Faq extends Mage_Core_Model_Abstract {

    public function _construct() {
        parent::_construct();
        $this->_init('faq/faq');
    }

    public function load($id, $field = null) {
        $storeId = $this->getStoreId();
        parent::load($id, $field);
        if ($storeId) {
            $this->loadStoreValue($storeId);
        }
        return $this;
    }
    public function loadStoreValue($storeId = null) {
        if (!$storeId)
            $storeId = $this->getStoreId(); //zend_debug::dump($storeId);die('22');
        if (!$storeId)
            return $this;

        $storeValues = Mage::getModel('faq/faqvalue')->getCollection()
                ->addFieldToFilter('faq_id', $this->getId())
                ->addFieldToFilter('store_id', $storeId);
        foreach ($storeValues as $value) {
            $this->setData($value->getAttributeCode() . '_in_store', true);
            $this->setData($value->getAttributeCode(), $value->getValue());
        }

        return $this;
    }

    public function updateUrlKey() {
        $id = $this->getId();
        $store_id = $this->getStoreId();
        if (!$store_id) {
            $store_id = 0;
        }

        $url_key = $this->getData('url_key');
        $url_key .= ".html";
        $urlrewrite = Mage::getModel("faq/urlrewrite")->load("faq/" . $store_id . "/" . $id, "id_path");
        $product_id = Mage::getResourceModel("faq/faq")->getFirstProductId();
        $urlrewrite->setData("id_path", "faq/" . $store_id . "/" . $id);
        $urlrewrite->setData("request_path", $this->getData('url_key'));
        $urlrewrite->setData("target_path", 'faq/index/index/id/' . $id);
        $urlrewrite->setData("product_id", $product_id);
        $urlrewrite->setData("store_id", $store_id);
        $urlrewrite->setData("is_system", 0);

        try {
            $urlrewrite->save();
        } catch (Exception $e) {
            try {
                $temp = $this->getData('url_key');
                $explodeUrl = explode('.', $temp);
                $before = '';
                $after = '';
                foreach ($explodeUrl as $key => $value) {
                    if ($key == (sizeof($explodeUrl) - 2)) {
                        $after = $explodeUrl[sizeof($explodeUrl) - 2];
                    }
                    if ($key < (sizeof($explodeUrl) - 3)) {
                        $before = $before . $value;
                    }
                }
                $temp = $before . $after . '-' . $id . '.html';
                $urlrewrite->setData("request_path", $temp);
                $urlrewrite->save();

                Mage::getModel('faq/faq')->load($id)
                        ->setUrlKey($temp)
                        ->save();
                $this->setUrlKey($temp)
                        ->save();
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
            //Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
        }
    }

    public function deleteUrlKey() {
        $id = $this->getId();
        $store_id = $this->getStoreId();
        if (!$store_id) {
            $store_id = 0;
        }
        $url_key = $this->getData('url_key');
        $urlrewrite = Mage::getModel("faq/urlrewrite")->load("faq/" . $store_id . "/" . $id, "id_path");

        try {
            $urlrewrite->delete();
        } catch (Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
        }
    }

    public function deleteAllUrlKey() {
        try {
            $id = $this->getId();
            $stores = Mage::app()->getStores(true);
            foreach ($stores as $store) {
                $urlrewrite = Mage::getModel("faq/urlrewrite")->load("faq/" . $store->getStoreId() . "/" . $id, "id_path");
                $urlrewrite->delete();
            }
        } catch (Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
        }
    }
    public function getFullUrlKey() {
        $store_id = $this->getStoreId() ? $this->getStoreId() : 0;
        $urlrewrite = Mage::getModel("faq/urlrewrite")->load("faq/" . $store_id . "/" . $this->getFaqId(), "id_path");
        return $urlrewrite->getData('request_path');
    }

}
