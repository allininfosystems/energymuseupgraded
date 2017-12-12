<?php
/**
 * Scommerce Heardaboutus Admin getCsv Controller
 *
 * @category   Scommerce
 * @package    Scommerce_Heardaboutus
 * @author     Scommerce Mage (core@scommerce-mage.co.uk)
 */
class Scommerce_Heardaboutus_Adminhtml_CsvController extends Mage_Adminhtml_Controller_Action {

    public function indexAction() {
        $filename = 'heardaboutus.csv';
        $content = Mage::helper('heardaboutus')->generateCsv();
 
        $this->_prepareDownloadResponse($filename, $content);
    }
}