<?php 
namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;
use Cake\Utility\Inflector;

class ContentsTable extends AppTable {

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
        $this->addBehavior('Position', [
                'order' => 'ASC',
                'group' => ['site_config_id']
            ]);

        $this->hasMany('ContentMaterials')->setDependent(true);
        $this->hasMany('MachineBoxes')->setDependent(false);

        $this->belongsTo('SiteConfigs');


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

    public function serialIncrement($id) {
        $content = $this->find()->where(['Contents.id' => $id])->first();

        if (empty($content)) {
            return;
        }

        $entity = $this->patchEntity($content, ['serial_no' => $content->serial_no + 1]);
        $this->save($entity);

        return;
    }

}