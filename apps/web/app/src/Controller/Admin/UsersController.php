<?php

namespace App\Controller\Admin;

use Cake\Core\Configure;
use Cake\Network\Exception\ForbiddenException;
use Cake\Network\Exception\NotFoundException;
use Cake\View\Exception\MissingTemplateException;
use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use Cake\Utility\Hash;
use App\Model\Entity\User;
/**
 * Static content controller
 *
 * This controller will render views from Template/Pages/
 *
 * @link https://book.cakephp.org/3.0/en/controllers/pages-controller.html
 */
class UsersController extends AppController
{
    private $list = [];

    public function initialize()
    {
        parent::initialize();

        $this->modelName = $this->name;
        $this->set('ModelName', $this->modelName);
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
        
        parent::_lists($cond, array('order' => array($this->modelName.'.id' =>  'ASC'),
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
    public function edit($id = 0) {
        $this->checkLogin();

        $this->setList();


        $site_config_ids = [];
        $validate = null;

        if ($this->request->is(['post', 'put'])) {

            if ($id) {
                if ($this->request->getData('_password')) {
                    $this->request->data['password'] = $this->request->getData('_password');
                    $validate = 'modifyIsPass';
                } else {
                    $validate = 'modify';
                }
            } else {
                $validate = 'new';
                $this->request->data['password'] = $this->request->getData('_password');
            }
        }


        return parent::_edit($id, [ 'validate' => $validate]);
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
        
        return parent::_enable($id);
    }

    public function setList() {
        
        $list = array();

        $list['role_list'] = User::$role_list;


        if (!empty($list)) {
            $this->set(array_keys($list),$list);
        }

        $this->list = $list;
        return $list;
    }
}
