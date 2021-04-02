<?php 
namespace App\View\Helper;


class UserAdminHelper extends AppHelper
{

    static $adminMenu = [
            'main' => [
                'マスタ管理' => [
                    // １行目
                    [
                        'ユーザー管理' => '/admin/users',

                    ],
                    // 2行目
                    // [
                    // ]
                ],
                // 'マスタ管理' => [
                //     [
                //         '状況マスタ' => '/admin/mst_statuses'
                //     ]
                // ]
            ],
            'side' => [
                // '担当者管理' => [
                    // '一覧' => '/admin/staffs/',
                    // '新規登録' => '/admin/staffs/edit/0'
                // ]
            ]
        ];

    static $userMenu = [
            'admin' => [
                'メインメニュー' => [
                    // 'ブース管理' => [
                    // ],
                    // // １行目
                    // '動画管理' => [
                    // ],
                    ' ' => [
                        '媒体' => [
                            'icon' => 'fas fa-mobile-alt',
                            'link' => '/media/'
                        ],
                        'アーティスト' => [
                            'icon' => 'fas fa-users',
                            'link' => '/artists/'
                        ],
                        '楽曲' => [
                            'icon' => 'fas fa-music',
                            'link' => '/musics/'
                        ],
                        '報告先' => [
                            'icon' => 'far fa-building',
                            'link'=> '/companies/'
                        ],
                    ],
                    // 2行目
                    '  ' => [
                        'レポート出力' => [
                            'icon' => 'far fa-clipboard',
                            'link' => '/reports/'
                        ]
                    ]
                ],
                // '管理者メニュー' => [

                // ],
                // '設定' => [
                //     [
                //         // 'コンテンツ設定' => '/user/page-configs'
                //         '取込項目' => '/mst-import-names/'
                //     ]
                // ]
            ],
            'staff' => [
                'メインメニュー' => [
                    // 'ブース管理' => [
                    // ],
                    // // １行目
                    // '動画管理' => [
                    // ],
                    '商談資料管理' => [

                        '記事一覧' => '/user/folders/',
                    ],
                    // 2行目
                    // [
                    // ]
                ],
            ],
            'side_admin' => [
                '商談資料管理' => [
                    '一覧表示' => '/user/folders'
                ],
                'ユーザー管理' => [
                    'Webサイト利用者管理' => '/user/customers/',
                    'CMS利用者管理' => '/user/users/'
                ],
                'マスタ管理' => [
                    'Webサイト　部署' => '/user/departments/',
                    'CMS利用者　所属企業' => '/user/companies/',
                    '資料カテゴリ' => '/user/categories/'
                ]

                // '設定' => [
                //     'コンテンツ設定' => '/user/user-pages/'
                // ]
            ],
            'side_staff' => [
                '商談資料管理' => [
                    '一覧表示' => '/user/folders'
                ]
            ]
        ];

    public function getUserMenu($type='main') {
        return self::$userMenu[$type];
    }
    public function getAdminMenu($type='main') {
        return self::$adminMenu[$type];
    }

    public function getUsername() {
        $session = $this->request->getSession();

        return $session->read('data.username');
    }

    public function getName() {
        $session = $this->request->getSession();

        return $session->read('data.name');
    }


}