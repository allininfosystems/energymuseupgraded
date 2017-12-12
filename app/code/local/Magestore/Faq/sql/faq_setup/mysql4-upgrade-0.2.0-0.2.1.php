<?php

$installer = $this;

$installer->startSetup();
$installer->run("
	DROP TABLE IF EXISTS {$this->getTable('faq_store')};
	DROP TABLE IF EXISTS {$this->getTable('faq_category_store')};
	DROP TABLE IF EXISTS {$this->getTable('faq_value')};
	CREATE TABLE {$this->getTable('faq_value')} (
	  `faq_value_id` int(11) unsigned NOT NULL auto_increment,
	  `faq_id` int(11) unsigned NOT NULL,
	  `store_id` smallint(5) unsigned NOT NULL,
	  `attribute_code` varchar(255) NOT NULL default '',
	  `value` text NOT NULL,
	  INDEX(`faq_id`),
	  INDEX(`store_id`),

	  FOREIGN KEY (`faq_id`) REFERENCES {$this->getTable('faq')} (`faq_id`) ON DELETE CASCADE ON UPDATE CASCADE,
	  FOREIGN KEY (`store_id`) REFERENCES {$this->getTable('core/store')} (`store_id`) ON DELETE CASCADE ON UPDATE CASCADE,
	  PRIMARY KEY (`faq_value_id`)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8;
	DROP TABLE IF EXISTS {$this->getTable('faq_category_value')};
	CREATE TABLE {$this->getTable('faq_category_value')} (
	  `category_value_id` int(11) unsigned NOT NULL auto_increment,
	  `category_id` int(11) unsigned NOT NULL,
	  `store_id` smallint(5) unsigned NOT NULL,
	  `attribute_code` varchar(255) NOT NULL default '',
	  `value` text NOT NULL,
	  INDEX(`category_id`),
	  INDEX(`store_id`),
	  
	  FOREIGN KEY (`category_id`) REFERENCES {$this->getTable('faq_category')} (`category_id`) ON DELETE CASCADE ON UPDATE CASCADE,
	  FOREIGN KEY (`store_id`) REFERENCES {$this->getTable('core/store')} (`store_id`) ON DELETE CASCADE ON UPDATE CASCADE,
	  PRIMARY KEY (`category_value_id`)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8;
	

");//UNIQUE(`category_id`, `store_id`),	  UNIQUE(`faq_id`, `store_id`),
$installer->endSetup(); 