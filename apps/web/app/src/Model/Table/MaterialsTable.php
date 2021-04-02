<?php 
namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;
use Cake\Utility\Inflector;

class MaterialsTable extends AppTable {

    // テーブルの初期値を設定する
    public $defaultValues = [
        "id" => null,
        'type' => 1
    ];

    public $attaches = array('images' =>
                            array(),
                            'files' => array(),
                            );
                // 
    public function initialize(array $config)
    {
        $this->addBehavior('Position', [
                'order' => 'ASC',
                // 'group' => ['parent_id']
            ]);

        $this->hasMany('ContentMaterials')->setDependent(true);


        parent::initialize($config);
    }

    public function validationDefault(Validator $validator)
    {
        $validator->setProvider('App', 'App\Validator\AppValidation');

        $validator
            ->notEmpty('name', '入力してください')
            ->add('name', 'maxLength', ['rule' => ['maxLength', 40],'message' => __('40字以内で入力してください') ])
            ;
        
        return $validator;
    }

}