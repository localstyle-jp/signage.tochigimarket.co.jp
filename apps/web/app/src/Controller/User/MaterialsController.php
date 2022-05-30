<?php

namespace App\Controller\User;

use Cake\Core\Configure;
use Cake\Network\Exception\ForbiddenException;
use Cake\Network\Exception\NotFoundException;
use Cake\View\Exception\MissingTemplateException;
use Cake\Event\Event;
use Cake\ORM\TableRegistry;

use App\Model\Entity\Media;
use App\Model\Entity\Material;

/**
 * Static content controller
 *
 * This controller will render views from Template/Pages/
 *
 * @link https://book.cakephp.org/3.0/en/controllers/pages-controller.html
 */
class MaterialsController extends AppController
{
    private $list = [];

    public function initialize()
    {
        $this->MaterialCategories = $this->getTableLocator()->get('MaterialCategories');

        parent::initialize();
    }
    
    public function beforeFilter(Event $event) {

        parent::beforeFilter($event);
        // $this->viewBuilder()->theme('Admin');
        $this->viewBuilder()->setLayout("user");

        $this->setCommon();
        $this->getEventManager()->off($this->Csrf);

        $this->modelName = $this->name;
        $this->set('ModelName', $this->modelName);

    }

    protected function _getQuery() {
        $query = [];

        $query['sch_name'] = $this->request->getQuery('sch_name');
        $query['sch_type'] = $this->request->getQuery('sch_type');
        // 一番下層の（連番が一番大きな）カテゴリを検索するカテゴリIDとする
        $query['sch_category_id'] = 0;
        for ($i=0; true ; $i++) { 
            if( !$this->request->getQuery('sch_category_id' . $i) ) {break;}
            $query['sch_category_id'] = $this->request->getQuery('sch_category_id' . $i);
        }
        $query['sch_modified_start'] = $this->checkDate($this->request->getQuery('sch_modified_start')) ?
                                            $this->request->getQuery('sch_modified_start') : 
                                            null;
        $query['sch_modified_end'] = $this->checkDate($this->request->getQuery('sch_modified_end')) ?
                                            $this->request->getQuery('sch_modified_end') : 
                                            null;
        $query['sch_created_start'] = $this->checkDate($this->request->getQuery('sch_created_start')) ?
                                            $this->request->getQuery('sch_created_start') : 
                                            null;
        $query['sch_created_end'] = $this->checkDate($this->request->getQuery('sch_created_end')) ?
                                            $this->request->getQuery('sch_created_end') : 
                                            null;

        return $query;
    }

    protected function checkDate($date) {
        $is_match = preg_match('/^(\d{4})[\/-](\d{2})[\/-](\d{2})$/', $date, $match);
        if($is_match && checkdate($match[2], $match[3], $match[1])){
            return true;
        }
        return false;
    }

    protected function _getConditions($query) {
        $cond = [];
        $cnt = 0;

        $site_config_id = $this->getSiteId();

        $cond[$cnt++]['Materials.site_config_id'] = $site_config_id;

        if ($query['sch_name']) {
            $cond[$cnt++]['Materials.name like'] = "%{$query['sch_name']}%";
        }

        if (!empty($query['sch_type'])) {
            $cond[$cnt++]['Materials.type'] = $query['sch_type'];
        }

        if ($query['sch_category_id']) {
            $cond[$cnt++]['Materials.category_id'] = $query['sch_category_id'];
        }

        if ($query['sch_modified_start']) {
            $cond[$cnt++]['Materials.modified >= '] = $query['sch_modified_start'] . ' 00:00:00';
        }

        if ($query['sch_modified_end']) {
            $cond[$cnt++]['Materials.modified <= '] = $query['sch_modified_end'] . ' 23:59:59';
        }

        if ($query['sch_created_start']) {
            $cond[$cnt++]['Materials.created >= '] = $query['sch_created_start'] . ' 00:00:00';
        }

        if ($query['sch_created_end']) {
            $cond[$cnt++]['Materials.created <= '] = $query['sch_created_end'] . ' 23:59:59';
        }

        return $cond;
    }

    public function index() {
        $this->checkLogin();

        $this->setList();

        $query = $this->_getQuery();
        $cond = $this->_getConditions($query);

        $is_search = false;
        foreach ($query as $k => $v) {
            if (empty($v)) {
                continue;
            }
            if (preg_match('/^sch_/', $k)) {
                $is_search = true;
            }
        }
        // $is_search = ($this->request->getQuery() ? true : false);

        $this->setCategoryListForSearch($query['sch_category_id']);

        $this->set(compact('query', 'is_search'));

        $options = [
            'contain' => ['MaterialCategories'],
            'order' => ['position' => 'ASC'],
            'limit' => 20
        ];

        $this->_lists($cond, $options);
    }

    public function edit($id=0) {
        $this->checkLogin();

        $this->setList();
        $this->setCategoryListDefault();
        $get_callback = null;
        $callback = null;
        $redirect = ['action' => 'index'];
        $rates = [];
        $validate = 'default';

        if ($this->request->is(['put', 'post'])) {
            // mp4動画のステータス
            if ($this->request->getData('type') == Material::TYPE_MOVIE_MP4 && !empty($this->request->getData('file')['tmp_name'])) {
                $this->request->data['status_mp4'] = 'converting';
            }

            $site_config_id = $this->getSiteId();
            $this->request->data['site_config_id'] = $site_config_id;

            $post_data = $this->request->getData();

            if (array_key_exists($post_data['type'], Material::$validation_list)) {
                $validate = Material::$validation_list[$post_data['type']];
                if ($id) {
                    $validate .= 'Update';
                } else {
                    $validate .= 'New';
                }
            }

        }

        $callback = function($id) {
            $entity = $this->Materials->find()->where(['Materials.id' => $id])->first();
            if ($entity->type == Material::TYPE_PAGE_MOVIE) {
                $material = $this->Materials->patchEntity($entity, ['url' => '/material/detail/' . $id]);
                $this->Materials->save($material);
            }
            // MP4の変換
            if ($entity->type == Material::TYPE_MOVIE_MP4 && empty($entity->url)) {
                $this->Materials->setMp4($entity->toArray());
            }
            // MP4分割
            if ($entity->type == Material::TYPE_MOVIE_MP4) {
                $cakeCommPath = ROOT . DS . 'bin/cake';
                $a = exec(
                    'nohup ' .
                        $cakeCommPath . ' convert_mp4 ' .
                        $entity->id . ' ' .
                        WWW_ROOT . UPLOAD_BASE_URL . DS . 'Materials' . DS . 'files' . DS . ' ' .
                        WWW_ROOT . UPLOAD_MOVIE_BASE_URL . DS . 'm' . $id . DS . ' ' .
                        $entity->file . ' ' .
                        '1>>' . LOGS . 'mp4_conversion.log ' . 
                        '2>/dev/null ' . 
                        // '2>>' . LOGS . 'mp4_conversion_debug.log ' .
                        '&',
                    $output, 
                    $status_code
                );
            }
        };

        $associated = [];

        $options = [
            'callback' => $callback,
            'get_callback' => $get_callback,
            'redirect' => $redirect,
            'associated' => $associated,
            'validate' => $validate
        ];

        parent::_edit($id, $options);

    }


    public function position($id, $pos) {
        $this->checkLogin();

        $options = [];

        return parent::_position($id, $pos, $options);
    }

    public function enable($id) {
        $this->checkLogin();

        $options = [];
        
        parent::_enable($id, $options);

    }

    public function delete($id, $type, $columns = null) {
        $this->checkLogin();
        
        $options = [];

        return parent::_delete($id, $type, $columns, $options);
    }


    public function setList() {
        
        $list = array(
            'type_list' => Material::$type_list,
        );

        if (!empty($list)) {
            $this->set(array_keys($list),$list);
        }

        $this->list = $list;
        return $list;
    }

    public function setCategoryListDefault() {
        $category_list = [];
        $category_list = $this->MaterialCategories->find('list', ['keyField' => 'id', 'valueField' => 'name'])
                                        ->order(['MaterialCategories.position' => 'ASC'])
                                        ->toArray();

        $this->set(compact('category_list'));
    }

    public function setCategoryListForSearch($category_id) {
        // 現在カテゴリ情報
        $category = $this->MaterialCategories->find()->where(['MaterialCategories.id' =>$category_id])->first();

        $pankuzu_category = [];
        $category_list = [];
        // $is_data = true;
        if ($category) {
            $_parent_id = $category->parent_category_id;
            $pankuzu_category[] = $category;
            do {
                $tmp = $this->MaterialCategories->find()->where([
                            'MaterialCategories.id' => $_parent_id,
                        ])->first();
                if (!empty($tmp)) {
                    $_parent_id = $tmp->parent_category_id;
                    $pankuzu_category[] = $tmp;
                }
                
            } while(!empty($tmp));
            
            while($pcat = array_pop($pankuzu_category)) {
                $category_cond['MaterialCategories.parent_category_id'] = $pcat->parent_category_id;
                $tmp = $this->MaterialCategories->find('list', ['keyField' => 'id', 'valueField' => 'name'])
                                        ->where($category_cond)
                                        ->order(['MaterialCategories.position' => 'ASC'])
                                        ->all();
                if ($tmp->isEmpty()) {
                    $tmp = null;
                } elseif ($pcat->parent_category_id===0) {
                    $category_list[] = [
                        'category' => $pcat,
                        'list' => $tmp->toArray(),
                        'empty' => ['0' => '全て']
                    ];
                } else {
                    $category_list[] = [
                        'category' => $pcat,
                        'list' => $tmp->toArray(),
                        'empty' => false
                    ];
                }
            }

            // 最後に現カテゴリに下層カテゴリがあれば追加
            $category_cond['MaterialCategories.parent_category_id'] = $category->id;
            $tmp = $this->MaterialCategories->find('list', ['keyField' => 'id', 'valueField' => 'name'])
                                    ->where($category_cond)
                                    ->order(['MaterialCategories.position' => 'ASC'])
                                    ->all();
            if (!$tmp->isEmpty()) {
                $category_list[] = [
                    'category' => (object)['id' => 0],
                    'list' => $tmp->toArray(),
                    'empty' => ['' => '選択してください']
                ];
                // $is_data = false;
            }
        } else {        // カテゴリを絞り込まないとき
            $tmp = $this->MaterialCategories->find('list', ['keyField' => 'id', 'valueField' => 'name'])
                                    ->where(['MaterialCategories.parent_category_id' => 0])
                                    ->order(['MaterialCategories.position' => 'ASC'])
                                    ->all();
            $category_list[] = [
                'category' => (object)['id' => 0],
                'list' => $tmp->toArray(),
                'empty' => ['0' => '全て']
            ];
        }
        
        $this->set(compact('category_list'));
        return $category_list;
    }

    public function popList() {
        $this->viewBuilder()->setLayout("pop");

        $this->setList();

        $query = $this->_getQueryPop();

        $this->setCategoryListForSearch($query['sch_category_id']);
        
        $cond = $this->_getConditionsPop($query);
        $this->set(compact('query'));

        $options = [
            'contain' => ['MaterialCategories'],
            'limit' => 10, 
            'order' => ['Materials.position' => 'ASC']
        ];

        $this->_lists($cond, $options);

    }
    private function _getQueryPop() {
        $query = [];

        $query = $this->_getQuery();

        return $query;
    }

    private function _getConditionsPop($query) {
        $cond = [];

        $cond = $this->_getConditions($query);
        $cnt = count($cond);
        if (!$query['sch_type']) {
            $cond[$cnt++]['Materials.type !='] = Material::TYPE_SOUND;
        }
        return $cond;
    }

    public function changeCategoryInput() {
        $this->viewBuilder()->setLayout("plain");
        
        $category_id = $this->request->getData('category_id');

        $this->setCategoryListForSearch($category_id);
        // $datas = [];
        
        $this->set(compact('category_id'));
    }

    public function getYearList(){
        $result = [];
        $first_mate = $this->Materials->find()
                            ->order(['Materials.created' => 'ASC'])
                            ->first();

        $first_year = (new \DateTime($first_mate['created']))->format('Y');
        $last_year = (new \DateTime('now'))->format('Y');

        for ($i=$first_year; $i <= $last_year; $i++) { 
            $result[$i] = $i;
        }

        return $result;
    }

}
