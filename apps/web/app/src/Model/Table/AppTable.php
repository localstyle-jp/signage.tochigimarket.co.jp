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
    
    public function copyAttachement($source_id, $distModel) {
        // コピー元
        $source = $this->find()->where([$this->getAlias() . '.id' => $source_id])->first();

        if (empty($source) ) {
            return false;
        }

        // 画像 
        $basedir = UPLOAD_DIR . $this->getAlias() . DS . 'images' . DS;
        $distDir = UPLOAD_DIR . $distModel . DS . 'images' . DS;

        $image_columns = $this->attaches['images'];

        $r = true;

        if (!empty($image_columns)) {
            foreach ($image_columns as $column => $imageConfig) {
                if (empty($source->attaches[$column])) {
                    continue;
                }
                foreach ($source->attaches[$column] as $path) {
                    if (empty($path)) {
                        continue;
                    }
                    $copy = WWW_ROOT . ltrim($path,'/');
                    $dist = str_replace($basedir, $distDir, $copy);
                    copy($copy, $dist);
                }
            }
        }

        // ファイル

        $basedir = UPLOAD_DIR . $this->getAlias() . DS . 'files' . DS;
        $distDir = UPLOAD_DIR . $distModel . DS . 'files' . DS;

        $file_columns = $this->attaches['files'];

        if (!empty($file_columns)) {
            foreach ($file_columns as $column => $config) {
                if (empty($source->attaches[$column])) {
                    continue;
                }
                $src = $source->attaches[$column]['src'];
                if (empty($src)) {
                    continue;
                }
                $copy = WWW_ROOT . ltrim($src,'/');
                $dist = str_replace($basedir, $distDir, $copy);
                copy($copy, $dist);
            }
        }

        return $r;

    }
}