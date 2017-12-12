<?php
/**
 * MageWorx
 * Abandoned Cart Recovery
 *
 * @category   MageWorx
 * @package    MageWorx_AbCart
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_AbCart_Block_Adminhtml_Report_Chart_Type_Pie extends Mage_Adminhtml_Block_Template
{
    protected $_chart;
    protected $_chartDivId = 'mageworx_abcart_chart';

    public function __construct()
    {
        $this->_chart = new Highchart_Highchart();

        $this->setOptionData('chart', array(
            'type' => 'pie',
            'renderTo' => $this->_chartDivId
        ));

        $this->setOptionData('yAxis', array(
            'allowDecimals' => false,
            'min' => 0,
            'title' => array('text' => 'some text')
        ));

        $this->setOptionData('yAxis', array(
            'type' => 'datetime',
        ));

        $this->setOptionData('plotOptions', array(
            'pie' => array(
                'allowPointSelect' => true,
                'cursor' => 'pointer',
                'dataLables' => array(
                    "enabled" => true,
                    "color" => '#000000',
                    "connectorColor" => '#000000',
                    "format" => '<b>{point.name}</b>: {point.percentage:.1f} %'
                )
            )
        ));

        $this->_showChart = false;

        return parent::__construct();
    }

    /**
     * @param $option
     * @param $data
     */
    protected function setOptionData($option, $data)
    {
        $this->_chart->getOptions()->getData($option)->setData($data);
    }

    /**
     * @param $data
     */
    public function setSeriesData($data)
    {
        $this->_checkEmptyData($data);

        $this->setOptionData('series', array(array(
            "type" => "pie",
            "name" => "Abandoned Carts",
            "data" => $data
        )));
    }

    /**
     * @return string
     */
    public function getChartHtml()
    {
        if (!$this->_showChart) {
            return '';
        }

        $html = '<div id="'.$this->_chartDivId.'"></div>';
        $html .= '<script type="text/javascript">';
        $html .= $this->_chart->render();
        $html .= '</script>';

        return $html;
    }

    /**
     * @param $data
     * @return bool
     */
    protected function _checkEmptyData($data)
    {
        $showChart = false;
        foreach ($data as $v) {
            if ($v[1] > 0) {
                $showChart = true;
                break;
            }
        }

        $this->_showChart = $showChart;

        return $this->_showChart;
    }
}