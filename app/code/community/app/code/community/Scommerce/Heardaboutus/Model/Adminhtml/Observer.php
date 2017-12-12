<?php
class Scommerce_Heardaboutus_Model_Adminhtml_Observer
{
	public function overrideTheme()
	{
		Mage::getDesign()->setArea('adminhtml')
			->setTheme((string)Mage::getStoreConfig('design/admin/theme'));
	}
}