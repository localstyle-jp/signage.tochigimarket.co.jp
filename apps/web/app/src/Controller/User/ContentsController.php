<?php

namespace App\Controller\User;

use Cake\Core\Configure;
use Cake\Network\Exception\ForbiddenException;
use Cake\Network\Exception\NotFoundException;
use Cake\View\Exception\MissingTemplateException;
use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use App\Model\Entity\Material;

/**
 * Static content controller
 *
 * This controller will render views from Template/Pages/
 *
 * @link https://book.cakephp.org/3.0/en/controllers/pages-controller.html
 */
class ContentsController extends AppController
{
    private $list = [];

    public function initialize()
    {
        $this->Materials = $this->getTableLocator()->get('Materials');
        $this->ContentMaterials = $this->getTableLocator()->get('ContentMaterials');
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


        return $query;
    }

    protected function _getConditions($query) {
        $cond = [];
        $cnt = 0;



        return $cond;
    }

    public function index() {
        $this->checkLogin();

        $this->setList();

        $query = $this->_getQuery();
        $cond = $this->_getConditions($query);

        $is_search = ($this->request->getQuery() ? true : false);

        $this->set(compact('query', 'is_search'));

        $this->_lists($cond, ['order' => 'position ASC',
                              'limit' => null]);
    }

    public function edit($id=0) {
        $this->checkLogin();

        $this->setList();
        $get_callback = null;
        $error_callback = null;
        $callback = null;
        $redirect = ['action' => 'index'];
        $rates = [];
        $delete_ids = [];

        $associated = ['ContentMaterials'];

        $user_id = $this->getUserId();
        // $this->_getUserSite($user_id);
        $config_id = $this->getSiteId();
        $this->set(compact('user_id'));
        if ($this->request->is(['post', 'put'])) {
            $this->request->data['site_config_id'] = $config_id;

            if (array_key_exists('content_materials', $this->request->getData())) {
                $position = 0;
                foreach ($this->request->getData('content_materials') as $k => $v) {
                    if ($v['is_delete'] == 1) {
                        if ($v['id']) {
                            $delete_ids[] = $v['id'];
                        } else {
                            unset($this->request->data['content_materials'][$k]);
                        }
                        continue;
                    }
                    $this->request->data['content_materials'][$k]['position'] = ++$position;
                    unset($this->request->data['content_materials'][$k]['is_delete']);
                }
            }
        }

        // 成功時のコールバック
        $callback = function($id) use($delete_ids) {
            // 削除
            if (!empty($delete_ids)) {
                $this->ContentMaterials->deleteAll(['ContentMaterials.content_id' => $id, function($exp) use($delete_ids) {
                    return $exp->in('id', $delete_ids);
                }]);
            }
            // シリアルNo更新
            $this->Contents->serialIncrement($id);
        };

        // エラー時のコールバック
        $error_callback = function($datas) {
            if (!empty($datas['content_materials'])) {
                foreach ($datas['content_materials'] as $k =>  $data) {
                    if (empty($data['material']) && $data['material_id']) {
                        $datas['content_materials'][$k]['material'] = $this->Materials->find()->where(['Materials.id' => $data['material_id']])->first()->toArray();
                    }
                    if (empty($data['id'])) {
                        $datas['content_materials'][$k]['id'] = null;
                    }
                }
            }
            return $datas;
        };
        
        $contain =[
            'ContentMaterials'=> function($q){
                return $q->contain(['Materials'])->order(['ContentMaterials.position' => 'ASC']);
            } 
        ];

        // リダイレクト
        if ($this->request->getQuery('mode') == 'machine') {
            $redirect = ['controller' => 'machine-boxes', 'action' => 'index', '?' => ['sch_content' => $id]];
        }
        
        $options = [
            'callback' => $callback,
            'get_callback' => $get_callback,
            'error_callback' => $error_callback,
            'redirect' => $redirect,
            'associated' => $associated,
            'contain' => $contain
        ];
        
        parent::_edit($id, $options);

    }

    public function addMaterial() {
        $this->viewBuilder()->setLayout("plain");
        $this->setList();

        $rownum = $this->request->getData('rownum');
        $material_id = $this->request->getData('material_id');

        $master = $this->Materials->find()->where(['Materials.id' => $material_id])->first();

        $data = [];
        if (!empty($master)) {
            $data = [
                'id' => null,
                'material_id' => $master->id,
                'position' => 0,
                'view_second' => $master->view_second,
                'rolling_caption' => $master->rolling_caption,
                'material' =>[
                    'type' => $master->type,
                    'name' => $master->name,
                    'image' => $master->image,
                    'movie_tag' => $master->movie_tag,
                    'url' => $master->url,
                    'content' => '',
                    'attaches' => $master->attaches,
                    'status_mp4' => $master->status_mp4,
                    'category_id' => $master->category_id,
                ]
            ];
        }
        $result = $this->ContentMaterials->newEntity($data);
        $result['material']['content'] = $master->content;
        $material = $result->toArray();
        $this->set(compact('rownum', 'material'));
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
            'type_list' => Material::$type_list
        );

        $category_list = [];
        $category_list = $this->MaterialCategories->find('list', ['keyField' => 'id', 'valueField' => 'name'])
                                        ->order(['MaterialCategories.position' => 'ASC'])
                                        ->toArray();

        $list['category_list'] = $category_list;

        if (!empty($list)) {
            $this->set(array_keys($list),$list);
        }

        $this->list = $list;
        return $list;
    }


}
