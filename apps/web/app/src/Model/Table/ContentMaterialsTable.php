<?php 
namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;
use Cake\Utility\Inflector;

class ContentMaterialsTable extends AppTable {

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
        $this->addBehavior('Position', [
                'order' => 'ASC',
                'group' => ['site_config_id']
            ]);

        $this->belongsTo('Contents');
        $this->belongsTo('Materials');


        parent::initialize($config);
    }

    public function validationDefault(Validator $validator)
    {
        $validator
            ->notEmpty('view_second', '入力してください')
            ->add('view_second', 'num', ['rule' => ['numeric'],'message' => ('数字で入力してください') ])
            ;
        
        return $validator;
    }

}