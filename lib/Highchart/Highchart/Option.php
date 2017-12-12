<?php
/**
 * MageWorx
 * Abandoned Cart Recovery
 *
 * @category   MageWorx
 * @package    MageWorx_AbCart
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */
class Highchart_Highchart_Option extends Varien_Object
{
    public function __construct()
    {
        $args = func_get_args();
        if (!isset($args[0])) {
            $args[0] = array();
        }
        if (is_array($args[0])) {
            foreach ($args[0] as $k => $v) {
                $this->_data[$k] = new self($v);
            }
        }
        else {
            $this->_data = $args[0];
        }
        $this->_construct();
    }


    /**
     * Converts field names for setters and geters
     *
     * $this->setMyField($value) === $this->setData('myField', $value)
     * Uses cache to eliminate unneccessary preg_replace
     *
     * @param string $name
     * @return string
     */
    protected function _underscore($name)
    {
        if (isset(self::$_underscoreCache[$name])) {
            return self::$_underscoreCache[$name];
        }
        // lcfirt() function replacer for PHP < 5.3
        $result = (string)(strtolower(substr($name,0,1)).substr($name,1));
        self::$_underscoreCache[$name] = $result;
        return $result;
    }

    /**
     * Overwrite data in the object.
     *
     * $key can be string or array.
     * If $key is string, the attribute value will be overwritten by $value
     *
     * If $key is an array, it will overwrite all the data in the object.
     *
     * @param string|array $key
     * @param mixed $value
     * @return Varien_Object
     */
    public function setData($key, $value=null)
    {
        $this->_hasDataChanges = true;
        if(is_array($key)) {
            $this->_data = new self($key);
        } else {
            if (is_array($value)) {
                $this->_data[$key] = new self($value);
            }
            elseif (is_string($value)) {
                // TODO: Avoid json-encode errors
                $this->_data[$key] = $value;
            }
            else {
                $this->_data[$key] = $value;
            }
        }
        return $this;
    }

    /**
     * Retrieves data from the object
     *
     * If $key is empty will return all the data
     * Otherwise it will return value of the attribute specified by $key
     *
     * If $index is specified it will assume that attribute data is an array
     * and retrieve corresponding member.
     *
     * @param string $key
     * @param string|int $index Not used
     * @return mixed
     */
    public function getData($key='', $index=null)
    {
        if (''===$key) {
            return $this->_data;
        }

        $default = null;

        // accept a/b/c as ['a']['b']['c']
        if (strpos($key,'/')) {
            $keyArr = explode('/', $key);
            $data = $this->_data;
            foreach ($keyArr as $i=>$k) {
                if ($k==='') {
                    return $default;
                }
                if (is_array($data)) {
                    if (!isset($data[$k])) {
                        return $default;
                    }
                    $data = $data[$k];
                } elseif ($data instanceof Varien_Object) {
                    $data = $data->getData($k);
                } else {
                    return $default;
                }
            }
            return $data;
        }

        
        if (is_array($this->_data)) {
            if (!isset($this->_data[$key])) {
                $this->_data[$key] = new self();
            }
            return $this->_data[$key];
        }
        elseif ($this->_data instanceof Highchart_Highchart_Option) {
            return $this->_data->getData($key);
        }

        return $default;

        // legacy functionality for $index
        if (isset($this->_data[$key])) {
            if (is_null($index)) {
                return $this->_data[$key];
            }

            $value = $this->_data[$key];
            if (is_array($value)) {
                //if (isset($value[$index]) && (!empty($value[$index]) || strlen($value[$index]) > 0)) {
                /**
                 * If we have any data, even if it empty - we should use it, anyway
                 */
                if (isset($value[$index])) {
                    return $value[$index];
                }
                return null;
            } elseif (is_string($value)) {
                $arr = explode("\n", $value);
                return (isset($arr[$index]) && (!empty($arr[$index]) || strlen($arr[$index]) > 0)) ? $arr[$index] : null;
            } elseif ($value instanceof Varien_Object) {
                return $value->getData($index);
            }
            return $default;
        }
        return $default;
    }

    public function hasData($key = '')
    {
        if (!is_array($this->_data)) {
            return false;
        }
        return parent::hasData($key);
    }

    public function getFormattedData($key='')
    {
        $data = $this->getData($key);
        if (is_array($data)) {
            foreach ($data as $k => $v) {
                if ($v instanceof self) {
                    $data[$k] = $v->getFormattedData();
                }
                else {
                    $data[$k] = $v;
                }
            }
        }
        elseif ($data instanceof self) {
            $data = $data->getFormattedData();
        }
        return $data;
    }
}