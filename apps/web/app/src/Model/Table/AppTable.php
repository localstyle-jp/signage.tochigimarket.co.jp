<?php 
namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Filesystem\Folder;
use Cake\Utility\Text;

class AppTable extends Table {

    public function initialize(array $config)
    {
        // 作成日時と更新日時の自動化
        $this->addBehavior('Timestamp',[
            'events' => [
                'Model.beforeSave' => [
                    'created' => 'new',
                    'modified' => 'always'
                ]
            ]
        ]);
    }
    
    // cakePHP2と互換性を保つためにcreateを自前で作る
    public function create($data) {

        $entity = $this->createEntity()->toArray();

        return $entity;
    }
    public function createEntity($data=null) {

        if (is_null($data)) {
            $data = $this->defaultValues;
        }
        $entity = $this->newEntity($data);

        return $entity;
    }

    public function toFormData($query) {
        
        $data = $query->toArray();
        
        return $data;
    }
    

}