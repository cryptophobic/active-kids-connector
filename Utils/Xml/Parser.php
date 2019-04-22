<?php

namespace Utils\Xml;

/**
 * Class Parser
 * @package Utils\Xml
 */
class Parser
{
    /**
     * @var resource
     */
    private $_fileHandler;

    /**
     * @var array
     */
    private $_options;

    /**
     * @var resource 
     */
    private $_parser;

    /**
     * @var Processor 
     */
    private $_parseProcessor = null;

    /**
     * @return array of fields
     */
    private function _options()
    {
        return ['pathXml' => '', 'fileName' => '', 'feedUrl' => ''];
    }

    /**
     * check if file is open
     *
     * @return bool
     */
    private function _isOpenedFile()
    {
        return $this->_fileHandler !== null && get_resource_type($this->_fileHandler) === 'stream';
    }

    /**
     * @param bool $rewind
     * @return bool|resource|null
     */
    private function _openFile($rewind = false)
    {
        if (!$this->_isOpenedFile()) {
            $this->_fileHandler = fopen($this->_options['fileName'], 'rb');
        } elseif ($rewind === true) {
            rewind($this->_fileHandler);
        }
        return $this->_fileHandler;
    }

    /**
     * clean all options
     */
    private function _resetOptions()
    {
        $this->_options = $this->_options();
    }

    /**
     * @param $options array csv params to parse
     */
    private function _setOptions(array $options)
    {
        $options = array_intersect_key($options, $this->_options());
        $this->_options = array_merge($this->_options, $options);
    }

    /**
     * @param $optionName
     * @param $value
     * @return bool
     * @throws \Exception
     */
    public function setOption($optionName, $value)
    {
        if (array_key_exists($optionName, $this->_options)) {
            $this->_options[$optionName] = $value;
            return true;
        } else {
            throw new \Exception("Option $optionName is not defined");
        }
    }

    /**
     * XmlParser constructor.
     * @param array $options
     */
    public function __construct(array $options = [])
    {
        $this->_fileHandler = null;
        $this->_resetOptions();
        $this->_setOptions($options);
    }

    /**
     * @return array
     * @throws \Exception
     */
    public function analyze()
    {
        $analyser = new Analyser();

        $xmlParser = xml_parser_create();
        xml_set_element_handler($xmlParser, [$analyser, "startElement"], [$analyser, "endElement"]);

        $this->_openFile(true);

        //TODO 'Result OK' something terrible, communication with XmlAnalyser
        try {
            while ($this->start($xmlParser) === true){
                /*
                 * I don't know what to do here,
                 * Sincerely yours, Php Script
                 */
            }
        }
        catch (\Exception $e) {
            if ($e->getMessage() !== 'Result OK') {
                throw $e;
            }
        }
        $this->_openFile(true);

        $result = [];

        $tags = $analyser->getTags();

        $deepest = "";
        $productTag = "";
        $depth = 0;

        foreach ($tags as $tag => $value) {
            $value['count'] = $value['count'] == 0 ? 1 : $value['count'];
            $tags[$tag]['contents_average'] = $value['contents_count']/$value['count'];


            if ($value['depth'] > $depth) {
                if ($tags[$tag]['contents_average'] > 2) {
                    if (mb_stripos($tag, 'product') !== false) {
                        $productTag = $tag;
                    }
                    $depth = $value['depth'];
                    $deepest = $tag;
                } elseif ($depth == 0) {
                    $deepest = $tag;
                }
            }
        }

        if (!empty($productTag)) {
            $result['pathXml'] = $productTag;
        } else {
            $result['pathXml'] = $deepest;
        }
        $result['tags'] = $tags;
        $result['options'] = $this->_options;
        return $result;
    }

    /**
     * @param int $rowsCount
     * @return array|bool
     * @throws \Exception
     */
    public function getRows($rowsCount = 25)
    {
        if ($this->_parseProcessor === null || $this->_parser === null) {
            $this->_parseProcessor = new Processor($this->_options['pathXml']);
            $this->_parser = xml_parser_create();
            xml_set_element_handler($this->_parser, [$this->_parseProcessor, "startElement"], [$this->_parseProcessor, "endElement"]);
            xml_set_character_data_handler($this->_parser, [$this->_parseProcessor, "characters"]);
        }

        $rows = $this->_parseProcessor->popRows($rowsCount);
        $neededCount = $rowsCount - count($rows);

        if ($neededCount > 0) {

            while ($neededCount > 0) {
                $res = $this->start($this->_parser);
                $rows = array_merge($rows,$this->_parseProcessor->popRows($neededCount));
                $neededCount = $rowsCount - count($rows);
                if ($res === false)
                {
                    break;
                }
            }
        }

        if (count($rows) === 0) {
            return false;
        }

        return [
            'header' => $this->_parseProcessor->getHeader(),
            'rows' => $rows,
            'options' => $this->_options,
            'pathXml' => $this->_options['pathXml']
        ];
    }

    /**
     * @param $xmlParser
     * @return bool
     * @throws \Exception
     */
    public function start($xmlParser)
    {
        if (!$this->_openFile()) {
            throw new \Exception("Unable to open file ".$this->_options['fileName']);
        }

        if ($data = fread($this->_fileHandler, 1000)) {
            if (!xml_parse($xmlParser, $data)) {
                throw new \Exception("XML Error: " . xml_error_string(xml_get_error_code($xmlParser))." at line " . xml_get_current_line_number($xmlParser),1);
            }
        }
        if (feof($this->_fileHandler)) {
            return false;
        } else {
            return true;
        }
    }
}
