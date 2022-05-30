<?php

namespace App\Model\Entity;

class Material extends AppEntity
{

    const TYPE_IMAGE = 1;
    const TYPE_MOVIE = 2;
    const TYPE_URL = 3;
    const TYPE_PAGE = 4;
    const TYPE_MOVIE_MP4 = 5;
    const TYPE_PAGE_MOVIE = 6;
    const TYPE_MOVIE_WEBM = 7;
    const TYPE_SOUND = 8;

    static $type_list = [
        self::TYPE_IMAGE => '画像',
        // self::TYPE_MOVIE => 'YouTube',
        self::TYPE_MOVIE_MP4 => 'mp4',
        // self::TYPE_MOVIE_WEBM => 'webm',
        self::TYPE_URL => 'URL',
        self::TYPE_SOUND => '音楽',
        // self::TYPE_PAGE_MOVIE => '背景画像入り動画',
        // self::TYPE_PAGE => 'ページ',
    ];
    static $validation_list = [
        self::TYPE_IMAGE => 'image',
        // self::TYPE_MOVIE => 'movie',
        self::TYPE_MOVIE_MP4 => 'mp4',
        // self::TYPE_URL => 'webm',
        self::TYPE_URL => 'url',
        self::TYPE_SOUND => 'sound',
        // self::TYPE_PAGE => 'page'
    ];

    static $type_list_api = [
        self::TYPE_IMAGE => 'image',
        self::TYPE_MOVIE_MP4 => 'mp4',
        self::TYPE_URL => 'webpage',
    ];
}
