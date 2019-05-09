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

    const CATEGORY_SPECIFICATION = 'Подраздел';

    const EXTRA_SPEC = 'Рекомендованная цена';

    const NOT_SPECIFIED = 'Не указано';

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
        $categoriesMapped = $this->_images = $this->_specifications = [];

        $result = [static::CATEGORY_SPECIFICATION => 1];
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
                            $name = $this->_mapFeatureName($name);
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
                $type = $this->_getFeatureType($item);

                return "Feature $type $item";
            }, $result);

            for ($i = 0; $i < $pictureCount; $i++) {
                $this->_images[] = 'Image ' . ($i + 1);
            }
        }
        return array_merge(static::MANDATORY, $result, $categoriesMapped, $this->_images, ['Supplier']);
    }

    /**
     * @param $featureName
     * @return string
     */
    private function _getFeatureType($featureName) {
        return in_array($featureName, Config::RoyalToys()->featureFilters) ? 'checkbox' : 'input';
    }

    /**
     * @param string $name
     * @return string
     */
    private function _mapFeatureName($name) {
        return empty(Config::RoyalToys()->featureMapping[trim($name)]) ? $name : empty(Config::RoyalToys()->featureMapping[trim($name)]);
    }

    /**
     * @param array $categoriesList
     * @param int $categoryId
     * @return string
     */
    private function _getCategoryName($categoriesList, $categoryId) {
        $categoryName = !empty($categoriesList[$categoryId]) ? $categoriesList[$categoryId]['name'] : '';
        if (!empty(Config::RoyalToys()->categoriesMapping[$categoryName])) {
            $categoryName = Config::RoyalToys()->categoriesMapping[$categoryName];
        }
        return $categoryName;
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
                    $csvRow = array_fill(0, count($header), '');
                    foreach (Config::RoyalToys()->featureFilters as $filterName) {
                        $rowPosition = $this->_specifications[$filterName] + count(self::MANDATORY);
                        $csvRow[$rowPosition] = static::NOT_SPECIFIED;
                    }

                    $categoryName = $this->_getCategoryName($categoriesList, $row['CATEGORYID'][0]);
                    if (empty($categoryName)) {
                        continue;
                    }

                    $csvRow[static::BRAND] = empty($row['VENDOR']) ? Config::Common()->companyName : $row['VENDOR'][0];
                    $csvRow[static::CATEGORY] = $categoryName;

                    $csvRow[static::PRODUCT_NAME] = $this->_cutName($row['NAME'][0], empty($row['VENDORCODE'][0]) ? $row['OFFERID'][0] : $row['VENDORCODE'][0]);


                    if ($row['PRICE'][0] > 200) {
                        $csvRow[static::PRICE] = round($row['PRICE'][0] * 1.30);
                    } else {
                        $csvRow[static::PRICE] = round($row['PRICE'][0] * 1.40);
                    }

                    $csvRow[static::DESCRIPTION_LONG] = nl2br (empty($row['DESCRIPTION']) ? $row['NAME'][0] : $row['DESCRIPTION'][0]);
                    $csvRow[static::DESCRIPTION_SHORT] = empty($row['DESCRIPTION']) ? '' : $row['NAME'][0];
                    $csvRow[static::ORIGINAL_LINK] = $row['URL'][0];
                    $csvRow[static::STOCK] = 1;
                    $csvRow[static::IS_PUBLIC] = 1;

                    $catSpecName = !empty($categoriesList[$row['CATEGORYID'][0]]) ? trim($categoriesList[$row['CATEGORYID'][0]]['name']) : '';
                    $rowPosition = $this->_specifications[static::CATEGORY_SPECIFICATION] + count(self::MANDATORY);

                    if(!empty(Config::RoyalToys()->categoriesMapping[$catSpecName])) {
                        $csvRow[$rowPosition] = $catSpecName;
                    }

                    if (!empty($row[static::PARAMNAME])) {
                        foreach ($row[static::PARAMNAME] as $key => $name) {
                            $name = $this->_mapFeatureName(trim($name));
                            if (isset($this->_specifications[$name])) {
                                $rowPosition = $this->_specifications[$name] + count(self::MANDATORY);
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
                    $csvRow[count($csvRow)-1] = Config::RoyalToys()->SupplierName;
                    fputcsv($exportFile, $csvRow);
                }
            }
            fclose($exportFile);
        }
        return file_exists($fileName) ? $fileName : false;
    }

    /**
     * @param $name
     * @param $id
     * @return string
     */
    private function _cutName($name, $id)
    {
        //$id = "D-88844";
        //$name = "Коляска D-88844 для куклы,прогулочная,рег.ручка,стул для корм,в кор-ке,37-58-19см";
        if (mb_strlen($name) > 40) {
            $arraySplit = preg_split("/[\s,]+/", $name);
            $count = count($arraySplit);
            $result = $resultCandidate = '';
            for ($i = 0;$i < $count; $i++) {
                $resultCandidate = "$result $arraySplit[$i]";
                if (mb_strlen($resultCandidate) > 40) {
                    break;
                }
                $result = $resultCandidate;
            }
            if (mb_strlen($result) > 0) {
                $arraySplit = preg_split("/[\s\-\(\)]+/", $id);
                $result = str_replace($arraySplit, "", $result);
                $result = preg_replace(["/\([\s\-]*\)/", "/\s+\-+/", "/\-+\s+/", "/\"/"], " ", $result);
                $name = trim($result) . ' '. $id;

                $name = preg_replace(["/\s+/", "/\-+/"], [" ", "-"], $name);

            }
        }
        return $name;
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