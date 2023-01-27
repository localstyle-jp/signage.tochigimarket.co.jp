<?php
namespace App\Controller\Component;

use Cake\Controller\Component;
use Cake\Controller\ComponentRegistry;
use Cake\Datasource\ModelAwareTrait;
use Cake\Utility\Inflector;
use Cake\ORM\TableRegistry;
use Cake\Utility\Text;
use App\Model\Entity\User;

/**
 * OutputHtml component
 */
class AdminMenuComponent extends Component {
    public $menu_list = [];

    public function initialize(array $config) {
        $this->Controller = $this->_registry->getController();
        $this->Session = $this->Controller->getRequest()->getSession();
    }

    public function init() {
        if ($this->Session->check('admin_menu.menu_list')) {
            $this->menu_list = $this->Session->read('admin_menu.menu_list');
        } else {
            $this->menu_list = [
                'main' => [
                    // [
                    //     'title' => 'コンテンツ',
                    //     'role' => [ 'role_type' => 'staff'],
                    //     'buttons' => $this->setContent()
                    // ],
                    [
                        'title' => __('コンテンツ'),
                        'role' => ['role_type' => 'staff'],
                        'buttons' => [
                            [ // １行目
                                ['name' => __('スポット'), 'link' => '/user_admin/infos/?page_slug=spot'],
                                ['name' => __('イベント'), 'link' => '/user_admin/infos/?page_slug=event'],
                            ],
                        ],
                        'footer' => [
                            // 'function' => 'getLastUpdate'
                        ]
                    ],
                    [
                        'title' => __('各種設定'),
                        'role' => ['role_type' => 'admin'],
                        'buttons' => [
                            [
                                ['name' => __('コンテンツ設定'), 'link' => '/user_admin/page-configs/'],
                                ['name' => __('定数管理'), 'link' => '/user_admin/mst-lists/', 'role' => ['role_type' => 'develop']],
                            ],
                        ]
                    ]
                ],
                'side' => [
                    // [
                    //     'title' => 'コンテンツ',
                    //     'role' => [ 'role_type' => 'staff' ],
                    //     'buttons' => $this->setContent('side')
                    // ],
                    [
                        'title' => __('管理'),
                        'role' => ['role_type' => 'shop', 'role_only' => true],
                        'buttons' => [
                            ['name' => __('素材'), 'link' => '/shop_user/materials', 'icon' => 'nav-icon fas fa-image'],
                            ['name' => __('コンテンツ管理'), 'link' => '/shop_user/contents', 'icon' => 'nav-icon far fa-copy'],
                        ],
                    ],
                    [
                        'title' => __('表示端末'),
                        'role' => ['role_type' => 'shop', 'role_only' => true],
                        'buttons' => [
                            ['name' => __('コンテンツ切替'), 'link' => '/shop_user/machine-boxes'],
                            ['name' => __('現在のコンテンツ編集'), 'link' => $this->setMyContent()],
                        ]
                    ]
                ]
            ];

            $this->Session->write('admin_menu.menu_list', $this->menu_list);
        }
    }

    public function setMyContent() {
        $this->MachineBoxes = TableRegistry::get('MachineBoxes');
        $this->MachineBoxesUsers = TableRegistry::get('MachineBoxesUsers');

        $user_id = $this->Session->read('userid');
        $machine_id = 0;
        // １アカウント（shop）につき１端末とする
        $machine_boxes = $this->MachineBoxesUsers->find()->where(['MachineBoxesUsers.user_id' => $user_id])->all();
        if (!$machine_boxes->isEmpty()) {
            foreach ($machine_boxes as $box) {
                $machine_id = $box->machine_box_id;
                break;
            }
        }

        $machine = $this->MachineBoxes->find()->where(['MachineBoxes.id' => $machine_id])->first();
        if (empty($machine)) {
            return '';
        }

        $link = '/shop_user/contents/edit/' . $machine->content_id . '?mode=machine';

        return $link;
    }

    public function setContent($type = 'main') {
        $this->PageConfigs = TableRegistry::get('PageConfigs');
        $this->Users = TableRegistry::get('Users');

        $content_buttons = [];
        $user_configs = [];
        $cond = [
            'parent_config_id' => 0
        ];

        // dd($cond);
        $page_configs = $this->PageConfigs->find()
                                          // ->where(['PageConfigs.site_config_id' => $current_site_id])
                                          ->where($cond)
                                          ->order(['PageConfigs.position' => 'ASC'])
                                          ->all();
        if (!empty($page_configs)) {
            $page_configs = $page_configs->toArray();
            if ($type == 'main') {
                $configs = array_chunk($page_configs, 3);

                foreach ($configs as $_) {
                    $menu = [];
                    foreach ($_ as $config) {
                        $menu[] = [
                            'name' => $config->page_title,
                            'link' => '/infos/?sch_page_id=' . $config->id
                        ];
                    }
                    $content_buttons[] = $menu;
                }
            } elseif ($type == 'side') {
                foreach ($page_configs as $config) {
                    $menu = [
                        'name' => $config->page_title,
                        'subMenu' => [
                            ['name' => __('新規登録'), 'link' => '/infos/edit/0?sch_page_id=' . $config->id],
                            ['name' => __('一覧'), 'link' => '/infos/?sch_page_id=' . $config->id],
                        ]
                    ];
                    $content_buttons[] = $menu;
                }
            }
        }

        return $content_buttons;
    }
}
