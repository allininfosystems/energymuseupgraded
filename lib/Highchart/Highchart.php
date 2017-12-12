<?php
/**
 * MageWorx
 * Abandoned Cart Recovery
 *
 * @category   MageWorx
 * @package    MageWorx_AbCart
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */

class Highchart_Highchart extends Varien_Object {
    
    const ENGINE_JQUERY = 1;
    const ENGINE_MOOTOOLS = 2;
    const ENGINE_PROTOTYPE = 3;
    /**
     * The highchart options
     *
     * @var Highchart_Highchart_Option
     */
    protected $_options = array();

    /**
     * The javascript library to use.
     * One of ENGINE_JQUERY, ENGINE_MOOTOOLS or ENGINE_PROTOTYPE
     * @var int
     */
    protected $_jsEngine = array();

    protected $_globalOptionsKeys = array('global', 'lang');

    /**
     *
     * @var type
     * @todo Refactor or remove
     */
    protected $_ajax = false;

    /**
     * Object initialisation
     */
    protected function _construct($jsEngine = self::ENGINE_PROTOTYPE)
    {
        parent::_construct();
        $this->_options = new Highchart_Highchart_Option();
        $this->_jsEngine = $jsEngine;
    }

    /**
     * Highchart options
     * @return Highchart_Highchart_Option
     */
    public function getOptions()
        {
        return $this->_options;
    }

    /**
     * Set first level option data
     * @param string $option option name
     * @param array $data key => value pairs
     * @return Highchart_Highchart
     */
    protected function _setOption($option, $data)
    {
        foreach ($data as $key => $value) {
            $this->_options->getData($option)->setData($key, $value);
        }
        return $this;
    }

    /**
     * Add series to highchart options
     * @param Highchart_Highchart_Option|Highchart_Highchart_Option_Array $seriesData
     */
    public function setSeries($seriesData)
    {
        $this->_options->setSeries($seriesData);
    }

    /**
     * Render the chart and returns the javascript code
     * fully ready to use
     *
     * @param string $var The javascript chart variable name
     * @param string $callback The function callback to pass to the Highcharts.Chart method
     * @return string The javascript code
     */
    public function render($var = null, $callback = null)
    {
        $result = '';
        if (!is_null($var)) {
            $result = "$var = ";
        }
        $result .= $this->_renderGlobalOptions();
        $result .= "\n// DEFINE HIGHCHARTS ";
        $result .= "\nnew Highcharts.Chart(";
        $result .= $this->_renderChartOptions();
        $result .= is_null($callback) ? '' : ", $callback";
        $result .= ");";
        $result .= "\n// END DEFINE HIGHCHARTS ";
        return $result;
    }

    /**
     * Render Highcharts global options and return the javascript that
     * represents them
     *
     * @return string The javascript code
     */
    protected function _renderGlobalOptions()
    {
        $options = array();
        foreach ($this->_globalOptionsKeys as $key) {
            if ($this->_options->hasData($key)) {
                $options[$key] = $this->_options->getFormattedData($key);
            }
        }
        $result  = '\n\n // HIGHCHARTS GLOBAL OPTIONS ';
        $result .= "\n Highcharts.setOptions(\n";
        $result .= " " . Zend_Json::encode($options) . "\n";
        $result .= " );\n";
        $result .= '\n\n // END HIGHCHARTS GLOBAL OPTIONS ';
    }

    /**
     * Render the chart initialization options and return the javascript that
     * represents them
     *
     * @return string The javascript code
     */
    protected function _renderChartOptions()
    {
       $options = $this->_options->getFormattedData();
       //remove global options
       foreach ($this->_globalOptionsKeys as $key) {
           unset($options[$key]);
       }
       $result = Zend_Json::encode($options);
       return $result;
    }

    /**
     *
     * @param array|object $options
     * @return string JSON  encoded 
     */
    protected function _jsonEncode($options)
    {
        $value_arr = array();
        $replace_keys = array();
        $res = $this->_jsonEncodeF($arr);
        // Now encode the array to json format
        $json = json_encode($arr);
        // Replace the special keys with the original string.
        $json = str_replace($res['replace_keys'], $res['value_arr'], $json);
        return $json;

    }


    /**
     *
     *
     *
     * #########################
     * #########################
     * #########################
     *
     *
     *
     *
     *
     */

    

    
    /** returns new Highcharts javascript
     * @return string - highcharts!
     * @todo Refactor or remove
     */
    function renderChart($engine = 'prototype')
    {
        $chartJS = '';
        if(!$this->_ajax){
            if ($engine == 'mootools')
            $chartJS = 'window.addEvent(\'domready\', function() {';
            elseif ($engine == 'prototype')
                $chartJS = 'document.observe(\'dom:loaded\', function() {';
            else
                $chartJS = '$(document).ready(function() {';
        }

        $chartJS .= "\n\n    // HIGHROLLER - HIGHCHARTS UTC OPTIONS ";

        $chartJS .= "\n    Highcharts.setOptions(\n";
        $chartJS .= "       " . json_encode($this->_renderChartOptions()) . "\n";
        $chartJS .= "    );\n";
        $chartJS .= "\n\n    // HIGHROLLER - HIGHCHARTS '" . $this->getTitle() . "' " . $this->getType() . " chart";
        $chartJS .= "\n    var " . $this->getRenderTo() . " = new Highcharts.Chart(\n";
        $chartJS .= "       " . $this->getChartOptionsObject() . "\n";
        $chartJS .= "    );\n";
        if(!$this->_ajax){
            $chartJS .= "\n  });\n";
        }
        return trim($chartJS);
    }

    /**
     *
     * @param type $use
     * @return Highchart_Highchart
     * @todo Refactor or remove
     */
    public function setUseAjax($use = false)
    {
        $this->_ajax = $use;
        return $this;
    }

    /**
     *
     * @return type
     * @todo Refactor or remove
     */
    public function getUseAjax()
    {
        return $this->_ajax;
    }

    

    /**
     *
     * @param type $arr
     * @return string
     * @todo Refactor or remove
     */
    protected function _jsonEncodeF(&$arr)
    {
        $resArr = array('value_arr' => array(), 'replace_keys' => array());
        foreach($arr as $key => &$value){
        // Look for values starting with 'function('
            $r = array();
            if(is_array($value)){
                $r = $this->_jsonEncodeF($value);
                $resArr['value_arr'] = array_merge($resArr['value_arr'], $r['value_arr']);
                $resArr['replace_keys'] = array_merge($resArr['replace_keys'], $r['replace_keys']);
            } elseif (is_string($value)) {
                if(strpos($value, 'function')===0){
                    // Store function string.
                    $resArr['value_arr'][] = $value;
                    // Replace function string in $foo with a 'unique' special key.
                    $value = '%' . $key . '%';
                    // Later on, we'll look for the value, and replace it.
                    $resArr['replace_keys'][] = '"' . $value . '"';
                }
            }
        }
        return $resArr;
    }

    /**
     *
     * @param type $periodType
     * @return type
     * @todo Refactor or remove
     */
    protected function _getMicroTimeByPeriodType($periodType = 'day')
    {
        switch ($periodType) {
                case 'year':
                    $seconds = 3600*24*365*1000;
                    break;
                case 'month':
                    $seconds = 3600*24*31*1000;
                    break;
                case 'hour':
                    $seconds = 3600*1000;
                    break;
                default:
                case 'day':
                    $seconds = 3600*24*1000; // day in microtime
                    break;
            }
        return $seconds;
    }

    /**
     *
     * @param type $xPosition
     * @param type $yPosition
     * @param type $layoutVertical
     * @return Highchart_Highchart
     * @todo Refactor or remove
     */
    public function setLegend($xPosition = false, $yPosition = false, $layoutVertical = false)
    {
        $arr = array();
        switch ($xPosition) {
            case 'left':
                $arr['align'] = 'left';
                $arr['x'] = 50;
                $arr['y'] = -10;
                break;
            case 'right':
                $arr['align'] = 'right';
                $arr['x'] = -200;
                break;
        }
        switch ($yPosition) {
            case 'top':
                $arr['verticalAlign'] = 'top';
                break;
            case 'bottom':
                $arr['verticalAlign'] = 'bottom';
                break;
        }
        if (!empty($arr)) {
            $arr['floating'] = true;
            $arr['borderWidth'] = 0;
            if($layoutVertical){
                $arr['layout'] = 'vertical';
            }
        }
        $this->setLegendArr($arr);
        return $this;
    }

    /**
     * Add series to the chart
     * @param Highchart_Seriesdata $chartData
     * @return Highchart_Highchart
     * @todo Refactor or remove
     */
    public function addSeries_old(Highchart_Seriesdata $chartData)
    {
        if (!is_object($chartData)) {
            die("HighRoller::addSeries() - series input format must be an object.");
        }

        if (is_object($this->_series)) {   // if series is an object
            $this->_series = array($chartData);
        } else if (is_array($this->_series)) {                        // else
            array_push($this->_series, $chartData);
        }
        return $this;
    }

    /** returns valid Highcharts javascript object containing your HighRoller options, for manipulation between the markup script tags on your page
     * @return string - highcharts options object!
     * @todo Refactor or remove
     */
    public function getChartOptionsObject() {
        $arr = array(
            'chart' => $this->getChartOptions(),
            'title' => array('text' => $this->getTitle()),
            'series' => $this->_series,
        );

        if ($this->getLegendArr()) {
            $arr['legend'] = $this->getLegendArr();
        } 
//        else {
//            $arr['legend'] = false;
//        }
        if ($this->getSubtitleArr()) {
            $arr['subtitle'] = $this->getSubtitleArr();
        }
        if ($this->getTooltipArr()) {
            $arr['tooltip'] = $this->getTooltipArr();
        }
        if ($this->getXAxis())
            $arr['xAxis'] = $this->getXAxis();
        if ($this->getYAxis())
            $arr['yAxis'] = $this->getYAxis();
        if ($this->getPlotOptions())
            $arr['plotOptions'] = $this->getPlotOptions();
        if($this->getData('data_column_labels')){
            $ob = $arr['series'][0];
            $ob->adddataLabels($this->getData('data_column_labels'));
        }
        return trim($this->_jsonEncode($arr));
    }

    /**
     *
     * @param type $timeRange
     * @param type $periodType
     * @param type $name
     * @return Highchart_Highchart
     * @todo Refactor or remove
     */
    public function setDateTooltip($timeRange, $periodType = 'day', $name = 'line')
    {
        $timeRange = $timeRange * 1000; //micro seconds
        $seconds = $this->_getMicroTimeByPeriodType($periodType);
        
        $arr = array('formatter' => "function() {
                        ret = '';
                        for (var i = 0; this.points[i]; i++) {
                            time = this.points[i].x;
                            if(i == 1){
                                time = this.points[i].x - $timeRange;
                            }
                            if(i > 0){
                                ret = ret + '<br/>';
                            }
                            ret = ret + 
                                    '<span style=\"font-size: 10px; font-weight:bold\">' + 
                                    Highcharts.dateFormat('%e. %b %Y', time) + 
                                    ' </span><br/><span style=\"fill:' + this.points[i].series.color + '\" >' +
                                    '$name' +
                                    '</span>' +
                                    '<span>: </span>' +
                                    '<span style=\"font-weight:bold\">' +
                                    this.points[i].y +
                                    '</span>';
                        }
                        return ret;
                    }
                "
                );
        $arr['crosshairs'] = true;
        $arr['shared'] = true;

        $this->setTooltipArr($arr);
        return $this;
    }

    /**
     *
     * @return Highchart_Highchart
     * @todo Refactor or remove
     */
    public function setPieTooltip()
    {
        $arr = array('formatter' => "function(){
                    return '<b>' + this.point.name + ' :</b> ' + Math.round(this.percentage) + '%';
                }");
        $this->setTooltipArr($arr);
        return $this;
    }

    /**
     *
     * @param type $subtitle
     * @return Highchart_Highchart
     * @todo Refactor or remove
     */
    public function setSubtitle($subtitle) {
        $arr = array('text' => $subtitle);
        $this->setSubtitleArr($arr);
        return $this;
    }

    /**
     * all in microtime
     * 'pointInterval' => 3600000*24, // one day
     * 'pointStart' => 1254787200000
     * 
     * pie($this->getType()): {
     *    allowPointSelect: true,
     *    cursor: 'pointer',
     *    dataLabels: {
     *    enabled: true,
     *    color: '#000000',
     *    connectorColor: '#000000',
     *        formatter: function() {
     *            return '<b>'+ this.point.name +'</b>: '+ this.percentage +' %';
     *        }
     *    }
     * }
     * 
     * 
     * @return boolean
     * @todo Refactor or remove
     */
    protected function getPlotOptions() 
    {
        if($this->getType() == 'pie'){
                $arr = array(
                    'allowPointSelect' => true,
                    'cursor' => 'pointer',
                    'dataLabels' => array(
                        'enabled' => true,
                        'color' => '#000000',
                        'connectorColor' => '#000000',
                        'formatter' => "function() {
                                return '<b>'+ this.point.name +'</b>: '+ Math.round(this.percentage) +' %';
                            }"
                    )
                );
                $this->setData('plot_options', $arr);
        } 
        if (!$this->getData('plot_options')) {
            return false;
        } else {
            $arr = array($this->getType() => $this->getData('plot_options'));
        }
        return $arr;
    }

    /**
     *  type: 'datetime',
     *  maxZoom: 14 * 24 * 3600000, // fourteen days
     *  title: {
     *     enabled: true,
     *     text: null
     *  }
     *  reversed: false,
     *  maxPadding: 0.05,
     *  showLastLabel: true
     *  categories: ['1750', '1800', '1850', '1900', '1950', '1999', '2050' ...],
     *  tickmarkPlacement: 'on',
     * @return array()
     * @todo Refactor or remove
     */
    public function getXAxis()
    {
        return $this->getData('x_axis');
    }

    /**
     *
     * @return type
     * @todo Refactor or remove
     */
    public function getYAxis() {
        return $this->getData('y_axis');
    }

    /**
     *
     * @param type $title
     * @todo Refactor or remove
     */
    public function setYAxisTitle($title)
    {
        $this->_setAxisTitle('y', $title);
    }

    /**
     *
     * @param type $title
     * @todo Refactor or remove
     */
    public function setXAxisTitle($title)
    {
        $this->_setAxisTitle('x', $title);
    }

    /**
     *
     * @param type $axis
     * @param type $title
     * @return Highchart_Highchart
     * @todo Refactor or remove
     */
    protected function _setAxisTitle($axis, $title)
    {
        $title = trim($title);
        $res = array();
        $res['title'] = array('text' => false);
        if ($title) {
            $res['title']['text'] = $title;
        }
        $this->addAxisData($axis, $res);
        return $this;
    }

    /**
     * Adds data to axes data array
     *
     * @param string $axis valid values "x" or "y"
     * @param array $data
     * @todo Refactor or remove
     */
    public function addAxisData($axis, array $data)
    {
        if (!($res = $this->getData())) {
            $res = array();
        }
        $this->setData($axis.'_axis', array_merge($res, $data));
    }


    /**
     *
     * @return Highchart_Highchart
     * @todo Refactor or remove
     */
    public function setDataLabels()
    {
        $arr = array(
                'enabled' => true,
                'rotation' => -90,
                'color' => '#FFFFFF',
                'align' => 'right',
                'x' => -5,
                'y' => 10,
//                'formatter' => 'function() {return this.y;}',
                'style' => array(
                    'fontSize' => '13px',
                    'fontFamily' => 'Verdana, sans-serif',
                    ),
            );
        $this->setData('data_column_labels', $arr);
        return $this;
                
    }

    /**
     *
     * @return type
     * @todo Refactor or remove
     */
    protected function getChartOptions() {
        if (!$this->getData('chart')) {
            $arr = array('renderTo' => $this->getRenderTo(), 'type' => $this->getType());
            if ($this->getSpacingRight())
                $arr['spacingRight'] = $this->getSpacingRight();
            if ($this->getZoomType())
                $arr['zoomType'] = $this->getZoomType();
            return $arr;
        }
        return $this->getData('chart');
    }
}