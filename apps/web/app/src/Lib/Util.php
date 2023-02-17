<?php

namespace App\Lib;

class Util {
    /**
     * 端数処理
     * @param [type] $value [description]
     */
    public static function Round($number, $decimal = 0, $type = 1) {
        $res = $number;
        // 四捨五入
        if ($type == 0) {
            $res = round($number, $decimal);
        // 切り捨て
        } elseif ($type == 1) {
            $rate = 1;
            if ($decimal > 0) {
                for ($i = 0; $i < $decimal; $i++) {
                    $rate = $rate * 10;
                }
                $number = $number * $rate;
            }
            $res = floor($number);

            if ($decimal > 0) {
                $rate = 1;
                for ($i = 0;$i < $decimal;$i++) {
                    $rate = $rate * 0.1;
                }
                $res = $res * $rate;
            }
        // 切り上げ
        } elseif ($type == 2) {
            $rate = 1;
            if ($decimal > 0) {
                for ($i = 0; $i < $decimal; $i++) {
                    $rate = $rate * 10;
                }
                $number = $number * $rate;
            }
            $res = ceil($number);

            if ($decimal > 0) {
                $rate = 1;
                for ($i = 0;$i < $decimal;$i++) {
                    $rate = $rate * 0.1;
                }
                $res = $res * $rate;
            }
        }

        return $res;
    }

    public static function wareki($date) {
        $ymd = (new \DateTime($date))->format('Ymd');
        $y = (new \DateTime($date))->format('Y');

        if ($ymd >= '20190501') {
            $ret = array(
                'era' => '令和',
                'short' => '令',
                'alphabet' => 'R',
                'year' => $y - 2019 + 1
            );
        } elseif ($ymd >= '19890108') {
            $ret = array(
                'era' => '平成',
                'short' => '平',
                'alphabet' => 'H',
                'year' => $y - 1989 + 1
            );
        } else {
            $ret = array(
                'era' => '昭和',
                'short' => '昭',
                'alphabet' => 'S',
                'year' => $y - 1926 + 1
            );
        }

        if ($ret['year'] == 1) {
            $ret['year'] = '元';
        }

        return $ret;
    }
}
