<?php

namespace Classes\RoyalToys;

/**
 * Class FileDiff
 * @package Classes\RoyalToys
 */
class FileDiff
{
    /**
     * @var string
     */
    private $_oldFile;

    /**
     * @var string
     */
    private $_newFile;

    /**
     * FileDiff constructor.
     * @param $oldFile
     * @param $newFile
     */
    public function __construct($oldFile, $newFile)
    {
        $this->_oldFile = $oldFile;
        $this->_newFile = $newFile;
    }

    /**
     * @param string $resultFile
     */
    public function diff($resultFile) {
        $old = fopen($this->_oldFile, 'rb');
        $new = fopen($this->_newFile, 'rb');
        copy($this->_newFile, $resultFile);
        $result = fopen($resultFile, 'ab');

        $names = [];

        while ($row = fgetcsv($new, 20000)) {
            $names[$row[2]] = 1;
        }

        while ($row = fgetcsv($old, 20000)) {
            if (empty($names[$row[2]]) && $row[6] != 0) {
                fputcsv($result, [
                    $row[0],
                    $row[1],
                    $row[2],
                    $row[3],
                    $row[4],
                    $row[5],
                    '0',
                    '0',
                    $row[8],
                ]);
            }
        }
        fclose($result);
        fclose($new);
        fclose($old);
    }

}