<?php 
namespace App\View\Helper;

use Cake\View\Helper\HtmlHelper;
use App\Model\Entity\Document;

class MyHtmlHelper extends HtmlHelper
{
    public function getFullUrl($url) {
        $host = $this->Url->build('/', true);

        if (substr($url, 0, 1) == '/') {
            $url = substr($url, 1);
        }

        return $host . $url;
    }

    public function view($val, $options = array()) {

        $options = array_merge(array('before' => '',
                               'after' => '',
                               'default' => '',
                               'empty' => '',
                               'nl2br' => false,
                               'h' => true,
                               'emptyIsZero' => false,
                               'price_format' => false,
                               'decimal' => 2 //price_format=true時の小数点以下桁数
                           ),
                               $options);
        extract($options);

        if ($emptyIsZero && intval($val) === 0) {
            $val = "";
        }

        if ($val && $price_format) {
            $cost = $val;
            $cost = number_format($cost, $decimal);  // 1,234.50
            $cost = (preg_match('/\./', $cost)) ? preg_replace('/\.?0+$/', '', $cost) : $cost; // 末尾の０は消す
            $val = $cost;
        }

        if ($val != "") {
            if ($h) {
                $val = h($val);
            }
            if ($nl2br) {
                $val = nl2br($val);
            }
            return $before.$val.$after;
        }

        return $default.$empty;
    }

    public function filesize($size) {
      $size = max(0, (int)$size);
      $units = array( 'B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');
      $power = $size > 0 ? floor(log($size, 1024)) : 0;
      return number_format($size / pow(1024, $power), 2, '.', ',') . $units[$power];
    }

    public function getFileAppName($extension) {
        $list = Document::$extensions;

        if (array_key_exists($extension, $list)) {
            return $list[$extension];
        }

        return '';
    }

    public function free_space() {
      $free = disk_free_space('/');

      return $this->filesize($free);
    }
}