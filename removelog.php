<?php
require_once './app/Mage.php';
umask(0);
Mage::app('default');
Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);

try {
	
	echo   $path= Mage::getBaseDir('log');
				$files = glob($path); // get all file names
				foreach($files as $file)
				{ 
				if(is_file($file))
					//unlink($file); // delete file
				}
   echo 'done';
} catch (Exception $e) {
   echo $e->getMessage();
}
?>