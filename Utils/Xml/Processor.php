<?php

namespace Utils\Xml;

/**
 * Class Processor
 * @package Utils\Xml
 */
class Processor
{

    /**
     * @var array
     */
    protected $_stack;

    /**
     * @var string
     */
    protected $_key;

    /**
     * @var string
     */
    protected $_pathXml;

    /**
     * @var bool
     */
    protected $_isInContainer;

    /**
     * @var array
     */
    protected $_row;

    /**
     * @var array
     */
    protected $_rows;

    /**
     * @var array
     */
    protected $_header = [];

    /**
     * @var bool
     */
    protected $_endContainer = true;

    /**
     * Processor constructor.
     * @param $pathXml
     */
    function __construct($pathXml)
    {
        $this->_stack = $this->_row = $this->_rows = $this->_header = [];
        $this->_key = '';
        $this->_isInContainer = false;
        $this->_pathXml = $pathXml;
    }

    /**
     * @return array
     */
    public function getHeader()
    {
        return $this->_header;
    }

    /**
     * @return array
     */
    public function getRows()
    {
        return $this->_rows;
    }

    /**
     * @param $count
     * @return array
     */
    public function popRows($count)
    {
        $newRows = array_splice($this->_rows, $count);
        $gottenRows = $this->_rows;
        $this->_rows = $newRows;
        return $gottenRows;
    }

    /**
     * @return mixed
     */
    public function popRow()
    {
        return array_pop($this->_rows);
    }

    /**
     * @param $parser
     * @param $data
     */
    public function characters($parser, $data)
    {
        if ($this->_isInContainer === true) {
            if (trim($data) !== '') {
                $this->_header[$this->_key] = $this->_key;

                if(empty($this->_row[$this->_key])) {
                    $this->_row[$this->_key] = [];
                }

                if ($this->_endContainer === true || empty($this->_row[$this->_key])) {
                    $this->_row[$this->_key][] = $data;
                } else {
                    $lastElement = count($this->_row[$this->_key]) - 1;
                    $this->_row[$this->_key][$lastElement] .= $data;
                }
                $this->_endContainer = false;
            }
        }
    }

    /**
     * @param $parser
     * @param $name
     * @param $attrs
     */
    public function startElement($parser, $name, $attrs)
    {
        if (count($this->_stack) > 0) {
            $current = '/' . implode("/", $this->_stack) . "/" . $name;
        } else {
            $current = '/' . $name;
        }
        $this->_stack[] = $name;

        if (strstr($current, $this->_pathXml) !== false) {
            $this->_isInContainer = true;
        }

        if ($this->_isInContainer === true) {
            $this->_key = str_replace($this->_pathXml, '', $current);
            $this->_key = trim(ltrim($this->_key, '/'));
            if (empty($this->_key)) {
                $this->_key = $name;
            }

            if (count($attrs) > 0) {
                foreach ($attrs as $attribute => $value) {
                    $this->_header[$this->_key . $attribute] = $this->_key . $attribute;
                    if(empty($this->_row[$this->_key . $attribute])) {
                        $this->_row[$this->_key . $attribute] = [];
                    }
                    $this->_row[$this->_key . $attribute][] = trim($value);
                }
            }
        }
    }

    /**
     * @param $parser
     * @param $name
     */
    public function endElement($parser, $name)
    {
        $current = '/' . implode("/", $this->_stack);
        if ($this->_pathXml == $current) {
            $this->_rows[] = $this->_row;
            $this->_row = [];
        }
        $this->_isInContainer = false;
        $this->_endContainer = true;
        array_pop($this->_stack);
    }

}