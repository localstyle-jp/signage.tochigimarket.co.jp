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
        $query['sch_category_id'] = $this->request->getQuery('sch_category_id');
        // if (!$query['sch_category_id']) {
        //     $category = $this->MaterialCategories->find()->order(['MaterialCategories.position' => 'ASC'])->first();
        //     if (!empty($category)) {
        //         $query['sch_category_id'] = $category->id;
        //     }
        // }
        $query['sch_modified_year'] = $this->request->getQuery('sch_modified_year');
        $query['sch_modified_month'] = $this->request->getQuery('sch_modified_month');
        if (!$query['sch_modified_year']) {
            $query['sch_modified_month'] = 0;
        }
        $query['sch_created_year'] = $this->request->getQuery('sch_created_year');
        $query['sch_created_month'] = $this->request->getQuery('sch_created_month');
        if (!$query['sch_created_year']) {
            $query['sch_created_month'] = 0;
        }

        return $query;
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

        if ($query['sch_modified_year']) {
            $month = empty($query['sch_modified_month']) ?
                         ['s' => '01', 'e' => '12'] :
                         ['s' => str_pad($query['sch_modified_month'], 2, 0, STR_PAD_LEFT), 'e' => str_pad($query['sch_modified_month'], 2, 0, STR_PAD_LEFT)];

            $cond[$cnt++]['Materials.modified >= '] = $query['sch_modified_year'] . '-' . $month['s'] . '-01 00:00';
            $cond[$cnt++]['Materials.modified <= '] = $query['sch_modified_year'] . '-' . $month['e'] . '-31 23:59';
        }

        if ($query['sch_created_year']) {
            $month = empty($query['sch_created_month']) ?
                         ['s' => '01', 'e' => '12'] :
                         ['s' => str_pad($query['sch_created_month'], 2, 0, STR_PAD_LEFT), 'e' => str_pad($query['sch_created_month'], 2, 0, STR_PAD_LEFT)];

            $cond[$cnt++]['Materials.created >= '] = $query['sch_created_year'] . '-' . $month['s'] . '-01 00:00';
            $cond[$cnt++]['Materials.created <= '] = $query['sch_created_year'] . '-' . $month['e'] . '-31 23:59';
        }

        return $cond;
    }

    public function index() {
        $this->checkLogin();

        $this->setList();

        $query = $this->_getQuery();
        $cond = $this->_getConditions($query);

        $is_search = ($this->request->getQuery() ? true : false);

        $this->setCategoryListForSearch($query);

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
            // Webmの変換
            if ($entity->type == Material::TYPE_MOVIE_WEBM && empty($entity->url)) {
                $this->Materials->setWebm($entity->toArray());
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
    
        $list['year_list'] = $this->getYearList();
        $list['month_list'] = array_combine(range(1,12), range(1, 12));

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

    public function setCategoryListForSearch($query) {
        // 現在カテゴリ情報
        $category = $this->MaterialCategories->find()->where(['MaterialCategories.id' =>$query['sch_category_id']])->first();

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
    }

    public function popList() {
        $this->viewBuilder()->setLayout("pop");

        $this->setList();

        $query = $this->_getQueryPop();

        $this->setCategoryListForSearch($query);
        
        $cond = $this->_getConditionsPop($query);
        $this->set(compact('query'));

        $this->_lists($cond, ['limit' => 10, 'order' => ['Materials.position' => 'ASC']]);

    }
    private function _getQueryPop() {
        $query = [];

        $query = $this->_getQuery();

        return $query;
    }

    private function _getConditionsPop($query) {
        $cond = [];

        $cond = $this->_getConditions($query);
        return $cond;
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
