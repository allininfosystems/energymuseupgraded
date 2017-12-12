<?php
/**
 * MageWorx
 * Abandoned Cart Recovery
 *
 * @category   MageWorx
 * @package    MageWorx_AbCart
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_AbCart_Block_Adminhtml_Report_Device extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_controller = 'adminhtml_report_device';
        $this->_blockGroup = 'mageworx_abcart';
        $this->_headerText = Mage::helper('reports')->__('Carts Abandoned with Devices');

        parent::__construct();

        $this->_removeButton('add');
        $this->addButton('filter_form_submit', array(
            'label'     => Mage::helper('reports')->__('Show Report'),
            'onclick'   => 'filterFormSubmit()'
        ));
    }

    /**
     * @return string
     */
    public function getFilterUrl()
    {
        $this->getRequest()->setParam('filter', null);
        return $this->getUrl('*/*/device', array('_current' => true));
    }
}