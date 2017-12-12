<?php
class Magestore_Faq_Block_Sidebar extends Mage_Core_Block_Template
{
	protected function returnSidebar($sidebarposition)
	{       $store_Id = Mage::app()->getStore()->getId();
		$checkposition = Mage::getStoreConfig('faq/general/sidebar_position',$store_Id);
		$sidebar = $this->getLayout()->createBlock('faq/sidebar')->setTemplate('faq/sidebar.phtml')->renderView();
		if ($sidebarposition == $checkposition) return $sidebar;
	}
	
	protected function _toHtml()
    {
		return $this->returnSidebar($this->getSidebarPosition());
	}
	
        //Duy Tuan
	public function getMostFrequently()
	{   $store_Id = Mage::app()->getStore()->getId();
            $sidebarPosition = Mage::getStoreConfig('faq/general/sidebar_position',$store_Id);
            if(!strcmp($sidebarPosition,'sidebar-left')){
                  $page_size = Mage::getStoreConfig('faq/general/sidebar_question_number_left',$store_Id);
            }else{
                   $page_size = Mage::getStoreConfig('faq/general/sidebar_question_number_right',$store_Id);
            }
            if($page_size==0) $page_size =5;
            $most_frequently = Mage::getModel("faq/faq")->getCollection()
                ->setStoreId(Mage::app()->getStore()->getStoreId())
                ->addFieldToFilter('most_frequently',1)
                ->addFieldToFilter('status',1)
                ->setOrder('ordering', 'ASC')
                ->setOrder('title', 'ASC')
                ->setOrder('update_time', 'DESC');
            $most_frequently->setPageSize($page_size);
            return $most_frequently;
	}
                //phudu25
        public function getStoreId() {
        if (!$this->hasData('store_id')) {
            $store_id = Mage::app()->getStore()->getId();
            $this->setData('store_id', $store_id);
        }
        $categories = Mage::getModel("faq/category")
                ->setStoreId($this->getData('store_id'))
                ->getCollection()
        ;
        if($categories->getSize()==0){
            $this->setData('store_id', 0);
        }
        return $this->getData('store_id');
    }
}