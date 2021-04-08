<?php 
namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;
use Cake\Utility\Inflector;

class MachineContentsTable extends AppTable {

    // テーブルの初期値を設定する
    public $defaultValues = [
        "id" => null,
        "serial_no" => 0
    ];

    public $attaches = array('images' =>
                            array(),
                            'files' => array(),
                            );
                // 
    public function initialize(array $config)
    {
        

        $this->hasMany('MachineMaterials')->setDependent(true);
        $this->hasOne('MachineBoxes')->setDependent(false);


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