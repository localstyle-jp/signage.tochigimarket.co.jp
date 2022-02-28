<?php 
namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;

class MaterialCategoriesTable extends AppTable {


    // テーブルの初期値を設定する
    public $defaultValues = [
        "id" => null,
        "position" => 0
    ];

    public $attaches = array('images' =>
                            array(),
                            'files' => array(),
                            );
    
    // 
    public function initialize(array $config)
    {
        // 添付ファイル
        // $this->addBehavior('FileAttache');
        $this->addBehavior('Position', [
                'group' => ['parent_category_id'],
                'groupMove' => false,
                'order' => 'DESC'
            ]);

        // アソシエーション
        $this->hasMany('Materials')->setDependent(false);

        parent::initialize($config);
        
    }
    // Validation
    public function validationDefault(Validator $validator)
    {
        $validator
            ->notEmpty('name', '入力してください')
            ->add('name', 'maxLength', [
                'rule' => ['maxLength', 40],
                'message' => __('40字以内で入力してください')
            ]);
        
        return $validator;
    }
}