<?php 
namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;
use Cake\Utility\Inflector;
use Cake\Datasource\ConnectionManager;

class SequencesTable extends AppTable {

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


        parent::initialize($config);
    }
    
    public function getNumber($key)
    {
        $connection = ConnectionManager::get('default');
        $connection->begin();

        $no = 0;

        try {
            $r = true;
            $entity = $this->find()->where(['k' => $key])->first();
            if (empty($entity)) {
                $entity = $this->newEntity();
                $entity->id = null;
                $entity->k = $key;
                $entity->val = 0;
                $r = $this->save($entity);
            }

            if ($r) {
                $entity = $this->query()
                                ->where(['k' => $key])
                                ->select(['id', 'k', 'val'])
                                ->epilog('FOR UPDATE')
                                ->first();
                $entity->val = $entity->val + 1;
                $r = $this->save($entity);
            }

            if ($r) {
                $no = $entity->val;
                $connection->commit();
            } else {
                throw new \Exception("Error Processing Request", 1);
                
            }
        } catch (\Exception $e) {
            $connection->rollback();
        }

        return $no;
    }


}