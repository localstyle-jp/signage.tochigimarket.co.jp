<?php
namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;
use Cake\Utility\Inflector;
use App\Utils\Zip;
use Exception;

class MachineBoxesTable extends AppTable {
    // テーブルの初期値を設定する
    public $defaultValues = [
        'id' => null,
    ];

    public $attaches = array('images' => array(),
        'files' => array(),
    );
                //
    public function initialize(array $config) {
        $this->addBehavior('Position', [
            'order' => 'DESC',
            'group' => ['site_config_id']
        ]);

        $this->belongsTo('SiteConfigs');
        $this->belongsTo('Contents');

        $this->belongsTo('MachineContents');

        $this->belongsToMany('Users')->setDependent(true);

        parent::initialize($config);
    }

    public function validationDefault(Validator $validator) {
        $validator
            ->notEmpty('name', '入力してください')
            ->add('name', 'maxLength', ['rule' => ['maxLength', 40], 'message' => '40文字以内で入力してください'])
            ->notEmpty('url', '入力してください')
            // ->notEmpty('content_id', '選択してください')
            // ->naturalNumber('content_id', '選択してください')
;

        return $validator;
    }

    /**
     *
     *
     * Buildデータの保存
     *
     */
    public function buildZip($machine_box_id) {
        error_log("[DEBUG] buildZip started for id: {$machine_box_id}");
        
        // ZipArchive拡張確認
        if (!extension_loaded('zip')) {
            error_log("[ERROR] ZipArchive extension not loaded");
            return false;
        }
        
        // ZIP用データ
        $data = $this->getBuildZipData($machine_box_id);
        if (!$data) {
            error_log("[ERROR] No build data for id: {$machine_box_id}");
            return false;
        }
        
        error_log("[DEBUG] Build data count: " . count($data));

        /**
         *
         * ZIP出力
         *
         */
        // 初期化処理
        $build_version = $this->beforeBuild($machine_box_id);
        error_log("[DEBUG] Build initialized with version: {$build_version}");

        //　自分のバージョン取得処理(プログレス中変化があれば中止する)
        $getVersion = function () use ($machine_box_id) {
            return $this->getBuildVersion($machine_box_id);
        };

        // プログレス更新処理
        $updateProgress = function ($progress) use ($machine_box_id) {
            error_log("[DEBUG] Progress update: {$progress}% for id: {$machine_box_id}");
            $this->updateProgress($machine_box_id, $progress);
        };

        //
        $name = $this->getZipFolderName();
        $dest = $this->getUploadZipPath($machine_box_id);
        
        error_log("[DEBUG] Zip destination: {$dest}");
        error_log("[DEBUG] Destination dir writable: " . (is_writable(dirname($dest)) ? 'YES' : 'NO'));

        //
        try {
            $zip = new Zip;
            $zip->addProgressEvent($updateProgress, $getVersion);
            $result = $zip->make($data, $name, $dest);
            error_log("[DEBUG] Zip creation result: " . ($result ? 'SUCCESS' : 'FAILED'));
            return $result;
        } catch (Exception $e) {
            error_log("[ERROR] Zip creation exception: " . $e->getMessage());
            return false;
        }
    }

    public function getZipFolderName() {
        return 'caters-signage';
    }

    /**
     *
     * ZIPの場所
     *
     */
    public function getUploadZipPath($id) {
        return UPLOAD_DIR . 'MachineBoxes/' . $id . '.zip';
    }

    /**
     *
     * ビルド初期化処理
     *
     */
    public function beforeBuild($id) {
        $entity = $this->find()->where(['id' => $id])->first();
        if (!$entity) {
            return false;
        }
        // 新しいビルドバージョン
        $build_version = ($entity->build_version ?? 0) + 1;

        $this->updateAll(['build_progress' => 0, 'build_version' => $build_version], ['id' => $id]);
        return $build_version;
    }

    /**
     *
     * プログレス状況を更新する
     *
     */
    public function updateProgress($id, $progress) {
        $this->updateAll(['build_progress' => $progress], ['id' => $id]);
    }

    /**
     *
     * Builldバージョンを取得する
     *
     */
    public function getBuildVersion($id) {
        $entity = $this->find()->where(['id' => $id])->first();
        if (!$entity) {
            return false;
        }

        $build_version = $entity->build_version ?? 0;
        return $build_version;
    }

    /**
     *
     * プログレス値を取得する
     *
     */
    public function getProgress($id) {
        $entity = $this->find()->where(['id' => $id])->first();
        if (!$entity) {
            return false;
        }

        $build_progress = $entity->build_progress ?? 0;
        return $build_progress;
    }

    /**
     *
     *
     * ビルドデータをZIP出力用に変換
     *
     *
     */
    public function getBuildZipData($id) {
        $data = $this->getBuildData($id);
        if (!$data) {
            return false;
        }

        /**
         *
         * ビルドjson
         *
         */
        $json = [
            [
                'name' => 'build.json',
                'data' => [
                    'content' => json_encode($data)
                ]
            ]
        ];

        /**
         *
         * ソースデータ
         *
         */
        $public_root = WWW_ROOT;
        $public_root = rtrim($public_root, '/');
        
        error_log("[DEBUG] WWW_ROOT: {$public_root}");
        
        $files = array_map(function ($_file) use ($public_root) {
            $fullPath = $public_root . $_file['path'];
            error_log("[DEBUG] File mapping: name={$_file['name']}, path={$_file['path']}, fullPath={$fullPath}");
            return [
                'name' => 'sources/' . $_file['name'],
                'data' => [
                    'path' => $fullPath
                ]
            ];
        }, $data['files'] ?? []);

        //
        return array_merge($json, $files);
    }

    /**
     *
     *
     * ビルドデータをまとめる
     *
     *
     */
    public function getBuildData(int $id):?array {
        // 表示端末
        $machine_box = $this->find()->where(['MachineBoxes.id' => $id])->contain(['MachineContents' => ['MachineMaterials' => function ($q) {
            return $q->order(['MachineMaterials.position' => 'ASC']);
        }]])->first();
        if (!$machine_box) {
            return null;
        }

        // 共通の字幕
        $override_caption = $machine_box->caption_flg == 'machine' ? ($machine_box->rolling_caption ?? '') : '';

        //
        $files = [];
        $contents = [];
        foreach (($machine_box->machine_content->machine_materials ?? []) as $k => $material) {
            // file_extensionで判別する。　mp4かimageのみ想定

            // なぜかimageの時に空白
            $type = $material['file_extension'] == 'mp4' ? 'mp4' : 'image';

            //
            $source = '';
            $sound = '';
            if ($type == 'mp4') {
                if ($source = $material['file']) {
                    // attaches が空の場合はファイル名から直接パスを構築
                    $attachPath = $material['attaches']['file'][0] ?? '';
                    if (empty($attachPath)) {
                        $attachPath = '/upload/MachineMaterials/files/' . $source;
                        error_log("[DEBUG] MP4 attach path built from filename: {$attachPath}");
                    }
                    error_log("[DEBUG] MP4 file: source={$source}, attachPath={$attachPath}");
                    $files[] = [
                        'name' => $source,
                        'path' => $attachPath
                    ];
                }
            }
            if ($type == 'image') {
                if ($source = $material['image']) {
                    // attaches が空の場合はファイル名から直接パスを構築
                    $attachPath = $material['attaches']['image'][0] ?? '';
                    if (empty($attachPath)) {
                        $attachPath = '/upload/MachineMaterials/images/' . $source;
                        error_log("[DEBUG] Image attach path built from filename: {$attachPath}");
                    }
                    error_log("[DEBUG] Image file: source={$source}, attachPath={$attachPath}");
                    $files[] = [
                        'name' => $source,
                        'path' => $attachPath
                    ];
                }

                //
                if ($sound = $material['sound']) {
                    $files[] = [
                        'name' => $sound,
                        'path' => '/upload/Materials/files/' . $sound
                    ];
                }
            }

            $caption = $material['rolling_caption'] ?? '';
            if ($override_caption) {
                $caption = $override_caption;
            }
            $contents[] = [
                'id' => $material['id'],
                'display_seconds' => $material['view_second'],
                'subtitle' => $caption,
                'type' => $type,
                'source' => $source,
                'bgm' => $sound,
            ];
        }
        if (!$contents) {
            return null;
        }

        $data = [
            'setting' => [
                'width' => $machine_box->width,
                'height' => $machine_box->height,
                'is_vertical' => $machine_box->is_vertical,
            ],
            'data' => $contents,
            'files' => $files,
        ];

        return $data;
    }
}
