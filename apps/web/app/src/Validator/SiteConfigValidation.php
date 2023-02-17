<?php
namespace App\Validator;

use Cake\Validation\Validation;
use App\Model\Table\PageConfigsTable;

class SiteConfigValidation {
    public static function isUnique($value, $context) {
        $field = $context['field'];
        $table = $context['providers']['table'];

        $id = 0;
        if (array_key_exists('id', $context['data'])) {
            $id = $context['data']['id'];
        }
        $cond = [
            'SiteConfigs.id !=' => $id,
            "SiteConfigs.{$field}" => $value
        ];

        $count = $table->find()->where($cond)->count();

        if ($count == 0) {
            return true;
        } else {
            return false;
        }
    }

    public static function ngSlugName($value, $context) {
        $words = ['home', 'index', 'edit', 'logout', 'login', 'detail', 'admin', 'user', 'assets'];

        if (in_array($value, $words)) {
            return false;
        }
        return true;
    }

    public static function checkPasswordRule($value, $context) {
    }

    public static function checkName($value, $context) {
        if (preg_match('/^[a-zA-Z][a-zA-Z0-9_]{1,29}$/', $value)) {
            return true;
        } else {
            return false;
        }
    }
}
