<?php
/**
 * MageWorx
 * Abandoned Cart Recovery
 *
 * @category   MageWorx
 * @package    MageWorx_AbCart
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_AbCart_Block_Adminhtml_Report_Chart_Group extends MageWorx_AbCart_Block_Adminhtml_Report_Chart_Type_Pie
{
    protected $_collection;

    public function __construct()
    {
        parent::__construct();
        $this->setOptionData('title', array(
            'text' => $this->__('Carts Abandoned by Customer Groups')
        ));
    }

    /**
     * @param $data
     */
    public function setChartData($data)
    {
        $series = array();
        foreach ($data as $item) {
            $series[] = array($item['customer_group'], (int)$item['abandoned_carts']);
        }

        $this->setSeriesData($series);
    }
}