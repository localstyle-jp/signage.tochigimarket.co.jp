<?php
namespace App\Validator;

use Cake\Validation\Validation;
use App\Model\Table\PageConfigsTable;

class PageConfigValidation {
    public static function isUnique($value, $context) {
        $field = $context['field'];
        $table = $context['providers']['table'];

        $id = 0;
        if (array_key_exists('id', $context['data'])) {
            $id = $context['data']['id'];
        }
        $cond = [
            'PageConfigs.id !=' => $id,
            "PageConfigs.{$field}" => $value,
            'PageConfigs.site_config_id' => $context['data']['site_config_id']
        ];

        $count = $table->find()->where($cond)->count();

        if ($count == 0) {
            return true;
        } else {
            return false;
        }
    }

    public static function ngSlugName($value, $context) {
        $words = ['home', 'index', 'edit', 'logout', 'login', 'detail'];

        if (in_array($value, $words, true)) {
            return false;
        }
        return true;
    }

    public static function checkPasswordRule($value, $context) {
    }

    public static function checkName($value, $context) {
        if (preg_match('/^[a-zA-Z][a-zA-Z0-9_]{2,29}$/', $value)) {
            return true;
        } else {
            return false;
        }
    }
}
