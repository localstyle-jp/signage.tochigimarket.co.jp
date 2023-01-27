<?php
namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;
use Cake\Utility\Inflector;

class InfosTable extends AppTable {
    // テーブルの初期値を設定する
    public $defaultValues = [
        'id' => null,
        'position' => 0,
        'status' => 'draft'
    ];

    // 新CMSの枠ブロックを使う場合の設定
    public $useHierarchization = [
        'contents_table' => 'info_contents',
        'contents_id_name' => 'info_content_id',
        'sequence_model' => 'SectionSequences',
        'sequence_table' => 'section_sequence',
        'sequence_id_name' => 'section_sequence_id'
    ];

    public $attaches = array('images' => array('image' => array('extensions' => array('jpg', 'jpeg', 'gif', 'png'),
        'width' => 1200,
        'height' => 1200,
        'file_name' => 'img_%d_%s',
        'thumbnails' => array(
            's' => array(
                'prefix' => 's_',
                'width' => 320,
                'height' => 240
            )
        ),
    )
        //image_1
    ),
        'files' => array(),
    );

    // 推奨サイズ
    public $recommend_size_display = [
        // 'image' => true, //　編集画面に推奨サイズを常時する場合の指定
        // 'image' => ['width' => 300, 'height' => 300] // attaachesに書かれているサイズ以外の場合の指定
        // 'image' => false
        'image' => '横幅700以上を推奨。1200x1200以内に縮小されます。'
    ];
                            //
    public function initialize(array $config) {
        // 並び順
        if (CATEGORY_SORT) {
            $this->addBehavior('Position', [
                'group' => ['page_config_id', 'category_id', 'brand_id'],
                'groupMove' => false
            ]);
        } else {
            $this->addBehavior('Position', [
                'group' => ['page_config_id', 'brand_id'],
                'groupMove' => false
            ]);
        }
        // 添付ファイル
        $this->addBehavior('FileAttache');

        $this->hasMany('InfoContents')->setDependent(true);
        $this->hasMany('InfoTags')->setDependent(true);

        $this->belongsTo('PageConfigs');
        $this->belongsTo('Categories');
        $this->belongsto('Brands');

        parent::initialize($config);
    }
    // Validation
    public function validationDefault(Validator $validator) {
        $validator
            ->notEmpty('title', '入力してください')
            ->add('title', 'maxLength', [
                'rule' => ['maxLength', 100],
                'message' => __('100字以内で入力してください')
            ])
            ->notEmpty('start_date', '入力してください');

        return $validator;
    }

    public function validationIsCategory(Validator $validator) {
        $validator = $this->validationDefault($validator);

        $validator
            ->notEmpty('category_id', '選択してください')
            ->add('category_id', 'check', ['rule' => ['comparison', '>', 0], 'message' => '選択してください']);

        return $validator;
    }

    public function getRecommendImageSize($column) {
    }
}
