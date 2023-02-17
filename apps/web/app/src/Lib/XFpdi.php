<?php
namespace App\Lib;

use setasign\Fpdi;
use setasign\Fpdi\TcpdfFpdi;

class XFpdi extends TcpdfFpdi {
    public function setSizeFont($str, $width, $font, $size = null, $min_size = null) {
        $r = true;
        $fontsizes = [
            // 文字数 => フォントサイズ
            27,
            24,
            18,
            14,
            12,
            11,
            10,
            9,
            8,
            7,
            6,
            5,
        ];

        if (is_null($size)) {
            $size = $this->defaultFontSize;
        }

        $max = count($fontsizes);
        $start = 0;
        if (!is_null($size)) {
            foreach ($fontsizes as $cnt => $_size) {
                if ($_size == $size) {
                    $start = $cnt;
                    break;
                }
            }
        }

        for ($i = $start; $i < $max; $i++) {
            $this->SetFont($font, '', $fontsizes[$i]);
            $w = $this->GetStringWidth($str);
            if ($w <= $width) {
                return $fontsizes[$i];
            }
            if (!is_null($min_size) && $fontsizes[$i] < $min_size) {
                $this->SetFont($font, '', $min_size);
                break;
            }
        }

        // 最終確認
        $w = $this->GetStringWidth($str);
        if ($w > $width) {
            $r = false;
        }

        return $r;
    }
}
