<?php 
namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;
use Cake\Utility\Inflector;
use Cake\Filesystem\Folder;

class MaterialsTable extends AppTable {

    // テーブルの初期値を設定する
    public $defaultValues = [
        "id" => null,
        'type' => 1
    ];

    public $attaches = array('images' =>
                            array('image' => array('extensions' => array('jpg', 'jpeg', 'gif', 'png'),
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
                            'files' => array(
                                'file' => array(
                                    'extensions' => array('mp4'),
                                    'file_name' => 'e_f_%d_%s'
                                    )
                                // file_1
                                // 'file_webm' => array(
                                //     'extensions' => array('webm'),
                                //     'file_name' => 'e_f_%d_%s'
                                //     )
                                // // file_2
                                
                                ),
                            );
                // 
    public function initialize(array $config)
    {
        $this->addBehavior('Position', [
                'order' => 'ASC',
                // 'group' => ['parent_id']
            ]);

        $this->addBehavior('FileAttache');
        
        $this->hasMany('ContentMaterials')->setDependent(true);
        $this->belongsTo('MaterialCategories')->setForeignKey('category_id');


        parent::initialize($config);
    }

    public function validationDefault(Validator $validator)
    {
        // $validator->setProvider('App', 'App\Validator\AppValidation');

        $validator
            ->notEmpty('name', '入力してください')
            ->add('name', 'maxLength', ['rule' => ['maxLength', 40],'message' => ('40字以内で入力してください') ])
            ->notEmpty('category_id', '選択してください')
            ;
        
        return $validator;
    }

    // 画像
    public function validationImageNew(Validator $validator) {

        $validator = $this->validationDefault($validator);

        $validator
            ->notEmpty('image', '選択してください')
        ;

        return $validator;
    }

    public function validationImageUpdate(Validator $validator) {
        $validator = $this->validationDefault($validator);

        $validator
            ->notEmpty('_old_image', '選択してください')
        ;

        return $validator;
    }

    // 動画
    public function validationMovieNew(Validator $validator) {
        $validator = $this->validationDefault($validator);

        $validator
            ->notEmpty('movie_tag', '入力してください')
            // ->notEmpty('view_second', '情報取得してください')
            // ->add('view_second', 'comaprison', ['rule' => ['comparison', '>', 0], 'message' => '情報取得してください'])
        ;

        return $validator;
    }

    public function validationMovieUpdate(Validator $validator) {

        return $this->validationMovieNew($validator);

    }

    // URL
    public function validationUrlNew(Validator $validator) {
        $validator = $this->validationDefault($validator);

        $validator
            ->notEmpty('url', '入力してください')
        ;

        return $validator;
    }
    public function validationUrlUpdate(Validator $validator) {
        return $this->validationUrlNew($validator);
    }

    // ページ
    public function validationPageNew(Validator $validator) {
        $validator = $this->validationDefault($validator);

        $validator
            ->notEmpty('content', '入力してください')
            ->notEmpty('image', '選択してください')
        ;

        return $validator;
    }
    public function validationPageUpdate(Validator $validator) {
        $validator = $this->validationDefault($validator);

        $validator
            ->notEmpty('content', '入力してください')
            ->notEmpty('_old_image', '選択してください')
        ;

        return $validator;
    }

    public function validationMp4New(Validator $validator) {
        $validator = $this->validationDefault($validator);

        $validator
            ->notEmpty('file', '選択してください')
        ;

        return $validator;
    }

    public function validationMp4Update(Validator $validator) {
        $validator = $this->validationDefault($validator);

        // $validator
        //     ->notEmpty('_old_file', '選択してください')
        // ;

        return $validator;
    }

    // public function validationWebmNew(Validator $validator) {
    //     $validator = $this->validationDefault($validator);

    //     // $validator
    //     //     ->notEmpty('file', '選択してください')
    //     // ;

    //     return $validator;
    // }

    // public function validationWebmUpdate(Validator $validator) {
    //     $validator = $this->validationDefault($validator);

    //     return $validator;
    // }

    public function setMp4($data) {
        $dir = UPLOAD_MOVIE_BASE_URL . DS . 'm' . $data['id'];
        if (!is_dir($dir)) {
            $Folder = new Folder();

            if (!$Folder->create(WWW_ROOT . $dir, 0777)) {

            }
        }

        return;
    }

    public function setWebm($data) {
        $dir = UPLOAD_MOVIE_BASE_URL . DS . 'm' . $data['id'];
        if (!is_dir($dir)) {
            $Folder = new Folder();

            if (!$Folder->create(WWW_ROOT . $dir, 0777)) {

            }
        }

        return;
    }
}