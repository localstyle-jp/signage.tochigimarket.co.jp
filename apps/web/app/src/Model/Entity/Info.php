<?php

namespace App\Model\Entity;

class Info extends AppEntity
{
    const BLOCK_TYPE_TITLE = 1;
    const BLOCK_TYPE_TITLE_H4 = 5;
    const BLOCK_TYPE_CONTENT = 2;
    const BLOCK_TYPE_IMAGE = 3;
    const BLOCK_TYPE_FILE = 4;
    const BLOCK_TYPE_BUTTON = 8;
    const BLOCK_TYPE_LINE = 9;
    const BLOCK_TYPE_SECTION = 10;
    const BLOCK_TYPE_SECTION_WITH_IMAGE = 11;
    const BLOCK_TYPE_SECTION_FILE = 12;
    const BLOCK_TYPE_SECTION_RELATION = 13;
    const BLOCK_TYPE_RELATION = 14;
    
    const BLOCK_TYPE_LIST = [
        self::BLOCK_TYPE_TITLE => '小見出し(H3)',
        self::BLOCK_TYPE_TITLE_H4 => '小見出し(H4)',
        self::BLOCK_TYPE_CONTENT => '本文',
        self::BLOCK_TYPE_IMAGE => '画像',
        self::BLOCK_TYPE_SECTION_WITH_IMAGE => '画像回り込み用',
        self::BLOCK_TYPE_FILE => 'ファイル添付',
        self::BLOCK_TYPE_BUTTON => 'リンクボタン',
        self::BLOCK_TYPE_LINE => '区切り線',

    ];

    // 枠属性リスト
    const BLOCK_TYPE_WAKU_LIST = [
        self::BLOCK_TYPE_SECTION => '枠',        
        self::BLOCK_TYPE_SECTION_FILE => 'ファイル枠',
        self::BLOCK_TYPE_SECTION_RELATION => '関連記事',
    ];


    static $option_default_values = [
        // self::BLOCK_TYPE_SECTION_WITH_IMAGE => ''
    ];

    // 枠属性への侵入を除外するブロック
    static $out_waku_list = [
        self::BLOCK_TYPE_SECTION => [
            self::BLOCK_TYPE_RELATION,

            self::BLOCK_TYPE_SECTION,
            // self::BLOCK_TYPE_SECTION_WITH_IMAGE,
            self::BLOCK_TYPE_SECTION_FILE,
            self::BLOCK_TYPE_SECTION_RELATION,
        ],
        // self::BLOCK_TYPE_SECTION_WITH_IMAGE => [
        //     self::BLOCK_TYPE_IMAGE,
            
        //     self::BLOCK_TYPE_SECTION,
        //     self::BLOCK_TYPE_SECTION_WITH_IMAGE,
        //     self::BLOCK_TYPE_SECTION_FILE,
        //     self::BLOCK_TYPE_SECTION_RELATION
        // ],
        self::BLOCK_TYPE_SECTION_FILE => [
            self::BLOCK_TYPE_TITLE,
            self::BLOCK_TYPE_TITLE_H4,
            self::BLOCK_TYPE_CONTENT,
            self::BLOCK_TYPE_IMAGE,
            self::BLOCK_TYPE_BUTTON,
            self::BLOCK_TYPE_LINE,

            self::BLOCK_TYPE_SECTION,
            self::BLOCK_TYPE_SECTION_WITH_IMAGE,
            self::BLOCK_TYPE_SECTION_FILE,
            self::BLOCK_TYPE_SECTION_RELATION

        ],
        self::BLOCK_TYPE_SECTION_RELATION => [
            self::BLOCK_TYPE_TITLE,
            self::BLOCK_TYPE_TITLE_H4,
            self::BLOCK_TYPE_CONTENT,
            self::BLOCK_TYPE_IMAGE,
            self::BLOCK_TYPE_FILE,
            self::BLOCK_TYPE_BUTTON,
            self::BLOCK_TYPE_LINE,

            self::BLOCK_TYPE_SECTION,
            self::BLOCK_TYPE_SECTION_WITH_IMAGE,
            self::BLOCK_TYPE_SECTION_FILE,
            self::BLOCK_TYPE_SECTION_RELATION
        ]
    ];

    static function getBlockTypeList($type = 'normal') {
        if ($type == 'normal') {
            return self::BLOCK_TYPE_LIST;
        } elseif ($type == 'waku') {
            return self::BLOCK_TYPE_WAKU_LIST;
        }
    }

    static $font_list = [
        'font_style_1' => '明朝',
        'font_style_2' => 'ゴシック',
        'font_style_3' => '丸ゴシック'
    ];

    static $line_style_list = [
        'line_style_1' => '線',
        'line_style_2' => '二重線',
        'line_style_3' => '破線',
        'line_style_4' => '点線'
    ];

    static $line_color_list = [
        'line_color_1' => '赤',
        'line_color_2' => '緑',
        'line_color_3' => 'オレンジ',
        'line_color_4' => '青'
    ];

    static $line_width_list = [
        '1' => '1px',
        '2' => '2px',
        '3' => '3px',
        '4' => '4px',
        '5' => '5px',
        '6' => '6px',
        '7' => '7px',
        '8' => '8px',
        '9' => '9px',
        '10' => '10px'
    ];


    static $waku_color_list = [
        'waku_color_1' => '赤',
        'waku_color_2' => '緑',
        'waku_color_3' => 'オレンジ',
        'waku_color_4' => '青'
    ];

    static $waku_style_list = [
        'waku_style_1' => '線',
        'waku_style_2' => '破線',
        'waku_style_3' => '点線',
        'waku_style_4' => '二重線',
        'waku_style_5' => '上下のみ',
        'waku_style_6' => '影付き'
    ];

    static $button_color_list = [
        'button_color_1' => '赤',
        'button_color_2' => '緑',
        'button_color_3' => 'オレンジ',
        'button_color_4' => '青',
        'button_color_5' => 'グレー',
    ];


    static $content_liststyle_list = [
        'liststyle_1' => '中点',
        'liststyle_2' => 'チェック',
        'liststyle_3' => '＞',
    ];

    static $link_target_list = [
        '_self' => '現在のウインドウ',
        '_blank' => '新しいウインドウ'
    ];

    static $week_strings = [
        '0' => 'SUN',
        '1' => 'MON',
        '2' => 'THE',
        '3' => 'WED',
        '4' => 'THU',
        '5' => 'FRI',
        '6' => 'SAT'
    ];

    protected function _setMetaDescription($value) {
        return strip_tags(str_replace("\n", '', $value));
    }

    // protected function _setMetaKeywords($value) {
    //     if (array_key_exists('keywords', $this->_properties)) {
    //         $value = implode(",", array_values($this->properties['keywords']));

    //     }
        
    //     return $value;
    // }

    protected $_virtual = ['keywords'];

    protected function _getKeywords($value) {
        if (!array_key_exists('meta_keywords', $this->_properties)) {
            return '';
        }
        $values = explode(',',$this->_properties['meta_keywords']);

        return $values;

    }

    static function getWeekStr($w) {
        if (array_key_exists($w, self::$week_strings)) {
            return self::$week_strings[$w];
        }

        return '';
    }
}
