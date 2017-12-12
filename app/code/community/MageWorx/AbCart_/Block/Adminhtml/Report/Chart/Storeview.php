<?php
/**
 * MageWorx
 * Abandoned Cart Recovery
 *
 * @category   MageWorx
 * @package    MageWorx_AbCart
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_AbCart_Block_Adminhtml_Report_Chart_Storeview extends MageWorx_AbCart_Block_Adminhtml_Report_Chart_Type_Pie
{
    protected $_collection;

    public function __construct()
    {
        parent::__construct();
        $this->setOptionData('title', array(
            'text' => $this->__('Carts Abandoned Store Views')
        ));
    }

    /**
     * @param $data
     */
    public function setChartData($data)
    {
        $series = array();
        foreach ($data as $item) {
            $series[] = array($item['store_name'], (int)$item['abandoned_carts']);
        }

        $this->setSeriesData($series);
    }
}