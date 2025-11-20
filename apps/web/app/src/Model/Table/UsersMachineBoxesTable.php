<?php 
namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;

class UsersMachineBoxesTable extends AppTable {

    // テーブルの初期値を設定する
    public $defaultValues = [
        "id" => null
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
    // Validation
    public function validationDefault(Validator $validator)
    {

        return $validator;
    }

}