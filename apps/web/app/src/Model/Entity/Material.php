<?php

namespace App\Model\Entity;

class Material extends AppEntity
{

    const TYPE_IMAGE = 1;
    const TYPE_MOVIE = 2;
    const TYPE_URL = 3;
    const TYPE_PAGE = 4;

    static $type_list = [
        self::TYPE_IMAGE => '画像',
        self::TYPE_MOVIE => '動画',
        self::TYPE_URL => 'URL',
        self::TYPE_PAGE => 'ページ'
    ];
}
