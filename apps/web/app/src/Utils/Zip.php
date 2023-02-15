<?php
namespace App\Utils;

use Cake\ORM\TableRegistry;
use Cake\I18n\Date;
use Cake\Utility\Hash;

class Zip {
    public $updateProgress;
    public $getVersion;

    //
    public function addProgressEvent($updateProgress = null, $getVersion = null) {
        $this->updateProgress = $updateProgress;
        $this->getVersion = $getVersion;
    }

    //大文字小文字数字を含めたトークン
    public static function getRandomStr($length = 20) {
        $result = '';
        $str = array_merge(range('a', 'z'), range('A', 'Z"'), range('0', '9'));
        for ($i = 0; $i < $length; $i++) {
            $result .= $str[rand(0, count($str) - 1)];
        }
        return $result;
    }

    /**
    *
    * ZIPをどこかに保存する
    *
    * @param datas = 下述
    * @param zipname = String
    *
    */
    public function make($datas, $zipname, $dest) {
        // $datas = [
        //     [
        //         'name' => '/〇〇/filename',
        //         'data' => [
        //             'path' => '/〇〇/filename.pdf', // 基本これだけでいい
        //             'type' => 'json', // 使ってない
        //             'content' => 'テキストです', // 指定するとテキストファイルになる
        //         ]
        //     ]
        // ];

        $zip = new \ZipArchive();

        set_time_limit(0);

        //$zipname = mb_convert_encoding( $zipname, 'SJIS-WIN', 'UTF-8' );
        $tmpZipPath = $dest;
        if (file_exists($tmpZipPath)) {
            unlink($tmpZipPath);
        }

        if ($zip->open($tmpZipPath, \ZipArchive::CREATE) === false) {
            throw new IllegalStateException("failed to create zip file. ${tmpZipPath}");
        }

        // プログレス処理
        $zip = $this->progress($zip);

        foreach ($datas as $_ => $data) {
            $filename = $data['name'] ?? '';
            $filedata = $data['data'] ?? [];

            $zip_filepath = $zipname . '/' . $filename;
            $zip_filepath = mb_convert_encoding($zip_filepath, 'SJIS-WIN', 'UTF-8');

            // テキスト
            if ($text = $filedata['content'] ?? '') {
                $zip->addFromString($zip_filepath, $text);
            }
            // ファイル指定
            if ($file = $filedata['path'] ?? '') {
                $zip->addFile($file, $zip_filepath);
            }
        }

        if ($zip->close() === false) {
            $this->clearProgress();
            throw new IllegalStateException("failed to close zip file. ${tmpZipPath}");
        }

        // 確認
        if (!file_exists($tmpZipPath)) {
            return false;
        }

        $this->finishedProgress();

        // $finishToDownloda = function ($tmp, $finishedProgress) use ($name) {
        //     $finishedProgress();
        //     return $this->downloadZip($tmp, $name);
        // };

        // $finished = function ($tmp, $finishedProgress) use ($dest) {
        //     if (rename($tmp, $dest)) {
        //         $finishedProgress();
        //         return true;
        //     }
        // };
    }

    /**
    *
    * ZIP出力
    *
    * @param datas = 下述
    * @param zipname = String
    *
    */
    public function output($datas, $zipname, $finished) {
        // $datas = [
        //     [
        //         'name' => '/〇〇/filename',
        //         'data' => [
        //             'path' => '/〇〇/filename.pdf', // 基本これだけでいい
        //             'type' => 'json', // 使ってない
        //             'content' => 'テキストです', // 指定するとテキストファイルになる
        //         ]
        //     ]
        // ];

        $zip = new \ZipArchive();

        set_time_limit(0);

        //$zipname = mb_convert_encoding( $zipname, 'SJIS-WIN', 'UTF-8' );
        $tmpZipPath = '/tmp/' . $this->getRandomStr() . '.zip';
        if (file_exists($tmpZipPath)) {
            unlink($tmpZipPath);
        }

        if ($zip->open($tmpZipPath, \ZipArchive::CREATE) === false) {
            throw new IllegalStateException("failed to create zip file. ${tmpZipPath}");
        }

        // プログレス処理
        $zip = $this->progress($zip);

        foreach ($datas as $_ => $data) {
            $filename = $data['name'] ?? '';
            $filedata = $data['data'] ?? [];

            $zip_filepath = $zipname . '/' . $filename;
            $zip_filepath = mb_convert_encoding($zip_filepath, 'SJIS-WIN', 'UTF-8');

            // テキスト
            if ($text = $filedata['content'] ?? '') {
                $zip->addFromString($zip_filepath, $text);
            }
            // ファイル指定
            if ($file = $filedata['path'] ?? '') {
                $zip->addFile($file, $zip_filepath);
            }
        }

        if ($zip->close() === false) {
            throw new IllegalStateException("failed to close zip file. ${tmpZipPath}");
        }

        // 確認
        if (!file_exists($tmpZipPath)) {
            return false;
        }

        // finished
        if ($finished) {
            $finishedEvent = function () {
                $this->finishedProgress();
            };
            $finished($tmpZipPath, $finishedEvent);
        }

        // $finishToDownloda = function ($tmp, $finishedProgress) use ($name) {
        //     $finishedProgress();
        //     return $this->downloadZip($tmp, $name);
        // };

        // $finished = function ($tmp, $finishedProgress) use ($dest) {
        //     if (rename($tmp, $dest)) {
        //         $finishedProgress();
        //         return true;
        //     }
        // };
    }

    // プログレス処理
    public function progress($zip) {
        $updateProgress = $this->updateProgress;
        $getVersion = $this->getVersion;
        if (!$updateProgress && !$getVersion) {
            return $zip;
        }

        $version = $getVersion ? $getVersion() : 0;
        $zip->registerProgressCallback(0.02, function ($rate) use ($version, $updateProgress, $getVersion) {
            // 自分のZIPバージョンと現在のZIPバージョンが異なれば、中止する。
            if ($getVersion) {
                if ($version != $getVersion()) {
                    // 中止する
                    $zip->registerCancelCallback(function () {
                        return true;
                    });
                }
            }

            // 途中経過を渡す
            if ($updateProgress) {
                $updateProgress($rate * 100);
            }
        });
        return $zip;
    }

    // プログレス終了処理
    public function finishedProgress() {
        if ($updateProgress = $this->updateProgress) {
            $updateProgress(100);
        }
    }

    // プログレス終了処理
    public function clearProgress() {
        if ($updateProgress = $this->updateProgress) {
            $updateProgress(0);
        }
    }
}
