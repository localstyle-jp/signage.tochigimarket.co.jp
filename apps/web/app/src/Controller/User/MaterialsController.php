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

        return $cond;
    }

    public function index() {
        $this->checkLogin();

        $this->setList();

        $query = $this->_getQuery();
        $cond = $this->_getConditions($query);

        $is_search = ($this->request->getQuery() ? true : false);

        $this->set(compact('query', 'is_search'));

        $this->_lists($cond, ['order' => ['position' => 'ASC'],
                              'limit' => 20]);
    }

    public function edit($id=0) {
        $this->checkLogin();

        $this->setList();
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
            'type_list' => Material::$type_list
        );



        if (!empty($list)) {
            $this->set(array_keys($list),$list);
        }

        $this->list = $list;
        return $list;
    }

    public function popList() {
        $this->viewBuilder()->setLayout("pop");

        $this->setList();

        $query = $this->_getQueryPop();
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

}
