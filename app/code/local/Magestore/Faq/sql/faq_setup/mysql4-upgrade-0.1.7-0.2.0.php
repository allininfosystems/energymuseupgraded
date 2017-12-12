<?php

$installer = $this;

$installer->startSetup();
$installer->run("
  ALTER TABLE {$this->getTable('faq')}
  ADD INDEX (`category_id`),
  ADD FOREIGN KEY (`category_id`) REFERENCES {$this->getTable('faq_category')}(`category_id`);

");
$installer->endSetup(); 