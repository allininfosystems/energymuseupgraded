<?php
class Magestore_Faq_IndexController extends Mage_Core_Controller_Front_Action
{
   
	
	public function indexAction()
    {   
        $store_Id = Mage::app()->getStore()->getId();
       
        if(Mage::getStoreConfig('faq/general/active',$store_Id)){
             $faqId =  $this->getRequest()->getParam('id');
            $meta_keywords = Mage::getStoreConfig('faq/general/meta_keywords',$store_Id);
            $meta_description = Mage::getStoreConfig('faq/general/meta_description',$store_Id);
            if($faqId){
                $faq = Mage::getModel("faq/faq")->setStoreId(Mage::app()->getStore()->getStoreId())->load($faqId);
                $meta_keywords = $faq->getMetakeyword();
                $meta_description = $faq->getMetadescription();
            }
            $this->loadLayout();
            $this->getLayout()->getBlock("head")->setKeywords($meta_keywords)->setDescription($meta_description);  
            $this->renderLayout();
        }else{
            $this->_redirectUrl('cms/index/noRoute');
        }
    }
      public function ajaxviewAction()
    {  		
	$block = Mage::getBlockSingleton('faq/list')->setTemplate('faq/list.phtml')->toHtml();
        $this->getResponse()->setBody(json_encode($block));  
    }
     public function viewAction()
    {  		
	 
    }
    public function resetupAction(){
       $installer =  new Mage_Core_Model_Resource_Setup();
        $installer->getConnection()->addColumn($installer->getTable('faq'), 'tag', 'varchar(255) NULL');
    }
}