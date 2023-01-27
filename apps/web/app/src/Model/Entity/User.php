<?php

namespace App\Model\Entity;

use Cake\Auth\DefaultPasswordHasher;

class User extends AppEntity {
    const ROLE_DEVELOP = 0;
    const ROLE_ADMIN = 1;
    const ROLE_CMS = 10;
    const ROLE_SHOP = 20;
    const ROLE_DEMO = 90;

    public static $role_list = [
        self::ROLE_DEVELOP => '開発者',
        self::ROLE_ADMIN => 'システム管理',
        self::ROLE_CMS => 'CMS登録権限',
        self::ROLE_SHOP => 'SHOP'
    ];

    public static $role_key_list = [
        self::ROLE_DEVELOP => 'develop',
        self::ROLE_ADMIN => 'admin',
        self::ROLE_CMS => 'staff',
        self::ROLE_SHOP => 'shop'
    ];

    public static $role_key_values = [
        'admin' => self::ROLE_ADMIN,
        'staff' => self::ROLE_CMS,
        'cms' => self::ROLE_CMS,
        'shop' => self::ROLE_SHOP
    ];

    public static $status_list = [
        'publish' => '利用中',
        'draft' => '停止中'
    ];

    protected function _setPassword($password) {
        return (new DefaultPasswordHasher)->hash($password);
    }

    protected function _getListName() {
        return "{$this->_properties['name']}({$this->_properties['username']})";
    }
}
