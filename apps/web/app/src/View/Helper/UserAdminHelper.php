<?php
namespace App\View\Helper;

class UserAdminHelper extends AppHelper {
    public static $adminMenu = [
        'main' => [
            'マスタ管理' => [
                // １行目
                [
                    'ユーザー管理' => '/admin/users',
                    'サイト管理' => '/admin/site-configs',
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

    public static $userMenu = [
        'admin' => [
            'メインメニュー' => [
                // 'ブース管理' => [
                // ],
                // // １行目
                // '動画管理' => [
                // ],
                ' ' => [
                    '素材' => [
                        'icon' => '',
                        'link' => '/materials/'
                    ],
                    'コンテンツ' => [
                        'icon' => '',
                        'link' => '/contents/'
                    ],
                    '表示端末' => [
                        'icon' => '',
                        'link' => '/machine-boxes/'
                    ],
                ],
                // 2行目
                // '  ' => [
                //     'レポート出力' => [
                //         'icon' => 'far fa-clipboard',
                //         'link' => '/reports/'
                //     ]
                // ]
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
        'shop' => [
            'メインメニュー' => [
                ' ' => [
                    '素材' => [
                        'icon' => '',
                        'link' => '/shop/materials'
                    ]
                ]
            ]
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
        ],
        'side_shop' => [
            '管理' => [
                [
                    'title' => '素材',
                    'link' => '/shop_user/materials',
                    'icon' => 'nav-icon fas fa-image'
                ],
                [
                    'title' => 'コンテンツ管理',
                    'link' => '/shop_user/contents',
                    'icon' => 'nav-icon far fa-copy'
                ]
            ],
            '表示端末' => [
                [
                    'title' => 'コンテンツ切替',
                    'link' => '/shop_user/machine-boxes',
                    'icon' => ''
                ],
                [
                    'title' => '現在のコンテンツ編集',
                    'link' => '/shop_user/machine',
                    'icon' => ''
                ]
            ]
        ]
    ];

    public function getUserMenu($type = 'main') {
        if ($type == 'develop') {
            $type = 'admin';
        }
        return self::$userMenu[$type];
    }
    public function getAdminMenu($type = 'main') {
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
