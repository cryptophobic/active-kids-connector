<?php

namespace Classes\RoyalToys;

use Config\Config;
use Utils\EasyDownloader;
use Utils\Xml\Parser;

/**
 * Class Feed
 * @package Classes\RoyalToys
 */
class Feed
{
    const XML_PATH = '/YML_CATALOG/SHOP/OFFERS/OFFER';

    /**
     * @var string
     */
    private $_fileName;

    /**
     * @var string
     */
    private $_url;

    /**
     * @param $fileName
     * @return $this
     */
    public function setFeedLocalFileName($fileName)
    {
        $this->_fileName = $fileName;
        return $this;
    }

    /**
     * @param $url
     * @return $this
     */
    public function setFeedUrl($url)
    {
        $this->_url = $url;
        return $this;
    }

    /**
     * @throws \Exception
     */
    public function run()
    {
        if ($this->_url)
        {
            $downloader = new EasyDownloader();
            $this->_fileName = Config::Common()->filePath.DIRECTORY_SEPARATOR.'feed.xml';
            if ($downloader->downloadFile($this->_url, $this->_fileName) === false)
            {
                var_dump($downloader->getErrors());
            }
        }

        if ($this->_fileName && file_exists($this->_fileName))
        {
            $parser = new Parser([
                'fileName' => $this->_fileName,
                'pathXml' => static::XML_PATH,
            ]);
            var_dump($parser->getRows(2));
        }
    }
}