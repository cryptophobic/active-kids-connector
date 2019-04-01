<?php

namespace Utils\Xml;

/**
 * Class Analyser
 * @package Utils\Xml
 */
class Analyser
{

    /**
     * @var array
     */
    private $_stack;

    /**
     * @var array
     */
    private $_tags;

    /**
     * @var bool|int
     */
    private $_limit = false;

    /**
     * Analyser constructor.
     * @param int $limit
     */
    function __construct($limit = 1000)
    {
        $this->_stack = [];
        $this->_tags = [];
        $this->_limit = $limit;
    }

    /**
     * @return array
     */
    public function getTags()
    {
        return $this->_tags;
    }

    /**
     * @param $parser
     * @param $name
     * @throws \Exception
     */
    public function endElement($parser, $name)
    {
        array_pop($this->_stack);

        if ($this->_limit !== false && (--$this->_limit) <= 0) {
            //TODO throw custom exception
            throw new \Exception('Result OK');
        }
    }

    /**
     * @param $parser
     * @param $name
     * @param $attrs
     */
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
