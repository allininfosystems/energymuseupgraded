<?php


$installer = $this;

$installer->startSetup();

$installer->getConnection()->addColumn($this->getTable('faq/faq'), 'tag', "varchar(255) NULL");
$installer->getConnection()->addColumn($this->getTable('faq/faq'), 'metakeyword', "text NULL");
$installer->getConnection()->addColumn($this->getTable('faq/faq'), 'metadescription', "text NULL");
if($this->getTable('faq/faq')){
    $most_frequently = Mage::getModel("faq/faq")->getCollection()
                ->setStoreId(Mage::app()->getStore()->getStoreId())
                ->addFieldToFilter('most_frequently',1)
                ->setOrder('ordering', 'ASC')
                ->setOrder('title', 'ASC')
                ->setOrder('update_time', 'DESC');
     if(count($most_frequently)){
                foreach($most_frequently as $q) $q->updateUrlKey();
            }
}
$installer->endSetup();