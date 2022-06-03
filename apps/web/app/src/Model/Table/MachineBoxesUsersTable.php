<?php 
namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;
use Cake\Utility\Inflector;

class MachineBoxesUsersTable extends AppTable {

    // テーブルの初期値を設定する
    public $defaultValues = [
        "id" => null,
    ];

    public $attaches = array('images' =>
                            array(),
                            'files' => array(),
                            );
                // 
    public function initialize(array $config)
    {

        $this->belongsTo('Users');
        $this->belongsTo('MachineBoxes');

        parent::initialize($config);
    }

    public function validationDefault(Validator $validator)
    {
        // $validator
        //     ->notEmpty('name', '入力してください')
        //     ->add('name', 'maxLength', ['rule' => ['maxLength', 40],'message' => '40文字以内で入力してください'])
        //     ->notEmpty('url', '入力してください')
        //     // ->notEmpty('content_id', '選択してください')
        //     // ->naturalNumber('content_id', '選択してください')
        //     ;
        
        return $validator;
    }

}