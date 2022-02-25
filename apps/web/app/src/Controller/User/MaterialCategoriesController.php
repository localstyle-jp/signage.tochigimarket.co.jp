<?php

namespace App\Controller\User;

use Cake\Core\Configure;
use Cake\Network\Exception\ForbiddenException;
use Cake\Network\Exception\NotFoundException;
use Cake\View\Exception\MissingTemplateException;
use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use Cake\Filesystem\Folder;

use App\Model\Entity\MaterialCategory;
/**
 * Static content controller
 *
 * This controller will render views from Template/Pages/
 *
 * @link https://book.cakephp.org/3.0/en/controllers/pages-controller.html
 */
class MaterialCategoriesController extends AppController
{
    private $list = [];

    public function initialize()
    {
        parent::initialize();

        $this->Materials = $this->getTableLocator()->get('Materials');
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

        $query['parent_id'] = $this->request->getQuery('parent_id');
        if (empty($query['parent_id'])) {
            $query['parent_id'] = 0;
        }

        return $query;
    }

    protected function _getConditions($query) {
        $cond = [];


        return $cond;
    }

    public function index() {
        $this->checkLogin();

        $query = $this->_getQuery();
        $this->set(compact('query'));

        // if (!$this->isOwnPageByUser($query['sch_page_id'])) {
        //     $this->Flash->set('不正なアクセスです');
        //     $this->redirect('/user_admin/');
        //     return;
        // }

        $cond = [];

        $parent_category = [];
        $cond['MaterialCategories.parent_category_id'] = $query['parent_id'];
        $_parent_id = $query['parent_id'];
        do {

            $tmp = $this->MaterialCategories->find()->where(
                [
                    'MaterialCategories.id' => $_parent_id,
                    ])->first();
            if (!empty($tmp)) {
                $_parent_id = $tmp->parent_category_id;
                $parent_category[] = $tmp;
            }
            
        }while(!empty($tmp));
        $this->set(compact('parent_category'));

        $this->_lists($cond, ['order' => 'position ASC',
                              'limit' => null]);
    }

    public function edit($id=0) {
        $this->checkLogin();

        $query = $this->_getQuery();
        $this->set(compact('query'));

        // if ($id && !$this->isOwnCategoryByUser($id)) {
        //     $this->Flash->set('不正なアクセスです');
        //     $this->redirect('/user_admin/');
        //     return;
        // }

        $this->setList();

        $redirect = ['action' => 'index', '?' => $query];
        if ($this->request->is(['post', 'put'])) {
            $redirect = ['action' => 'index', '?' => ['parent_id' => $this->request->getData('parent_category_id')]];
        }

        $callback = function($id) {
            $data = $this->MaterialCategories->find()->where(['MaterialCategories.id' => $id])->first();
            $entity = $this->MaterialCategories->patchEntity($data, ['identifier' => MaterialCategory::IDENTIFIER . $data->position]);
            $this->MaterialCategories->save($entity);
        };

        $parent_category = [];
        $parent_category = $this->MaterialCategories->find()->where([
                'MaterialCategories.id' => $query['parent_id'],
            ])->first();
        $this->set(compact('parent_category'));

        $options['redirect'] = $redirect;
        $options['callback'] = $callback;

        parent::_edit($id, $options);

    }

    public function position($id, $pos) {
        $this->checkLogin();

        // if ($id && !$this->isOwnCategoryByUser($id)) {
        //     $this->Flash->set('不正なアクセスです');
        //     $this->redirect('/user_admin/');
        //     return;
        // }

        $options = [];

        $data = $this->MaterialCategories->find()->where(['MaterialCategories.id' => $id])->first();
        if (empty($data)) {
            $this->redirect('/user/');
            return;
        }

        $options['redirect'] = ['action' => 'index', '?' => ['sch_page_id' => $data->page_config_id, 'parent_id' => $data->parent_category_id]/*, '#' => 'content-' . $id*/];

        return parent::_position($id, $pos, $options);
    }

    public function enable($id) {
        $this->checkLogin();

        // if ($id && !$this->isOwnCategoryByUser($id)) {
        //     $this->Flash->set('不正なアクセスです');
        //     $this->redirect('/user_admin/');
        //     return;
        // }

        $options = [];

        $data = $this->MaterialCategories->find()->where(['MaterialCategories.id' => $id])->first();
        if (empty($data)) {
            $this->redirect('/user/');
            return;
        }

        $options['redirect'] = ['action' => 'index', '?' => ['sch_page_id' => $data->page_config_id, 'parent_id' => $data->parent_category_id]/*, '#' => 'content-' . $id*/];
        
        parent::_enable($id, $options);

    }

    public function delete($id, $type, $columns = null) {
        $this->checkLogin();

        // if ($id && !$this->isOwnCategoryByUser($id)) {
        //     $this->Flash->set('不正なアクセスです');
        //     $this->redirect('/user_admin/');
        //     return;
        // }

        $data = $this->MaterialCategories->find()->where(['MaterialCategories.id' => $id])->first();
        if (empty($data)) {
            $this->redirect('/user/');
            return;
        }

        $n_underlayer = $this->MaterialCategories->find()->where(['MaterialCategories.parent_category_id' => $id])->count();
        if ($n_underlayer > 0) {
            $this->Flash->set('紐づくカテゴリがあるため削除できません。');
            $this->redirect(['action' => 'index', '?' => ['parent_id' => $id]]);
            return;
        }

        $n_materials = $this->Materials->find()->where(['Materials.category_id' => $id])->count();
        if ($n_materials > 0) {
            $this->Flash->set('紐づく素材があるため削除できません。');
            $this->redirect(['controller' => 'Materials', 'action' => 'index', '?' => ['sch_category_id0' => $id]]);
            return;
        }

        $redirect = ['action' => 'index'];
        
        $options = ['redirect' => $redirect];

        $result = parent::_delete($id, $type, $columns, $options);
        // if (!$result) {
        //     $this->Infos->updateAll(['category_id' => 0, 'status' => 'draft'], ['Infos.category_id' => $data->id]);
        // }
    }


    public function setList() {
        
        $list = array();

        if (!empty($list)) {
            $this->set(array_keys($list),$list);
        }

        $this->list = $list;
        return $list;
    }


}
