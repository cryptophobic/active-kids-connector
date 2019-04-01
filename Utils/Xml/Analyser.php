<?php

namespace Utils\Xml;

/**
 * Class Analyser
 * @package Utils\Xml
 */
class Analyser
{

    private $_stack = [];
    private $_tags = [];
    private $_limit = false;

    function __construct($limit = 1000)
    {
        $this->_limit = $limit;
    }

    public function getTags()
    {
        return $this->_tags;
    }

    public function endElement($parser, $name)
    {
        array_pop($this->_stack);

        if ($this->_limit !== false && (--$this->_limit) <= 0) {
            //TODO
            //throw new ExceptionETLNoneCritical('Result OK');
            throw new \Exception('Result OK');
        }
    }

    public function startElement($parser, $name, $attrs)
    {
        $root = '';
        if (count($this->_stack) > 0) {
            $root = '/' . implode("/", $this->_stack);
        }
        $current = $root . "/" . $name;

        $this->_stack[] = $name;
        $depth = count($this->_stack);

        if (count($this->_stack) > 1) {
            if (empty($this->_tags[$root])) {
                $this->_tags[$root] = ['contents_count' => 0];
            }
            $this->_tags[$root]['contents_count']++;
        }

        if (empty($this->_tags[$current])) {
            $this->_tags[$current] = ['count' => 1, 'name' => $name, 'contents_count' => 0, 'root' => $root, 'depth' => $depth];
        } else {
            $this->_tags[$current]['count']++;
        }
    }

}
