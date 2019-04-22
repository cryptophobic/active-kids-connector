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

    const CATEGORIES_MAPPING = [
        'Детские палатки' => 'Детские палатки, корзины для игрушек и шарики',
        'Корзины для игрушек' => 'Детские палатки, корзины для игрушек и шарики',
        'Шарики для сухого бассейна' => 'Детские палатки, корзины для игрушек и шарики',

        'Скейты' => 'Детский транспорт',
        'Прыгуны' => 'Детский транспорт',
        'Толокары' => 'Детский транспорт',
        'Детские двухколесные велосипеды' => 'Детский транспорт',
        'Детские трехколесные велосипеды' => 'Детский транспорт',
        'Самокаты' => 'Детский транспорт',
        'Беговелы' => 'Детский транспорт',
        'Качалки' => 'Детский транспорт',

        'Матрасы' => 'Надувные изделия',
        'Бассейны' => 'Надувные изделия',
        'Надувные круги' => 'Надувные изделия',
        'Спортивные товары' => 'Надувные изделия',
        'Коврики для йоги' => 'Надувные изделия',
        'Боксерские наборы' => 'Надувные изделия',
        'Дартс' => 'Надувные изделия',

        'Зимние товары для улицы' => 'Товары для улицы',
        'Санки, ледянки и тарелки' => 'Товары для улицы',
        'Снежколеп' => 'Товары для улицы',

        'Игровые наборы для самих маленьких' => 'Для самых маленьких',
		'Мозаики' => 'Для самых маленьких',
		'Музыкальные игрушки' => 'Для самых маленьких',
		'Ночники, светильники' => 'Для самых маленьких',
		'Игрушки для ванной' => 'Для самых маленьких',
		'Машинки для малышей' => 'Для самых маленьких',
		'Погремушки, мобили' => 'Для самых маленьких',
		'Сортеры, логики' => 'Для самых маленьких',
		'Развивающие коврики' => 'Для самых маленьких',
		'Песочные наборы' => 'Для самых маленьких',
		'Игрушки повторюшки' => 'Для самых маленьких',
		'Книги, сказочники' => 'Для самых маленьких',
		'Игрушки с мыльными пузырями' => 'Для самых маленьких',
		'Рыбалки и сачки' => 'Для самых маленьких',

        'Домики для кукол' => 'Игрушки для девочек',
		'Игровые наборы бытовая техника' => 'Игрушки для девочек',
		'Игровые наборы доктор' => 'Игрушки для девочек',
		'Игровые наборы кухня' => 'Игрушки для девочек',
		'Игровые наборы магазин' => 'Игрушки для девочек',
		'Игровые наборы салон красоты' => 'Игрушки для девочек',
		'Куклы' => 'Игрушки для девочек',
		'Маленькие куклы' => 'Игрушки для девочек',
		'Одежда и аксессуары для кукол' => 'Игрушки для девочек',
		'Пупсы' => 'Игрушки для девочек',
		'Интерактивные куклы' => 'Игрушки для девочек',
		'Куклы типа барби' => 'Игрушки для девочек',
		'Коляски для кукол' => 'Игрушки для девочек',
		'Кроватки для кукол' => 'Игрушки для девочек',
		'Лошадки с каретами' => 'Игрушки для девочек',
		'Шкатулки' => 'Игрушки для девочек',

        'Волчки Infinity nado и Beyblade' => 'Игрушки для мальчиков',
		'Автотреки, паркинги' => 'Игрушки для мальчиков',
		'Детская железная дорога' => 'Игрушки для мальчиков',
		'Игрушечный транспорт' => 'Игрушки для мальчиков',
		'Наборы инструментов' => 'Игрушки для мальчиков',
		'Трансформеры и роботы' => 'Игрушки для мальчиков',
		'Игровые наборы для мальчиков' => 'Игрушки для мальчиков',
		'Фигурки героев' => 'Игрушки для мальчиков',

        'Водяное оружие' => 'Детское оружие',
		'Автоматы на пульках' => 'Детское оружие',
		'Арбалеты, луки' => 'Детское оружие',
		'Пистолеты' => 'Детское оружие',
		'Боеприпасы' => 'Детское оружие',
		'Оружие с мягкими пулями' => 'Детское оружие',

        'Пластиковые головоломки' => 'Головоломки',
		'Металлические головоломки' => 'Головоломки',
		'Деревянные головоломки' => 'Головоломки',
		'Кубик рубика' => 'Головоломки',

        'Конструкторы с мелкими деталями' => 'Конструкторы',
		'Конструкторы с крупными деталями' => 'Конструкторы',
		'Конструктор липучка'  => 'Конструкторы',
		'Магнитные конструкторы'  => 'Конструкторы',
		'Металлические конструкторы'  => 'Конструкторы',

        'Радиоуправляемые квадрокоптеры' => 'Радиоуправляемые модели',
		'Аксессуары' => 'Радиоуправляемые модели',
		'Радиоуправляемые вертолеты' => 'Радиоуправляемые модели',
		'Радиоуправляемые животные' => 'Радиоуправляемые модели',
		'Радиоуправляемые машины' => 'Радиоуправляемые модели',
		'Радиоуправляемые лодки' => 'Радиоуправляемые модели',
		'Радиоуправляемые роботы и трансформеры' => 'Радиоуправляемые модели',
		'Радиоуправляемые танки' => 'Радиоуправляемые модели',
		'Радиоуправляемый спецтранспорт' => 'Радиоуправляемые модели',

        'Настольные игры-головоломки' => 'Настольные игры',
		'Настольный бильярд' => 'Настольные игры',
		'Настольный футбол' => 'Настольные игры',
		'Настольный хоккей' => 'Настольные игры',
		'Карточные настольные игры' => 'Настольные игры',

        'Картины по номерам' => 'Наборы для творчества',
		'Кинетический песок' => 'Наборы для творчества',
		'Пластилин' => 'Наборы для творчества',
		'Рисование' => 'Наборы для творчества',
		'Рукоделие' => 'Наборы для творчества'
    ];

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
        $categoriesMapped = $result = $this->_images = $this->_specifications = [];
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
            $this->_specifications[static::CATEGORY_SPECIFICATION] = count($result);
            $categoriesMapped = ['Feature checkbox '.static::CATEGORY_SPECIFICATION];

            $result = array_map(function ($item) {
                return "Feature input ".$item;
            }, $result);

            for ($i = 0; $i < $pictureCount; $i++) {
                $this->_images[] = 'Image ' . ($i + 1);
            }
        }
        return array_merge(static::MANDATORY, $result, $categoriesMapped, $this->_images);
    }

    /**
     * @param array $categoriesList
     * @param int $categoryId
     * @return string
     */
    private function _getCategoryName($categoriesList, $categoryId) {
        $categoryName = !empty($categoriesList[$categoryId]) ? $categoriesList[$categoryId]['name'] : '';
        if (!empty(static::CATEGORIES_MAPPING[$categoryName])) {
            $categoryName = static::CATEGORIES_MAPPING[$categoryName];
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
                    $csvRow = array_fill(0, count($header) - 1, '');
                    $categoryName = $this->_getCategoryName($categoriesList, $row['CATEGORYID'][0]);
                    if (empty($categoryName)) {
                        continue;
                    }

                    $csvRow[static::BRAND] = empty($row['VENDOR']) ? Config::Common()->companyName : $row['VENDOR'][0];
                    $csvRow[static::CATEGORY] = $categoryName;
                    $csvRow[static::PRODUCT_NAME] = $row['NAME'][0];
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

                    if(!empty(static::CATEGORIES_MAPPING[$catSpecName])) {
                        $rowPosition = $this->_specifications[static::CATEGORY_SPECIFICATION] + count(self::MANDATORY);
                        $csvRow[$rowPosition] = "$catSpecName~0.00";
                    }

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