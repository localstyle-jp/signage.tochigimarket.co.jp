<?php 
namespace App\View\Helper;

use Cake\Datasource\ModelAwareTrait;

class ContentMaterialsHelper extends AppHelper
{
    use ModelAwareTrait;
    public function getMaterials($entity){
        $this->loadModel('Materials');
        $this->loadModel('ContentMaterials');
        $cond = [];
        
        foreach($entity->content_materials as $n => $cMaterial){
            if(!empty($cMaterial['material'])){
                continue;
            }
            $cond['OR'][] =['Materials.id' => $cMaterial['material_id']];


        }
        $cm = $this->Materials->find()->where($cond);
        $clist = $cm->toArray();
        
        $list =[];
        foreach($clist as $n => $v){
            if(empty($list[$v['id']])){
                $list[$v['id']] = $v;
            }
        }

        foreach($entity->content_materials as $n => $cMaterial){
            if(empty($cMaterial['material'])){
                $cMaterial['material'] = $list[$cMaterial['material_id']];
            }
        }
        return $entity;
    }


}