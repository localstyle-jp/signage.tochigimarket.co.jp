<?php

namespace App\Controller\Admin;

use Cake\Core\Configure;
use Cake\Network\Exception\ForbiddenException;
use Cake\Network\Exception\NotFoundException;
use Cake\View\Exception\MissingTemplateException;
use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use Cake\Filesystem\Folder;
use Cake\Routing\RequestActionTrait;
/**
 * Static content controller
 *
 * This controller will render views from Template/Pages/
 *
 * @link https://book.cakephp.org/3.0/en/controllers/pages-controller.html
 */
class SiteConfigsController extends AppController
{
    private $list = [];

    public function initialize()
    {
        parent::initialize();

        $this->PageConfigs = $this->getTableLocator()->get('PageConfigs');
        $this->Infos = $this->getTableLocator()->get('Infos');


        $this->modelName = $this->name;
        $this->set('ModelName', $this->modelName);

        $this->loadComponent('OutputHtml');
    }
    
    public function beforeFilter(Event $event) {
        // $this->viewBuilder()->theme('Admin');
        $this->viewBuilder()->setLayout("admin");
        $this->getEventManager()->off($this->Csrf);


    }
    public function index() {
        $this->checkLogin();
        
        $this->setList();

        $query = $this->_getQuery();

        $this->_setView($query);

        $cond = array();

        $cond = $this->_getConditions($query);
        
        return parent::_lists($cond, array('order' => array($this->modelName.'.id' =>  'ASC'),
                                            'limit' => null));
    }
    private function _getQuery() {
        $query = [];

        return $query;
    }

    private function _getConditions($query) {
        $cond = [];

        extract($query);


        return $cond;
    }
    public function edit($id=0) {
        $this->checkLogin();
        $validate = 'default';

        $this->setList();

        $old_data = null;

        if ($this->request->is(['post', 'put'])) {
            $old_data = $this->SiteConfigs->find()->where(['SiteConfigs.id' => $id])->first();
            if ($this->request->getData('is_root') == 1) {
                $validate = 'isRoot';
            }
        }


        $options['validate'] = $validate;

        parent::_edit($id, $options);
        $this->render('edit');

    }

    public function delete($id, $type, $columns = null) {
        $this->checkLogin();
        
        return parent::_delete($id, $type, $columns);
    }

    public function position($id, $pos) {
        $this->checkLogin();

        return parent::_position($id, $pos);
    }

    public function enable($id) {
        $this->checkLogin();
        
        parent::_enable($id);
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
