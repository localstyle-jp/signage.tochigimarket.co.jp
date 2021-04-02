<?php

namespace App\Model\Entity;

class Media extends AppEntity
{

    const IMPORT_TYPE_CSV = 1;
    const IMPORT_TYPE_EXCEL = 2;
    const IMPORT_TYPE_TSV = 3;
    static $import_type_list = [
        self::IMPORT_TYPE_CSV => 'CSV（カンマ区切り）',
        self::IMPORT_TYPE_TSV => 'TSV（タブ区切り）',
        self::IMPORT_TYPE_EXCEL => 'EXCEL'
    ];

    static $chara_list = [
        'UTF-8' => 'UTF-8',
        'SJIS' => 'SJIS',
        'UTF-16' => 'UTF-16',
    ];


    protected function _setRate($value) {
        $rate = $value * 100;
        if (array_key_exists('_rate', $this->_properties)) {
            $rate = ( $this->_properties['_rate'] * 100);
        }
        return $rate;
    }

    protected function _get_rate($value) {
        $rate = $value;
        if (array_key_exists('rate', $this->_properties)) {
            $rate = $this->_properties['rate'] * 0.01;
        }

        return $rate;
    }
}
