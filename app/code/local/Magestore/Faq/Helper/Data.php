<?php

class Magestore_Faq_Helper_Data extends Mage_Core_Helper_Abstract
{
	public static function getCategoryOptions1($store_id = null)
	{
		$options = array();
		$collection = Mage::getModel('faq/category')->getCollection()->setStoreId($store_id);	
		foreach($collection as $category)
		{
			$options[$category->getId()] = $category->getName();
		}
		return $options;
	}	
	
	public static function getCategoryOptions2($category_id, $store_id = null)
	{
		$options = array();
		$collection = Mage::getModel('faq/category')->getCollection()
								->setStoreId($store_id)
								;
		if(!$category_id)
			$collection->addFieldToFilter("status",1);	
								
		foreach($collection as $category)
		{
			$option = array();
			$option['label'] = $category->getName();
			$option['value'] = $category->getCategoryId();
			$options[] = $option;
		}
		
		return $options;
	}

	public function getOptionApplied()
	{
		return array(				
				array('value'=>1,'label'=>$this->__('Yes')),
				array('value'=>0,'label'=>$this->__('No')),
				
			);
	}
	
	public function getTablePrefix()
	{
		$tableName = Mage::getResourceModel('faq/faq')->getTable('faq');
		$prefix = substr($tableName,0,strlen($tableName)-3);		
		return $prefix;
	}
	
	public function normalizeUrlKey($urlKey)
	{
		$urlKeyEx = explode('.', $urlKey);		
        $end = end($urlKeyEx);
        if($end =='html' || $end=='htm'){
            unset($urlKeyEx[count($urlKeyEx)-1]);
            $urlKey = Mage::getModel('catalog/product_url')->formatUrlKey(implode('.', $urlKeyEx)).'.html';
        }else{
        $urlKey = Mage::getModel('catalog/product_url')->formatUrlKey(implode('.', $urlKeyEx));
        }
	
        $faq = Mage::getModel('faq/faq')->getCollection()->addFieldToFilter('url_key',$urlKey);
		$faqCategory = Mage::getModel('faq/category')->getCollection()->addFieldToFilter('url_key',$urlKey);
		if(count($faq) > 0|| count($faqCategory) > 0) 
		{
			//$faq = $faq->getFirstItem();
			// $url =  $faq->getUrlKey();
			$url =  $urlKey; 
            $explodeUrl = explode('.',$url);
            $before = '';
            $after = '';
            foreach ($explodeUrl as $key => $value) {
				if(sizeof($explodeUrl) == 1)
				{
					$before = $value;
				}
				else
				{
					if($key == (sizeof($explodeUrl) -2)){
						$after = $explodeUrl[sizeof($explodeUrl) -2];
					}
					if($key < (sizeof($explodeUrl) -3)){
						$before = $before.$value;
					}
				}
            }
            $result = $before.$after.'.html';
            return $result;
		} else {
            return $urlKey;			
            }
	}
    
	public function getFaqUrl()
	{
		$url = $this->_getUrl("faq", array());

		return $url;			
	}
	
	public function getStoreId()
	{		
		$store_id = Mage::app()->getStore()->getId();		
		
		return $store_id;
	}
	
	
	public function cloneFaqStoreData($faqStore)
	{
		
		$stores = Mage::app()->getStores(true);
		if(count($stores) > 1 && !$faqStore->getStoreId())
		{
			foreach($stores as $store)
			{
				$id = $faqStore->getFaqId();
				$store_id = $store->getStoreId();
				$faqStore_new = Mage::getModel("faq/faqstore")->loadByFaqIdStore($id,$store_id);
				$faqStore_new->setTitle($faqStore->getTitle());
				$faqStore_new->setCategoryId($faqStore->getCategoryId());
				$faqStore_new->setDescription($faqStore->getDescription());
				$faqStore_new->setStatus($faqStore->getStatus());
				$faqStore_new->setOrdering($faqStore->getOrdering());
				$faqStore_new->setUrlKey($faqStore->getUrlKey());
				$faqStore_new->setData("most_frequently",$faqStore->getData("most_frequently"));
				$faqStore_new->setStoreId($store_id);
				$faqStore_new->setFaqId($id);
				
				$faqStore_new->save();
				
				if($faqStore_new->getStatus() == 1)
				{
					$faqStore_new->updateUrlKey();		
				}
				else
				{
					$faqStore_new->deleteUrlKey();		
				}
			}
		}
		else
		{
			if(count($stores) == 1)
			{
				foreach($stores as $store)
				{
					$store_id = $store->getStoreId();
				}
			}
			$id = $faqStore->getFaqId();
			$store_id = $faqStore->getStoreId();
			$faqStore_new = Mage::getModel("faq/faqstore")->loadByFaqIdStore($id,$store_id);
			$faqStore_new->setTitle($faqStore->getTitle());
			$faqStore_new->setCategoryId($faqStore->getCategoryId());
			$faqStore_new->setDescription($faqStore->getDescription());
			$faqStore_new->setStatus($faqStore->getStatus());
			$faqStore_new->setOrdering($faqStore->getOrdering());
			$faqStore_new->setUrlKey($faqStore->getUrlKey());
			$faqStore_new->setData("most_frequently",$faqStore->getData("most_frequently"));
			$faqStore_new->setStoreId($store_id);
			$faqStore_new->setFaqId($id);
			
			$faqStore_new->save();
			
			if($faqStore_new->getStatus() == 1)
			{
				$faqStore_new->updateUrlKey();		
			}
			else
			{
				$faqStore_new->deleteUrlKey();		
			}
			// $faqStore->save();
			// if($faqStore->getStoreId())
			// {
				// if($faqStore->getStatus() == 1)
				// {
					// $faqStore->updateUrlKey();		
				// }
				// else
				// {
					// $faqStore->deleteUrlKey();		
				// }
			// }
		}		
	}
	
	public function cloneCategoryStoreData($categoryStore)
	{
		
		$stores = Mage::app()->getStores(true);
		if((count($stores) > 1) && !$categoryStore->getStoreId())
		{
			
			foreach($stores as $store)
			{
				$id = $categoryStore->getCategoryId();
				
				$store_id = $store->getStoreId();
				$categoryStore_new = Mage::getModel("faq/categorystore")->loadByCatIdStore($id,$store_id);
				$categoryStore_new->setName($categoryStore->getName());
				$categoryStore_new->setCategoryId($id);
				$categoryStore_new->setStoreId($store_id);
				$categoryStore_new->setDescription($categoryStore->getDescription());
				$categoryStore_new->setStatus($categoryStore->getStatus());
				$categoryStore_new->setOrdering($categoryStore->getOrdering());
				$categoryStore_new->setUrlKey($categoryStore->getUrlKey());
				
				$categoryStore_new->save();
				
				try
				{
					if($categoryStore_new->getStatus() == 1)
					{							
						$categoryStore_new->updateUrlKey();															
					}
					else
					{						
						$categoryStore_new->deleteUrlKey();		
					}
				}
				catch (Exception $e) {	}
			}
		}
		else
		{
			// $categoryStore->save();
			if(count($stores) == 1)
			{
				foreach($stores as $store)
				{
					$store_id = $store->getStoreId();
				}
			}
			$id = $categoryStore->getCategoryId();
				
			$store_id = $categoryStore->getStoreId();
			$categoryStore_new = Mage::getModel("faq/categorystore")->loadByCatIdStore($id,$store_id);
			$categoryStore_new->setName($categoryStore->getName());
			$categoryStore_new->setCategoryId($id);
			$categoryStore_new->setStoreId($store_id);
			$categoryStore_new->setDescription($categoryStore->getDescription());
			$categoryStore_new->setStatus($categoryStore->getStatus());
			$categoryStore_new->setOrdering($categoryStore->getOrdering());
			$categoryStore_new->setUrlKey($categoryStore->getUrlKey());
			
			$categoryStore_new->save();
			
			try
			{
				if($categoryStore_new->getStatus() == 1)
				{							
					$categoryStore_new->updateUrlKey();															
				}
				else
				{						
					$categoryStore_new->deleteUrlKey();		
				}
			}
			catch (Exception $e) {	}
			
			
			// if($categoryStore->getStoreId())
			// {
				// if($categoryStore->getStatus() == 1)
				// {
					// $categoryStore->updateUrlKey();		
				// }
				// else
				// {
					// $categoryStore->deleteUrlKey();		
				// }
			// }
		}		
	}

    //Duy Tuan - xu ly du lieu truoc khi luu
    public function parseTags($strtags){
        $tags = array();
        $temp = explode(',',$strtags);
        if(is_array($temp) && $temp!=null){
            foreach($temp as $tag){
                if(!in_array(trim($tag),$tags))
                $tags[] = trim($tag);
            }
        }
        return implode(', ',$tags);
    }
    
    //Duy Tuan
    public function getHtmlTags($storeId = null){
        $tags = Mage::getModel('faq/faq')->getCollection()->setStoreId($storeId)->addFieldToFilter('status',1)->getAllTags();
        if(!count($tags)) return;
        $_html = '';
        $_html .= '<'.'div id="box-tags" 
            style="display:none;float: left;
                    width: 100%;
                    margin-top: 11px;
                    border: 1px solid #c8c8c8;
                    padding: 15px;
                    box-sizing: border-box;
                    position: relative;">';
        foreach($tags as $key => $tag){
            $_html .= '<a style="
                        float: left;
                        color: #3e6d8e;
                        font-size: 12px;
                        white-space: nowrap;
                        background: #e4edf4;
                        border: 1px solid #e4edf4;
                        display: inline-block;
                        margin: 2px 2px 2px 0;
                        border-radius: 0;
                        -webkit-transition: color 0.15s ease, background 0.15s ease, border 0.15s ease;
                        -moz-transition: color 0.15s ease, background 0.15s ease, border 0.15s ease;
                        -ms-transition: color 0.15s ease, background 0.15s ease, border 0.15s ease;
                        -o-transition: color 0.15s ease, background 0.15s ease, border 0.15s ease;
                        line-height: 1;
                        text-decoration: none;
                        text-align: center;
                        padding: .4em .5em;
                        cursor: pointer;
                    
                    "'.' id=sug-'.$key.' class="post-tag" href="javascript:select('.$key.')" >'.$tag.'</a> ';
        }
        $_html .= '</br><'.'a style="  position: absolute;top: 5px;right: 5px;" id="close-box" href="javascript:void(0)" onclick="closeBox()" >[X]</a></div>' ;
        return $_html;
    }
	
	
}