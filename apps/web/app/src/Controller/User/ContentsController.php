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
        $callback = null;
        $redirect = ['action' => 'index'];
        $rates = [];

        $associated = ['ContentMaterials'];

        if ($this->request->is(['post', 'put'])) {
            // $this->request->data['site_config_id'] = $this->getSiteId();
            // dd($this->request->getData());
            if (array_key_exists('content_materials', $this->request->getData())) {
                $position = 0;
                
                foreach ($this->request->getData('content_materials') as $k => $v) {
                    $this->request->data['content_materials'][$k]['position'] = ++$position;
                }
            }
        }
        
        $contain =[
            'ContentMaterials'=> function($q){
                return $q->contain(['Materials'])->order(['ContentMaterials.position' => 'ASC']);
            } 
        ];
        
        $options = [
            'callback' => $callback,
            'get_callback' => $get_callback,
            'redirect' => $redirect,
            'associated' => $associated,
            'contain' => $contain
        ];
        
        // dd($this->request->getData());

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
                'material' =>[
                    'type' => $master->type,
                    'name' => $master->name,
                    'image' => $master->image,
                    'movie_tag' => $master->movie_tag,
                    'url' => $master->url,
                    'content' => '',
                    'attaches' => $master->attaches
                ]
            ];
        }
        $result = $this->ContentMaterials->newEntity($data);
        $result['material']['content'] = $master->content;
        $material = $result->toArray();
        // dd($material);
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



        if (!empty($list)) {
            $this->set(array_keys($list),$list);
        }

        $this->list = $list;
        return $list;
    }


}
