<?php
/**
 * Mason Web Development
 *
 * @category    RichardMason
 * @package     RichardMason_Profile
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class RichardMason_Profile_Helper_Data extends Mage_Core_Helper_Abstract
{
	
	public function getText($category_id, $text){
		switch ($text) {
			case "New":
				switch ($category_id) {
					case 0:
						return $this->__('Add a Celeb');
					case 1:
						return $this->__('Add a Print Item');
					case 2:
						return $this->__('Add a Online Item');
					case 3:
						return $this->__('Add a Video Item');
						
				}
				break;
			case "_headerText":
				switch ($category_id) {
					case 0:
						return $this->__('Celebs Manager');
					case 1:
						return $this->__('Print Manager');
					case 2:
						return $this->__('Online Manager');
					case 3:
						return $this->__('Videos Manager');
						
				}
				break;
			case "content_heading":
				switch ($category_id) {
					case 0:
						return $this->__('Title');
					case 1:
						return $this->__('Title');
					case 2:
						return $this->__('Title');						
					case 3:
						return $this->__('Title');						
						
				}
				break;
			case "content":
				switch ($category_id) {
					case 0:
						return $this->__('Quote');
					case 1:
						return $this->__('Quote');						
					case 2:
						return $this->__('Quote');						
					case 3:
						return $this->__('Quote');						
						
				}
				break;
			case "thumbnail":
				switch ($category_id) {
					case 0:
						return $this->__('Thumbnail (Max. 100x130px)');
					case 1:
						return $this->__('Thumbnail (Max. 100x130px)');
					case 2:
						return $this->__('Thumbnail (Max. 100x130px)');
					case 3:
						return $this->__('Thumbnail (Max. 100x130px)');						
						
				}
				break;
		}
	}
}