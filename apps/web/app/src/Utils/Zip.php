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
        error_log("[DEBUG] Zip.make started - dest: {$dest}");
        error_log("[DEBUG] Data items count: " . count($datas));
        
        set_time_limit(0);

        // 作業用の一時ディレクトリを作成（最終保存先と同じ場所）
        $tempDir = dirname($dest);
        $workDir = $tempDir . '/zip_work_' . uniqid();
        
        if (!mkdir($workDir, 0777, true)) {
            error_log("[ERROR] Failed to create work directory: {$workDir}");
            return false;
        }
        
        error_log("[DEBUG] Created work directory: {$workDir}");

        // プログレス処理用
        $totalItems = count($datas);
        $processedItems = 0;

        try {
            // ファイルを作業ディレクトリにコピー/作成
            foreach ($datas as $index => $data) {
                $filename = $data['name'] ?? '';
                $filedata = $data['data'] ?? '';
                
                error_log("[DEBUG] Processing item {$index}: {$filename}");

                $targetPath = $workDir . '/' . $zipname . '/' . $filename;
                $targetDir = dirname($targetPath);
                
                // ディレクトリ作成
                if (!is_dir($targetDir)) {
                    mkdir($targetDir, 0777, true);
                }

                // $filedata が配列でない場合はスキップ
                if (!is_array($filedata)) {
                    error_log("[ERROR] Invalid filedata format for: {$filename}");
                    continue;
                }

                // テキストコンテンツ
                if ($text = $filedata['content'] ?? '') {
                    error_log("[DEBUG] Writing text content for: {$filename}");
                    file_put_contents($targetPath, $text);
                }
                // ファイルコピー
                elseif ($file = $filedata['path'] ?? '') {
                    // ファイルパスが実際にファイルかチェック
                    if (is_dir($file)) {
                        error_log("[ERROR] Path is a directory, not a file: {$file}");
                        continue;
                    }
                    if (!file_exists($file)) {
                        error_log("[ERROR] Source file does not exist: {$file}");
                        continue;
                    }
                    if (!is_readable($file)) {
                        error_log("[ERROR] Source file is not readable: {$file}");
                        continue;
                    }
                    error_log("[DEBUG] Copying file: {$file} -> {$targetPath}");
                    copy($file, $targetPath);
                }

                // プログレス更新
                $processedItems++;
                if ($this->updateProgress) {
                    $progress = ($processedItems / $totalItems) * 90; // 90%まで（残り10%はzip作成）
                    ($this->updateProgress)($progress);
                }
            }

            // システムのzipコマンドを使用してZIPファイルを作成
            error_log("[DEBUG] Creating ZIP file using system zip command");
            
            // 既存のZIPファイルを削除
            if (file_exists($dest)) {
                unlink($dest);
            }
            
            // zipコマンドを実行（-rは再帰的、-q は静かに、-9は最高圧縮）
            $zipCmd = sprintf(
                'cd %s && zip -r -q -9 %s %s 2>&1',
                escapeshellarg($workDir),
                escapeshellarg(basename($dest)),
                escapeshellarg($zipname)
            );
            
            error_log("[DEBUG] Executing: {$zipCmd}");
            exec($zipCmd, $output, $returnCode);
            
            if ($returnCode !== 0) {
                error_log("[ERROR] zip command failed with code {$returnCode}: " . implode("\n", $output));
                return false;
            }
            
            // ZIPファイルを最終的な場所に移動
            $tempZipPath = $workDir . '/' . basename($dest);
            if (!file_exists($tempZipPath)) {
                error_log("[ERROR] ZIP file was not created: {$tempZipPath}");
                return false;
            }
            
            if (!rename($tempZipPath, $dest)) {
                error_log("[ERROR] Failed to move ZIP file to destination");
                return false;
            }
            
            $filesize = filesize($dest);
            error_log("[DEBUG] Zip file created successfully: {$dest}, size: {$filesize} bytes");

            $this->finishedProgress();
            return true;
            
        } finally {
            // 作業ディレクトリのクリーンアップ
            error_log("[DEBUG] Cleaning up work directory");
            $this->removeDirectory($workDir);
        }
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
        $zip = new \ZipArchive();

        set_time_limit(0);

        $tmpZipPath = '/tmp/' . $this->getRandomStr() . '.zip';
        if (file_exists($tmpZipPath)) {
            unlink($tmpZipPath);
        }

        if ($zip->open($tmpZipPath, \ZipArchive::CREATE) === false) {
            throw new \Exception("failed to create zip file. ${tmpZipPath}");
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
            throw new \Exception("failed to close zip file. ${tmpZipPath}");
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
    }

    // ディレクトリを再帰的に削除
    private function removeDirectory($dir) {
        if (!is_dir($dir)) {
            return;
        }
        
        $files = array_diff(scandir($dir), ['.', '..']);
        foreach ($files as $file) {
            $path = $dir . '/' . $file;
            if (is_dir($path)) {
                $this->removeDirectory($path);
            } else {
                unlink($path);
            }
        }
        rmdir($dir);
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
