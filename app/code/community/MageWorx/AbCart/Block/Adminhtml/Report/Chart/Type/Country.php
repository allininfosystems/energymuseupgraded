<?php
/**
 * MageWorx
 * Abandoned Cart Recovery
 *
 * @category   MageWorx
 * @package    MageWorx_AbCart
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_AbCart_Block_Adminhtml_Report_Chart_Type_Country extends Mage_Adminhtml_Block_Template
{
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        $this->setTemplate('mageworx/abcart/chart/country.phtml');
        return $this;
    }

    /**
     * @param $data
     */
    public function setChartData($data)
    {
        $formattedData = array(
            array('Country', 'Carts Abandoned')
        );

        foreach ($data as $item) {
            if (empty($item['country_code'])) {
                continue;
            }
            $formattedData[] = array($item['country_code'], (int)$item['abandoned_carts']);
        }

        $this->setData('series_data', $formattedData);
    }
}