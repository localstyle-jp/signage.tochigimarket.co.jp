<?php
namespace App\Model\Behavior;

use ArrayObject;
use Cake\Datasource\EntityInterface;
use Cake\Event\Event;
use Cake\ORM\Behavior;
use Cake\ORM\Entity;
use Cake\ORM\Query;
use Cake\Utility\Text;
use Cake\Filesystem\Folder;
use Cake\Event\EventManager;

class FileAttacheBehavior extends Behavior {
    public $uploadDirCreate = true;
    public $uploadDirMask = 0777;
    public $uploadFileMask = 0666;

    //ImageMagick configure
    public $convertPath = '/usr/bin/convert';
    public $convertParams = '-thumbnail';

    //ffmpeg configure
    public $convertPath_mp4 = 'ffmpeg';

    // cake command configure
    // public $cakeCommPath = ROOT . DS . 'bin/cake';

    public function initialize(array $config) {
        $entity = $this->getTable()->newEntity();
        $entity->setVirtual(['attaches']);
    }

    public function beforeMarshal(Event $event, ArrayObject $data, ArrayObject $options) {
        $table = $event->getSubject();
        // images
        if (array_key_exists('images', $table->attaches)) {
            $attache_image_columns = $table->attaches['images'];
            foreach ($attache_image_columns as $column => $val) {
                if (array_key_exists($column, $data)) {
                    $data['_' . $column] = $data[$column];
                    // $data['_old_'.$column] = $event->getData($column);
                }
                if ((empty($data[$column]) || $data[$column] != UPLOAD_ERR_OK) && array_key_exists('_old_' . $column, $data) && $data['_old_' . $column] != '') {
                    $data[$column] = $data['_old_' . $column];
                }
            }
        }
        // files
        if (array_key_exists('files', $table->attaches)) {
            $attache_image_columns = $table->attaches['files'];
            foreach ($attache_image_columns as $column => $val) {
                if (array_key_exists($column, $data)) {
                    $data['_' . $column] = $data[$column];
                    // $data['_old_'.$column] = $event->getData($column);
                }
                if ((empty($data[$column]) || $data[$column] != UPLOAD_ERR_OK) && array_key_exists('_old_' . $column, $data) && $data['_old_' . $column] != '') {
                    $data[$column] = $data['_old_' . $column];
                }
            }
        }
    }

    public function beforeFind(Event $event, Query $query, ArrayObject $options, $primary) {
        $table = $event->getSubject();

        // afterFindの代わり
        $query->formatResults(function ($results) use ($table, $primary) {
            return $results->map(function ($row) use ($table, $primary) {
                if (is_object($row) && !array_key_exists('existing', $row)) {
                    $results = $this->_attachesFind($table, $row, $primary);
                }
                return $row;
            });
        });
    }

    public function afterSave(Event $event, EntityInterface $entity, ArrayObject $options) {
        // pr($event);
        // pr($entity);
        // pr($options);
        //アップロード処理
        $this->_uploadAttaches($event, $entity);
    }

    public function checkUploadDirectory($table) {
        $Folder = new Folder();

        if ($this->uploadDirCreate) {
            $dir = UPLOAD_DIR . $table->getAlias() . DS . 'images';
            if (!is_dir($dir) && !empty($table->attaches['images'])) {
                if (!$Folder->create(UPLOAD_DIR . $table->getAlias() . DS . 'images', $this->uploadDirMask)) {
                }
            }

            $dir = UPLOAD_DIR . $table->getAlias() . DS . 'files';
            if (!is_dir($dir) && !empty($table->attaches['files'])) {
                if (!$Folder->create($dir, $this->uploadDirMask)) {
                }
            }
        }
    }

    public function checkConvertDirectoryMp4($dir) {
        $Folder = new Folder();

        if ($this->uploadDirCreate) {
            if (!is_dir($dir)) {
                if (!$Folder->create($dir, $this->uploadDirMask)) {
                }
            }
        }
    }

    protected function _attachesFind($table, $results, $primary = false) {
        $this->checkUploadDirectory($table);
        $_att_images = array();
        $_att_files = array();
        if (!empty($table->attaches['images'])) {
            $_att_images = $table->attaches['images'];
        }
        if (!empty($table->attaches['files'])) {
            $_att_files = $table->attaches['files'];
        }
        $entity = $results;
        // foreach ($results as $entity) {
        $columns = null;

        $entity->setVirtual(['attaches']);
        $_ = $entity->toArray();
        $entity_attaches = [];
        // pr($_);
        //image
        foreach ($_att_images as $columns => $_att) {
            $_attaches = array();
            if (isset($_[$columns])) {
                $_attaches['0'] = '';
                $_file = '/' . UPLOAD_BASE_URL . '/' . $table->getAlias() . '/images/' . $_[$columns];
                if (is_file(WWW_ROOT . $_file)) {
                    $_attaches['0'] = $_file;
                }
                if (!empty($_att['thumbnails'])) {
                    foreach ($_att['thumbnails'] as $_name => $_val) {
                        $key_name = (!is_int($_name)) ? $_name : $_val['prefix'];
                        $_attaches[$key_name] = '';
                        $_file = '/' . UPLOAD_BASE_URL . '/' . $table->getAlias() . '/images/' . $_val['prefix'] . $_[$columns];
                        if (!empty($_[$columns]) && is_file(WWW_ROOT . $_file)) {
                            $_attaches[$key_name] = $_file;
                        }
                    }
                }
                // pr($_attaches);
                $entity_attaches[$columns] = $_attaches;
            }
        }
        //file
        foreach ($_att_files as $columns => $_att) {
            $def = array('0', 'src', 'extention', 'name', 'download');
            $def = array_fill_keys($def, null);

            if (isset($_[$columns])) {
                $_attaches = $def;
                $_file = '/' . UPLOAD_BASE_URL . '/' . $table->getAlias() . '/files/' . $_[$columns];
                if (is_file(WWW_ROOT . $_file)) {
                    $_attaches['0'] = $_file;
                    $_attaches['src'] = $_file;
                    $_attaches['extention'] = $this->getExtension($_[$columns . '_name']);
                    $_attaches['name'] = $_[$columns . '_name'];
                    $_attaches['size'] = $_[$columns . '_size'];
                    $_attaches['download'] = '/file/' . $_[$table->getPrimaryKey()] . '/' . $columns . '/';
                }
                $entity_attaches[$columns] = $_attaches;
            }
        }
        $entity->attaches = $entity_attaches;
        // }
        return $results;
    }

    public function _uploadAttaches(Event $event, EntityInterface $entity) {
        $table = $event->getSubject();
        $this->checkUploadDirectory($table);

        $uuid = Text::uuid();

        // $table->eventManager()->off('Model.afterSave');

        if (!empty($entity)) {
            $_data = $entity->toArray();
            $id = $entity->id;
            $query = $table->find()->where([$table->getAlias() . '.' . $table->getPrimaryKey() => $id]);
            $old_entity = $query->first();
            $old_data = $old_entity->toArray();

            $_att_images = array();
            $_att_files = array();
            if (!empty($table->attaches['images'])) {
                $_att_images = $table->attaches['images'];
            }
            if (!empty($table->attaches['files'])) {
                $_att_files = $table->attaches['files'];
            }
            //upload images
            foreach ($_att_images as $columns => $val) {
                $image_name = array();
                if (!empty($_data['_' . $columns])) {
                    $image_name = $_data['_' . $columns];
                }
                if (!empty($image_name['tmp_name']) && $image_name['error'] === UPLOAD_ERR_OK) {
                    $basedir = WWW_ROOT . UPLOAD_BASE_URL . DS . $table->getAlias() . DS . 'images' . DS;
                    $imageConf = $_att_images[$columns];
                    $ext = $this->getExtension($image_name['name']);
                    $filepattern = $imageConf['file_name'];
                    $file = $image_name;
                    if ($info = getimagesize($file['tmp_name'])) {
                        //画像 処理方法
                        $convert_method = (!empty($imageConf['method'])) ? $imageConf['method'] : null;

                        if (in_array($ext, $imageConf['extensions'])) {
                            $newname = sprintf($filepattern, $id, $uuid) . '.' . $ext;
                            $this->convert_img(
                                $imageConf['width'] . 'x' . $imageConf['height'],
                                $file['tmp_name'],
                                $basedir . $newname,
                                $convert_method
                            );

                            //サムネイル
                            if (!empty($imageConf['thumbnails'])) {
                                foreach ($imageConf['thumbnails'] as $suffix => $val) {
                                    //画像処理方法
                                    $convert_method = (!empty($val['method'])) ? $val['method'] : null;
                                    //ファイル名
                                    $prefix = (!empty($val['prefix'])) ? $val['prefix'] : $suffix;
                                    $_newname = $prefix . $newname;
                                    //変換
                                    $this->convert_img(
                                        $val['width'] . 'x' . $val['height'],
                                        $file['tmp_name'],
                                        $basedir . $_newname,
                                        $convert_method
                                    );
                                }
                            }
                            //
                            // $_data[$columns] = $newname;
                            $old_entity->set($columns, $newname);
                            // $table->patchEntity($entity, $_data, ['validate' => false]);
                            $table->save($old_entity);

                            // 旧ファイルの削除
                            if (!empty($old_data['attaches'][$columns])) {
                                foreach ($old_data['attaches'][$columns] as $image_path) {
                                    if ($image_path && is_file(WWW_ROOT . $image_path)) {
                                        @unlink(WWW_ROOT . $image_path);
                                    }
                                }
                            }
                        }
                    }
                }
            }
            if (!empty($_att_files)) {
                //upload files
                foreach ($_att_files as $columns => $val) {
                    $file_name = array();
                    if (!empty($_data['_' . $columns])) {
                        $file_name = $_data['_' . $columns];
                    }
                    if (!empty($file_name['tmp_name']) && $file_name['error'] === UPLOAD_ERR_OK) {
                        $basedir = WWW_ROOT . UPLOAD_BASE_URL . DS . $table->getAlias() . DS . 'files' . DS;
                        $fileConf = $_att_files[$columns];
                        $ext = $this->getExtension($file_name['name']);
                        $filepattern = $fileConf['file_name'];
                        $file = $file_name;

                        if (in_array($ext, $fileConf['extensions'])) {
                            $newname = sprintf($filepattern, $id, $uuid) . '.' . $ext;
                            move_uploaded_file($file['tmp_name'], $basedir . $newname);
                            chmod($basedir . $newname, $this->uploadFileMask);

                            if ($ext == 'mp4') {
                                $newdist = WWW_ROOT . UPLOAD_MOVIE_BASE_URL . DS . 'm' . $id . DS;
                                $this->checkConvertDirectoryMp4($newdist);
                                // // tsファイルへの分割
                                // $newdist = WWW_ROOT . UPLOAD_MOVIE_BASE_URL . DS . 'm' . $id . DS;
                                // $bitrates = [/*BITRATE_LOW, BITRATE_MID, */BITRATE_HIGH];
                                // foreach ($bitrates as $bitrate) {
                                //     $filenameM3u8 = 'm' . $id . '_' . $bitrate . 'k.m3u8';
                                //     $this->convert_mp4($basedir.$newname, $newdist, $filenameM3u8, $bitrate);
                                // }
                                // // マスターファイルの作成
                                // $this->create_master_m3u8($newdist, $id, $bitrates);
                                // DBへの記録準備
                                $old_entity->set('view_second', $this->getViewSeconds($basedir . $newname));
                                // $newname = '';
                                $filenameMaster = 'm' . $id . '.m3u8';
                                $old_entity->set('url', 'm' . $id . DS . $filenameMaster);
                            }

                            // $_data[$columns] = $newname;
                            // $_data[$columns.'_name'] = $file_name['name'];
                            // $_data[$columns.'_size'] = $file_name['size'];
                            $old_entity->set($columns, $newname);
                            if (empty($old_entity->{$columns . '_name'})) {
                                $old_entity->set($columns . '_name', $this->getFileName($file_name['name'], $ext));
                            }
                            $old_entity->set($columns . '_size', $file_name['size']);
                            $old_entity->set($columns . '_extension', $ext);
                            // $table->patchEntity($entity, $_data, ['validate' => false]);
                            $table->save($old_entity);

                            // 旧ファイルの削除
                            if (!empty($old_data['attaches'][$columns])) {
                                foreach ($old_data['attaches'][$columns] as $file_path) {
                                    if ($file_path && is_file(WWW_ROOT . $file_path)) {
                                        @unlink(WWW_ROOT . $file_path);
                                    }
                                }
                            }
                            // 分割前mp4ファイルの削除
                            // if ($ext=='mp4') {
                            //     @unlink($basedir.$newname);
                            // }
                        }
                    }
                }
            }
            // $table->addBehavior('Position');
        }
    }

    /**
     * 拡張子の取得
     * */
    public function getExtension($filename) {
        return strtolower(substr(strrchr($filename, '.'), 1));
    }
    public function getFileName($filename, $ext) {
        return str_replace('.' . $ext, '', $filename);
    }

    /**
     * 動画時間の取得
     * */
    public function getViewSeconds($source) {
        // コマンド実行
        $command = 'ffprobe ' . $source . ' -hide_banner -show_entries format=duration';
        $last_line = exec(escapeshellcmd($command), $out);
        $time = preg_match('/^duration=([0-9]+)\.([0-9]+)/', $out[1], $matches);

        return $matches[1];
    }

    /**
     * 画像アップロード
     * @param $size [width]x[height]
     * @param $source アップロード元ファイル(フルパス)
     * @param $dist 変換後のファイルパス（フルパス）
     * @param $method 処理方法
     *        - fit     $size内に収まるように縮小
     *        - cover   $sizeの短い方に合わせて縮小
     *        - crop    cover 変換後、中心$sizeでトリミング
     * */
    public function convert_img($size, $source, $dist, $method = 'fit') {
        list($ow, $oh, $info) = getimagesize($source);
        $exif = @exif_read_data($source);
        $option_rotate = '';
        $deg = 0;
        $convertParams = $this->convertParams;
        if (!empty($exif) && is_array($exif) && array_key_exists('Orientation', $exif)) {
            switch ($exif['Orientation']) {
                case 1:
                    break;
                case 8:
                    $deg = 270;
                    break;
                case 3:
                    $deg = 180;
                    break;
                case 6:
                    $deg = 90;
                    break;
            }
            if ($deg) {
                $option_rotate = '-rotate ' . $deg;
                $convertParams = $option_rotate . ' ' . $this->convertParams;
            }
        }

        $sz = explode('x', $size);
        $cmdline = $this->convertPath;
        //サイズ指定ありなら
        if (0 < $sz[0] && 0 < $sz[1]) {
            if ($ow <= $sz[0] && $oh <= $sz[1]) {
                //枠より完全に小さければ、ただのコピー
                $size = $ow . 'x' . $oh;
                $option = $convertParams . ' ' . $size . '>';
            } else {
                //枠をはみ出していれば、縮小
                if ($method === 'cover' || $method === 'crop') {
                    //中央切り取り
                    $crop = $size;
                    if (($ow / $oh) <= ($sz[0] / $sz[1])) {
                        //横を基準
                        $size = $sz[0] . 'x';
                    } else {
                        //縦を基準
                        $size = 'x' . $sz[1];
                    }

                    //cover
                    $option = '-thumbnail ' . $size . '>';

                    //crop
                    if ($method === 'crop') {
                        $option .= ' -gravity center -crop ' . $crop . '+0+0';
                    }
                } else {
                    //通常の縮小 拡大なし
                    $option = $convertParams . ' ' . $size . '>';
                }
            }
        } else {
            //サイズ指定なしなら 単なるコピー
            $size = $ow . 'x' . $oh;
            $option = $convertParams . ' ' . $size . '>';
        }
        $a = system(escapeshellcmd($cmdline . ' ' . $option . ' ' . $source . ' ' . $dist));
        @chmod($dist, $this->uploadFileMask);
        return $a;
    }

    /**
     * mp4ファイルアップロード
     * @param $source 変換前のファイルパス(フルパス)
     * @param $dist_dir 変換後の格納先（フォルダパス）
     * @param $filenameM3u8 変換後のm3u8ファイル名
     * @param $n_bitrate 動画のビットレート(単位：kbps)
     * */
    // public function convert_mp4($source, $dist_dir, $filenameM3u8, $n_bitrate) {
    //     // ディレクトリの存在をチェック(なければ作成)
    //     $this->checkConvertDirectoryMp4($dist_dir);

    //     // ffmpegコマンドの要素作成
    //     $cmdline = $this->convertPath_mp4;
    //     $src = '-i ' . $source;
    //     $codec = '-c:v libx264 -c:a aac';
    //     $bitrate = '-b:v '. $n_bitrate .'k -minrate '. $n_bitrate .'k -maxrate '. $n_bitrate .'k -bufsize '. $n_bitrate*2 .'k -b:a 128k';
    //     // $scale = '-s 1920x1080';
    //     // $format = "-f hls -hls_time 1 -hls_playlist_type vod";
    //     $format = "-f hls -hls_time 10 -hls_playlist_type vod -g 30 -keyint_min 30 -sc_threshold 0";
    //     $dist = "-hls_segment_filename \"" . $dist_dir . "v1_".$n_bitrate."k_%4d.ts\" " . $dist_dir . $filenameM3u8;

    //     // コマンド実行
    //     $command = $cmdline . ' ' . $src . ' ' . $codec . ' ' . $bitrate .
    //                 // ' ' . $scale .
    //                 ' ' . $format . ' ' . $dist;
    //     $a = system(escapeshellcmd($command));
    //     // パーミッション
    //     @chmod($dist_dir.$filenameM3u8, $this->uploadFileMask);
    //     $idFile = 0;
    //     while ( @chmod($dist_dir.sprintf('v1_'.$n_bitrate.'k_%s.ts', sprintf('%04d', $idFile)), $this->uploadFileMask) ) {
    //         $idFile += 1;
    //     }

    //     return $a;
    // }

    /**
     * マスターm3u8ファイルの作成
     * @param $dist_dir 変換後の格納先（フォルダパス）
     * @param $id データベース保存時の動画のid
     * @param $bitrates 動画のビットレートの配列(単位：kbps)
     * */
    // public function create_master_m3u8($dist_dir, $id, $bitrates) {
    //     // ディレクトリの存在をチェック(なければ作成)
    //     $this->checkConvertDirectoryMp4($dist_dir);

    //     // マスターファイルの文面作成
    //     $contents = "#EXTM3U\n";
    //     foreach ($bitrates as $bitrate) {
    //         // if ($bitrate < 4000) {
    //         //     continue;
    //         // }
    //         $filenameM3u8 = 'm' . $id . '_' . $bitrate . 'k.m3u8';
    //         $contents .= '#EXT-X-STREAM-INF:BANDWIDTH='.$bitrate*1000*1.2.',RESOLUTION=1920x1080,CODECS="avc1.42e00a,mp4a.40.2"'."\n";
    //         $contents .= DS . UPLOAD_MOVIE_BASE_URL . DS . 'm' . $id . DS.$filenameM3u8."\n";
    //     }

    //     // マスターファイル作成
    //     $filenameMaster = $dist_dir.'m'.$id.'.m3u8';
    //     file_put_contents($filenameMaster, $contents);
    //     // パーミッション
    //     @chmod($dist_dir.'m'.$id.'.m3u8', $this->uploadFileMask);
    // }
}
