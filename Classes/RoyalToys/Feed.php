<?php

namespace Classes\RoyalToys;

use Config\Common;
use Config\Config;
use Utils\EasyDownloader;
use Utils\Xml\Parser;

/**
 * Class Feed
 * @package Classes\RoyalToys
 */
class Feed
{
    const EXTRA_SPEC = 'Рекомендованная цена';

    const MANDATORY = ['Brand', 'Category', 'Product name', 'Price', 'Description long', 'Description short', 'Stock', 'Public', 'Original link'];

    const BRAND = 0;

    const CATEGORY = 1;

    const PRODUCT_NAME = 2;

    const PRICE = 3;

    const DESCRIPTION_LONG = 4;

    const DESCRIPTION_SHORT = 5;

    const STOCK = 6;

    const IS_PUBLIC = 7;

    const ORIGINAL_LINK = 8;

    const XML_OFFERS_PATH = '/YML_CATALOG/SHOP/OFFERS/OFFER';

    const XML_CATEGORIES_PATH = '/YML_CATALOG/SHOP/CATEGORIES/CATEGORY';

    const PARAMNAME = 'PARAMNAME';

    const PARAM = 'PARAM';

    const PICTURE = 'PICTURE';

    /**
     * @var array
     */
    private $_specifications;

    /**
     * @var array
     */
    private $_images;

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
    private function _prepareCategories()
    {
        $categoriesList = [];
        if ($this->_prepareFeed()) {
            $parser = new Parser([
                'fileName' => $this->_fileName,
                'pathXml' => static::XML_CATEGORIES_PATH,
            ]);
            while($categoriesArray = $parser->getRows(500)) {
                foreach ($categoriesArray['rows'] as $category) {
                    if (!empty($category['CATEGORYPARENTID'])) {
                        $categoriesList[$category['CATEGORYID'][0]] = ['name' => $category['CATEGORY'][0], 'parentId' => $category['CATEGORYPARENTID'][0]];
                    } else {
                        $categoriesList[$category['CATEGORYID'][0]] = ['name' => $category['CATEGORY'][0]];
                    }
                }
            }
        }
        return $categoriesList;
    }

    /**
     * @throws \Exception
     */
    public function brandsList()
    {
        if ($this->_prepareFeed()) {
            $brands = [];
            $parser = new Parser([
                'fileName' => $this->_fileName,
                'pathXml' => static::XML_OFFERS_PATH,
            ]);
            while($offersArray = $parser->getRows(5)) {
                foreach ($offersArray['rows'] as $offer) {
                    if (!empty($offer['VENDOR'])) {
                        $brands[$offer['VENDOR']] = $offer['VENDOR'];
                    }
                }
            }
            $brands = array_values($brands);
            sort($brands);
            print_r(implode("\n",array_values($brands)));
        }
    }

    /**
     * @return array
     * @throws \Exception
     */
    private function _prepareHeader()
    {
        $result = $this->_images = $this->_specifications = [];
        $pictureCount = 0;
        if ($this->_prepareFeed()) {
            $parser = new Parser([
                'fileName' => $this->_fileName,
                'pathXml' => static::XML_OFFERS_PATH,
            ]);
            while ($array = $parser->getRows(100)) {
                foreach ($array['rows'] as $row) {
                    if (!empty($row[static::PARAMNAME])) {
                        foreach ($row[static::PARAMNAME] as $key => $name) {
                            if (trim($name) !== static::EXTRA_SPEC) {
                                $result[trim($name)] = 1;
                            }
                        }
                    }
                    if (!empty($row[static::PICTURE])) {
                        $pictureCount = count($row[static::PICTURE]) > $pictureCount ? count($row[static::PICTURE]) : $pictureCount;
                    }
                }
            }

            $result = array_keys($result);
            $this->_specifications = array_flip($result);

            $result = array_map(function ($item) {
                return "Feature input ".$item;
            }, $result);

            for ($i = 0; $i < $pictureCount; $i++) {
                $this->_images[] = 'Image ' . ($i + 1);
            }
        }
        return array_merge(static::MANDATORY, $result, $this->_images);
    }

    /**
     * @return bool|string
     * @throws \Exception
     */
    public function run()
    {
        $fileName = Config::Common()->filePath.DIRECTORY_SEPARATOR.Config::RoyalToys()->outputFileName;
        if ($this->_prepareFeed()) {

            $header = $this->_prepareHeader();
            $categoriesList = $this->_prepareCategories();

            $parser = new Parser([
                'fileName' => $this->_fileName,
                'pathXml' => static::XML_OFFERS_PATH,
            ]);

            $exportFile = fopen($fileName, 'wb');
            fputcsv($exportFile, $header);

            while($array = $parser->getRows(10))
            {
                foreach ($array['rows'] as $row)
                {
                    $csvRow = array_fill(0, count($header) - 1, '');
                    if (empty($categoriesList[$row['CATEGORYID'][0]])) {
                        continue;
                    }
                    $categoryName = $categoriesList[$row['CATEGORYID'][0]]['name'];

                    $csvRow[static::BRAND] = empty($row['VENDOR']) ? Config::Common()->companyName : $row['VENDOR'][0];
                    $csvRow[static::CATEGORY] = $categoryName;
                    $csvRow[static::PRODUCT_NAME] = $row['NAME'][0];
                    $csvRow[static::PRICE] = $row['PRICE'][0];
                    $csvRow[static::DESCRIPTION_LONG] = nl2br (empty($row['DESCRIPTION']) ? $row['NAME'][0] : $row['DESCRIPTION'][0]);
                    $csvRow[static::DESCRIPTION_SHORT] = empty($row['DESCRIPTION']) ? '' : $row['NAME'][0];
                    $csvRow[static::ORIGINAL_LINK] = $row['URL'][0];
                    $csvRow[static::STOCK] = 1;
                    $csvRow[static::IS_PUBLIC] = 1;

                    if (!empty($row[static::PARAMNAME])) {
                        foreach ($row[static::PARAMNAME] as $key => $name) {
                            if (isset($this->_specifications[trim($name)])) {
                                $rowPosition = $this->_specifications[trim($name)] + count(self::MANDATORY);
                                $csvRow[$rowPosition] = $row[static::PARAM][$key];
                            }
                        }
                    }

                    if (!empty($row['PICTURE'])) {
                        $pictureCount = count($row['PICTURE']);
                        for ($i = 0; $i < $pictureCount; $i++) {
                            $rowPosition = count($this->_specifications) + count(self::MANDATORY) + $i;
                            $csvRow[$rowPosition] = $row['PICTURE'][$i];
                        }
                    }
                    fputcsv($exportFile, $csvRow);
                }
            }
            fclose($exportFile);
        }
        return file_exists($fileName) ? $fileName : false;
    }

    /**
     * @return bool
     * @throws \Exception
     */
    private function _prepareFeed()
    {
        if ($this->_url) {
            $downloader = new EasyDownloader();
            $fileName = Config::Common()->filePath . DIRECTORY_SEPARATOR . 'feed.xml';
            if ($downloader->downloadFile($this->_url, $fileName) === false) {
                var_dump($downloader->getErrors());
            } else {
                $this->_fileName = $fileName;
                $this->_url = '';
            }
        }

        return ($this->_fileName && file_exists($this->_fileName));
    }
}