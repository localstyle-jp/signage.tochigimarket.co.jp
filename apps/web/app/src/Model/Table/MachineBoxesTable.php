<?php
namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;
use Cake\Utility\Inflector;

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
     * オフライン用のJSONデータ
     *
     */
    public function getBuildData(int $id):?array {
        // 表示端末
        $machine_box = $this->find()->where(['MachineBoxes.id' => $id])->contain(['MachineContents' => ['MachineMaterials']])->first();
        if (!$machine_box) {
            return null;
        }

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
                    $files[] = [
                        'name' => $source,
                        'path' => $material['attaches']['file'][0] ?? ''
                    ];
                }
            }
            if ($type == 'image') {
                if ($source = $material['image']) {
                    $files[] = [
                        'name' => $source,
                        'path' => $material['attaches']['image'][0] ?? ''
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

            $contents[] = [
                'id' => $material['id'],
                'display_seconds' => $material['view_second'],
                'subtitle' => $material['rolling_caption'],
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
