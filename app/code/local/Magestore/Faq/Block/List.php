<?php

class Magestore_Faq_Block_List extends Mage_Core_Block_Template {

    public function getFaqCollection() {
        $type = $this->getRequest()->getParam('faqType');
        $id = $this->getRequest()->getParam('faqId');
        $page = $this->getRequest()->getParam('page');
        $store_Id = Mage::app()->getStore()->getId();
        $page_size = Mage::getStoreConfig('faq/general/page_size_number', $store_Id);
        if (!$page_size)
            $page_size = 20;
        $faqId = $this->getRequest()->getParam('id');
        if ($type == 'category') {
            $collection = $this->getCategoryFaq($id);
        } elseif ($type == 'all') {
            $collection = $this->getAllFaq();
        } elseif ($type == 'tag') {
            $collection = $this->getTagFaq($id);
        } elseif ($type == 'search') {
            $collection = $this->getSearchResult($id);
        } elseif (!$type && $faqId) {
            $collection = $this->getViewFaq($faqId);
        } else {
            $collection = $this->getMostFrequently();
        }
        if (!$type && $faqId) {
            
        } else {
            $collection->setPageSize($page_size);
            if ($page)
                $collection->setCurPage($page);
        }
        return $collection;
    }

    public function getMostFrequently() {
        $most_frequently = Mage::getModel("faq/faq")->getCollection()
                ->setStoreId(Mage::app()->getStore()->getStoreId())
                ->addFieldToFilter('most_frequently', 1)
                ->addFieldToFilter('status', 1);
        $most_frequently->getSelect()->order('CAST(ordering AS SIGNED) ASC');
        $most_frequently->setOrder('title', 'ASC')
                ->setOrder('update_time', 'DESC');
        return $most_frequently;
    }

    public function getAllFaq() {
        $all = Mage::getModel("faq/faq")->getCollection()
                ->setStoreId(Mage::app()->getStore()->getStoreId())
                ->addFieldToFilter('status', 1);
        $all->getSelect()->order('CAST(ordering AS SIGNED) ASC');
        $all->setOrder('title', 'ASC')
                ->setOrder('update_time', 'DESC');
        return $all;
    }

    public function getCategoryFaq($category_id) {
        $category = Mage::getModel("faq/faq")->getCollection()
                ->setStoreId(Mage::app()->getStore()->getStoreId())
                ->addFieldToFilter('category_id', $category_id)
                ->addFieldToFilter('status', 1);
        $category->getSelect()->order('CAST(ordering AS SIGNED) ASC');
        $category->setOrder('title', 'ASC')
                ->setOrder('update_time', 'DESC');
        return $category;
    }

    public function getSearchResult($keyword) {
        $keyword = addslashes($keyword);
        $result = Mage::getModel("faq/faq")->getCollection()
                ->setStoreId(Mage::app()->getStore()->getStoreId())
                ->addFieldToFilter('status', 1);
        $result->getSelect()->order('CAST(ordering AS SIGNED) ASC');
        $result->setOrder('title', 'ASC')
                ->setOrder('update_time', 'DESC');
        $result->getSelect()->where("IF(faq_value_title.value IS NULL, main_table.title, faq_value_title.value) like '%$keyword%' or IF(faq_value_description.value IS NULL, main_table.description, faq_value_description.value)  like '%$keyword%'");
        return $result;
    }

    public function getTagFaq($tag) {
        $faq = Mage::getModel("faq/faq")->getCollection()
                ->setStoreId(Mage::app()->getStore()->getStoreId())
                ->addFieldToFilter('tag', array('like' => '%' . $tag . '%'))
                ->addFieldToFilter('status', 1);
        $faq->getSelect()->order('CAST(ordering AS SIGNED) ASC');
        $faq->setOrder('title', 'ASC')
                ->setOrder('update_time', 'DESC');
        return $faq;
    }

    public function getViewFaq($id) {
        $faq = Mage::getModel("faq/faq")->setStoreId(Mage::app()->getStore()->getStoreId())->load($id);
        return $faq;
    }

}
