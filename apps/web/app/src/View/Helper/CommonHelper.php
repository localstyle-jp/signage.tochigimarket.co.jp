<?php
namespace App\View\Helper;

use Cake\Datasource\ModelAwareTrait;
use App\Model\Entity\User;
use App\Lib\Util;

class CommonHelper extends AppHelper {
    use ModelAwareTrait;

    public function session_read($key) {
        return $this->getView()->getRequest()->getSession()->read($key);
    }
    public function session_check($key) {
        return $this->getView()->getRequest()->getSession()->check($key);
    }

    public function getCategoryEnabled() {
        return CATEGORY_FUNCTION_ENABLED;
    }

    public function getCategorySortEnabled() {
        return CATEGORY_SORT;
    }

    public function isCategoryEnabled($page_config) {
        if (!$this->getCategoryEnabled()) {
            return false;
        }

        if (empty($page_config)) {
            return false;
        }

        if ($page_config->is_category == 'Y') {
            return true;
        }

        return false;
    }

    public function isCategorySort($page_config_id) {
        $this->modelFactory('Table', ['Cake\ORM\TableRegistry', 'get']);
        $this->loadModel('PageConfigs');

        if (!CATEGORY_SORT) {
            return false;
        }
        $page_config = $this->PageConfigs->find()->where(['PageConfigs.id' => $page_config_id])->first();

        if (empty($page_config)) {
            return false;
        }

        if ($page_config->is_category_sort == 'Y') {
            return true;
        }

        return false;
    }

    public function isViewSort($page_config, $category_id = 0) {
        if ($this->getCategoryEnabled() && $page_config->is_category === 'Y'
             && ($this->isCategorySort($page_config->id)) || (!$this->isCategorySort($page_config->id) && !$category_id)) {
            return true;
        }

        return false;
    }

    public function getUserRole() {
        return $this->session_read('user_role');
    }

    public function isUserRole($role_key, $isOnly = false) {
        $role = $this->session_read('user_role');

        if (intval($role) === 0) {
            $res = 'develop';
        } elseif ($role < 10) {
            $res = 'admin';
        } elseif ($role < 20) {
            $res = 'staff';
        } elseif ($role < 30) {
            $res = 'shop';
        } elseif ($role >= 90) {
            $res = 'demo';
        }
        /** 必要に応じて追加 */
        else {
            $res = 'staff';
        }

        if (!$isOnly) {
            if ($role_key == 'admin') {
                $role_key = array('develop', 'admin');
            } elseif ($role_key == 'staff') {
                $role_key = array('develop', 'admin', 'staff');
            } elseif ($role_key == 'shop') {
                $role_key = ['develop', 'admin', 'staff', 'shop'];
            }
        }

        if (in_array($res, (array)$role_key)) {
            return true;
        } else {
            return false;
        }
    }

    public function getuserRoleKey() {
        $role = $this->getUserRole();

        $key = User::$role_key_list[$role];

        return $key;
    }

    public function Round($number, $decimal = 0, $type = 1) {
        return Util::Round($number, $decimal, $type);
    }

    public function getAdminMenu() {
        return $this->session_read('admin_menu.menu_list');
    }
}
