<?php

class Magestore_Faq_Model_Observer{
    public function afterSaveStore($observer){
        $store=$observer->getEvent()->getStore();
        //Zend_Debug::dump($store->isObjectNew());die("1");
        if($store->isObjectNew()){
            $store_id=$store->getId();
            $cate_ids=Mage::getModel('faq/category')->getCollection()->getAllIds();
            //Zend_Debug::dump($cate_ids);die("1");
            if(count($cate_ids)){
                foreach ($cate_ids as $catId){
                    $categoryStore = Mage::getModel("faq/categorystore")->loadByCatIdStore($catId,0);
                    $catStore=Mage::getModel('faq/categorystore')->setData($categoryStore->getData())
                            ->setStoreId($store_id)
                            ->setId(null)
                            ->save();
                }
            }
            //return;
            $faq_ids=Mage::getModel('faq/faq')->getCollection()->getAllIds();
            //Zend_Debug::dump($faq_ids);die("1");
            if(count($faq_ids)){
                foreach ($faq_ids as $faqId){
                    $faqStoreDefault = Mage::getModel("faq/faqstore")->loadByFaqIdStore($faqId,0);
                    $faqStore=Mage::getModel('faq/faqstore')->setData($faqStoreDefault->getData())
                            ->setStoreId($store_id)
                            ->setId(null)
                            ->save();
            }
            }
	 $urlrewrite_ids=Mage::getModel("faq/faqstore")->getCollection()->getAllIds();
		   if(count($urlrewrite_ids))
		   {
                foreach ($urlrewrite_ids as $urlrewrite_id)
				{
                    $urlrewritedata = Mage::getModel("faq/faqstore")->loadByFaqIdStore($urlrewrite_id,0);
					$id=$urlrewritedata->getFaqId();
					if($id!=0){
					$url_key = $urlrewritedata->getData('url_key');
					$url_key .= ".html";
					$urlrewrite = Mage::getModel("faq/urlrewrite")->load("faq/" . $store_id . "/" . $id, "id_path");
					$product_id = Mage::getResourceModel("faq/faq")->getFirstProductId();
					$urlrewrite->setData("id_path", "faq/" . $store_id . "/" . $id);
					$urlrewrite->setData("store_id", $store_id);
					$urlrewrite->setData("is_system", 0);
					$urlrewrite->setData("request_path", $urlrewritedata->getData('url_key'));
					$urlrewrite->setData("target_path", 'faq/index/view/id/' . $id);
					if ($product_id) 
					{
						$urlrewrite->setData("product_id", $product_id);
					}
					try {
					$urlrewrite->save();
					} catch (Exception $e) {
						try {
							$temp = $urlrewritedata->getData('url_key');
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
                // Mage::getModel('faq/faq')->load($id)
                    // ->setUrlKey($temp)
                    // ->save();
                // $this->setUrlKey($temp)
                    // ->save();
				 } catch (Exception $e) {
					 Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				 }
					
				 }
			}
			}
		}
        }
    }
    public function showLinks($observer) {
        $store_Id = Mage::app()->getStore()->getId();
        if (Mage::getStoreConfig('faq/general/active',$store_Id)) {
            $block = $observer['block'];

            /* Show or hide the "check Gift Card" link */
            if (($block instanceof Mage_Page_Block_Template_Links) && Mage::getStoreConfig('faq/general/active',$store_Id)) {
                if (!Mage::registry('show_faq_link')) {
                    Mage::register('show_faq_link', 1);
                    $top_links = $block->getLayout()->getBlock('top.links');
                    if (isset($top_links) && $top_links != null)
                        $top_links->addLink('FAQ', Mage::getBaseUrl().'faq', 'FAQ', false, null, 10);
                }
            }
        }
    }
}