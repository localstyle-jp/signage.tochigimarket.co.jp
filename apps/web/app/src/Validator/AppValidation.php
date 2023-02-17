<?php
namespace App\Validator;

use Cake\Validation\Validation;

class AppValidation {
    public static function checkEmail($value, $context) {
        return (bool) preg_match('/\A[a-zA-Z0-9_-]([a-zA-Z0-9_\!#\$%&~\*\+-\/\=\.]+)*@[a-zA-Z0-9-]+(\.[a-zA-Z0-9-]+)*\.([a-zA-Z]{2,20})\z/', $value);
    }

    public static function checkPostcode($value, $context) {
        return (bool) preg_match('/[0-9]{3}-[0-9]{4}/', $value);
    }

    public static function checkTel($value, $context) {
        # return (bool) preg_match('/^(0\d{1,4}[\s-]?\d{1,4}[\s-]?\d{4})$/', $value);
        return (bool) preg_match('/^(0\d{1,4}-\d{1,4}-\d{4})$/', $value);
    }

    public static function checkPassword($value, $context) {
        return (bool) preg_match('/^[a-zA-Z0-9\-_\.@#%&]{8,16}$/', $value);
    }

    public static function checkPasswordRule($value, $context) {
        return (bool) preg_match('/^[a-zA-Z0-9\-_\.@#%&]{8,16}$/', $value);

        // $pattern = '/\A(?=\d{0,99}+[a-zA-Z\-_#\.])(?=[\-_#\.]{0,99}+[a-zA-Z\d])(?=[a-zA-Z]{0,99}+[\d\-_#\.])[a-zA-Z\d\-_#\.]{1,100}+\z/i';
        // if (preg_match($pattern, $value)) {
        //     return true;
        // } else {
        //     return false;
        // }
    }

    public static function checkDate($value, $context) {
        return self::_checkDate($value);
    }

    public static function checkDateTime($value, $context) {
        return self::_checkDateTime($value);
    }

    public static function checkBirthday($value, $context) {
        $r = self::checkDate($value, $context);

        if (is_array($value) && count($value) == 3) {
            $value = implode('-', $value);
        }

        if ($r) {
            $now = new \DateTime();
            if ($now->format('Y-m-d') == $value) {
                $r = false;
            }
        }

        return $r;
    }

    public static function _checkDate($value) {
        if ($value == DATE_ZERO || $value == '0000-00-00') {
            return false;
        }

        if (is_array($value) && count($value) == 3) {
            $value = implode('-', $value);
        }

        try {
            $dt = new \DateTime($value);
        } catch (Exception $e) {
            return false;
        }

        return true;
    }

    public static function _checkDateTime($value) {
        if ($value == DATETIME_ZERO || $value == '0000-00-00 00:00:00') {
            return false;
        }

        if (is_array($value) && count($value) == 3) {
            $value = implode('-', $value);
        }

        try {
            $dt = new \DateTime($value);
        } catch (Exception $e) {
            return false;
        }

        return true;
    }

    public static function isUnique($value, $context, $mode = '') {
        $field = $context['field'];
        $table = $context['providers']['table'];

        $id = 0;
        if (array_key_exists('id', $context['data'])) {
            $id = $context['data']['id'];
        }

        $now = new \DateTime();

        $cond = [
            $table->getAlias() . '.id !=' => $id,
            $table->getAlias() . ".{$field}" => $value,
        ];
        if ($mode == 'publishOnly') {
            $cond[$table->geAlias() . '.status'] = 'publish';
        }

        $count = $table->find()->where($cond)->count();

        if ($count == 0) {
            return true;
        } else {
            return false;
        }
    }

    public static function checkExpired($value, $context) {
        $now = new \DateTime();
        $dt = new \DateTime($value);

        if ($dt->format('YmdHi') < $now->format('YmdHi')) {
            return false;
        }

        return true;
    }

    public static function checkHira($value, $context) {
        if (preg_match('/^[ぁ-ゞー・． 　０-９]+$/u', $value)) {
            return true;
        } else {
            return false;
        }
    }
    public static function checkKatakana($value, $context) {
        if (preg_match('/^[ァ-ヾ0-9０-９ー、。 　]+$/u', $value)) {
            return true;
        } else {
            return false;
        }
    }
}
