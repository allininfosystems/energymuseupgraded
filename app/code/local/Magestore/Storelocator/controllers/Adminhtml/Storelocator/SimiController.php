<?php 
class Magestore_Storelocator_Adminhtml_storelocator_SimiController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction(){
		$url = "https://www.simicart.com/features/store-locator.html/?utm_source=Magestore&utm_medium=related&utm_campaign=SLpage";
		Mage::app()->getResponse()->setRedirect($url)->sendResponse();
		exit();
	}
    protected function _isAllowed() {
        return Mage::getSingleton('admin/session')->isAllowed('storelocator');
    }
}