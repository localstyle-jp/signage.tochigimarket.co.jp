<?php

namespace App\Model\Entity;

class ImportConfig extends AppEntity
{

    // import_detailsのカラム名
    const ROW_ARTIST_NAME = 'artist_name';
    const ROW_MUSIC_TITLE = 'music_name';
    const ROW_COMPANY_NAME = 'company_name';
    const ROW_PRICE = 'price';
    const ROW_DL_COUNT = 'dl_count';
    const ROW_AMOUNT = 'amount';
    const ROW_YM = 'ym';
    const ROW_ISRC = 'isrc';

    // 取込をする項目
    static $rows = [
        self::ROW_ARTIST_NAME => 'アーティスト名',
        self::ROW_MUSIC_TITLE => '楽曲名',
        self::ROW_PRICE => '単価',
        self::ROW_DL_COUNT => 'DL数',
        self::ROW_AMOUNT => '売上額',
        self::ROW_YM => '配信年月',
        self::ROW_ISRC => 'ISRC'
    ];

    const FIXED_COUNT = 2;

    static $display_rows = [
        self::ROW_ARTIST_NAME => [
            'title' => 'アーティスト名',
            'id_name' => 'artist_id',
            'th_class' => 'w-250px',
            'link' => 'artists_edit',
            'link_id' => 'artist_id'
        ],
        self::ROW_MUSIC_TITLE => [
            'title' => '楽曲名',
            'id_name' => 'music_id',
            'th_class' => 'w-250px',
            'link' => 'musics_edit',
            'link_id' => 'music_id'
        ],
        self::ROW_ISRC => [
            'title' => 'ISRC',
            'th_class' => 'w-200px',
            'id_name' => 'isrc_registed',
            'link' => 'musics_edit',
            'link_id' => 'music_id'
        ],
        self::ROW_DL_COUNT => [
            'title' => 'DL数',
            'th_class' => 'w-50px',
            'td_class' => 'text-right',
            'link' => ''
        ]
    ];

    protected function _setPrice($value) {
        if (!is_numeric($value)) {
            $value = 0;
        }

        return $value;
    }
    protected function _setDlCount($value) {
        if (!is_numeric($value)) {
            $value = 0;
        }

        return $value;
    }
    protected function _setAmount($value) {
        if (!is_numeric($value)) {
            $value = 0;
        }

        return $value;
    }

}
