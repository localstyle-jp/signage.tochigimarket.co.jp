<?php

namespace App\Command;

use Cake\Console\Arguments;
use Cake\Console\Command;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOptionParser;

class ConvertMp4Command extends Command
{
    public $uploadDirCreate = true;
    public $uploadDirMask = 0777;
    public $uploadFileMask = 0666;

    //ffmpeg configure
    public $convertPath_mp4 = 'ffmpeg';
    
    protected function buildOptionParser(ConsoleOptionParser $parser)
    {
        $parser
            ->addArguments([
                'id' => ['required' => true],
                'basedir' => ['required' => true],
                'newdist' => ['required' => true],
                'name_mp4' => ['required' => true],
            ]);

        return $parser;
    }

    public function execute(Arguments $args, ConsoleIo $io)
    {   
        $id = $args->getArgument('id');
        $basedir = $args->getArgument('basedir');
        $newdist = $args->getArgument('newdist');
        $name_mp4 = $args->getArgument('name_mp4');

        $io->out((new \DateTime('now'))->format('Y/m/d H:i:s') . ' START ' . $basedir.$name_mp4 . ' -> ' . $newdist);
        // tsファイルへの分割
        $bitrates = [/*BITRATE_LOW, BITRATE_MID, */BITRATE_HIGH];
        foreach ($bitrates as $bitrate) {
            $filenameM3u8 = 'm' . $id . '_' . $bitrate . 'k.m3u8';
            $this->_split_mp4($basedir.$name_mp4, $newdist, $filenameM3u8, $bitrate);
        }
        // マスターファイルの作成
        $this->_create_master_m3u8($newdist, $id, $bitrates);
        $io->out((new \DateTime('now'))->format('Y/m/d H:i:s') . ' FINISH ' . $basedir.$name_mp4 . ' -> ' . $newdist);
    }

    /**
     * mp4ファイル分割
     * @param $source 変換前のファイルパス(フルパス)
     * @param $dist_dir 変換後の格納先（フォルダパス）
     * @param $filenameM3u8 変換後のm3u8ファイル名
     * @param $n_bitrate 動画のビットレート(単位：kbps)
     * */
    private function _split_mp4($source, $dist_dir, $filenameM3u8, $n_bitrate) {
        // ffmpegコマンドの要素作成
        $cmdline = $this->convertPath_mp4;
        $src = '-i ' . $source;
        $codec = '-c:v libx264 -c:a aac';
        $bitrate = '-b:v '. $n_bitrate .'k -minrate '. $n_bitrate .'k -maxrate '. $n_bitrate .'k -bufsize '. $n_bitrate*2 .'k -b:a 128k';
        // $scale = '-s 1920x1080';
        $format = "-f hls -hls_time 10 -hls_playlist_type vod -g 30 -keyint_min 30 -sc_threshold 0";
        $dist = "-hls_segment_filename \"" . $dist_dir . "v1_".$n_bitrate."k_%4d.ts\" " . $dist_dir . $filenameM3u8;

        // コマンド実行
        $command = $cmdline . ' ' . $src . ' ' . $codec . ' ' . $bitrate . 
                    // ' ' . $scale . 
                    ' ' . $format . ' ' . $dist;
        $a = system(escapeshellcmd($command));
        // パーミッション
        @chmod($dist_dir.$filenameM3u8, $this->uploadFileMask);
        $idFile = 0;
        while ( @chmod($dist_dir.sprintf('v1_'.$n_bitrate.'k_%s.ts', sprintf('%04d', $idFile)), $this->uploadFileMask) ) {
            $idFile += 1;
        }

        return $a;
    }

    /**
     * マスターm3u8ファイルの作成
     * @param $dist_dir 変換後の格納先（フォルダパス）
     * @param $id データベース保存時の動画のid
     * @param $bitrates 動画のビットレートの配列(単位：kbps)
     * */
    private function _create_master_m3u8($dist_dir, $id, $bitrates) {
        // ディレクトリの存在をチェック(なければ作成)
        // $this->checkConvertDirectoryMp4($dist_dir);

        // マスターファイルの文面作成
        $contents = "#EXTM3U\n";
        foreach ($bitrates as $bitrate) {
            // if ($bitrate < 4000) {
            //     continue;
            // }
            $filenameM3u8 = 'm' . $id . '_' . $bitrate . 'k.m3u8';
            $contents .= '#EXT-X-STREAM-INF:BANDWIDTH='.$bitrate*1000*1.2.',RESOLUTION=1920x1080,CODECS="avc1.42e00a,mp4a.40.2"'."\n";
            $contents .= DS . UPLOAD_MOVIE_BASE_URL . DS . 'm' . $id . DS.$filenameM3u8."\n";
        }
        
        // マスターファイル作成
        $filenameMaster = $dist_dir.'m'.$id.'.m3u8';
        file_put_contents($filenameMaster, $contents);
        // パーミッション
        @chmod($dist_dir.'m'.$id.'.m3u8', $this->uploadFileMask);
    }

}