<?php
/**
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link      https://cakephp.org CakePHP(tm) Project
 * @since     0.2.9
 * @license   https://opensource.org/licenses/mit-license.php MIT License
 */
namespace App\Controller;

use Cake\Core\Configure;
use Cake\Network\Exception\ForbiddenException;
use Cake\Network\Exception\NotFoundException;
use Cake\View\Exception\MissingTemplateException;
use Cake\Auth\DefaultPasswordHasher;

/**
 * Static content controller
 *
 * This controller will render views from Template/Pages/
 *
 * @link https://book.cakephp.org/3.0/en/controllers/pages-controller.html
 */
class HomesController extends AppController {
    public function initialize() {
        $this->Departments = $this->getTableLocator()->get('Departments');
        $this->Categories = $this->getTableLocator()->get('Categories');
        $this->Documents = $this->getTableLocator()->get('Documents');
        $this->Folders = $this->getTableLocator()->get('Folders');
        $this->Customers = $this->getTableLocator()->get('Customers');
        $this->FolderCategories = $this->getTableLocator()->get('FolderCategories');
        $this->CustomerLogs = $this->getTableLocator()->get('CustomerLogs');

        parent::initialize();
    }

    public function index() {
        $this->Customers = $this->getTableLocator()->get('Customers');

        $this->viewBuilder()->setLayout('simple');
        $view = 'login';
        $r = array();
        if ($this->request->is('post') || $this->request->is('put')) {
            $data = $this->request->getData();
            if (!empty($data['username']) && !empty($data['password'])) {
                $query = $this->Customers->find('all', array('conditions' => array('Customers.username' => $data['username'],
                    'Customers.status' => 'publish'
                ),
                    'limit' => 1));
                $r = $query->first();
                $is_login = false;
                if ($r) {
                    $hasher = new DefaultPasswordHasher();
                    if ($hasher->check($data['password'], $r->password)) {
                        $is_login = true;
                    }
                }

                if ($r && $is_login) {
                    $this->Session->write(array('customer_id' => $r->id,
                        'data' => array(
                            'name' => $r->name
                        )
                    ));
                } else {
                    $r = false;
                }
            }
            if (empty($r)) {
                $this->Flash->set('アカウント名またはパスワードが違います');
            }
        }
        if (0 < $this->Session->read('customer_id')) {
            // $this->viewBuilder()->setLayout("user");
            $view = 'index';
        }
        $this->render($view);
    }

    public function logout() {
        if (0 < $this->Session->read('customer_id')) {
            $this->Session->delete('customer_id');
            $this->Session->destroy();
        }
        $this->redirect('/');
    }

    public function loglink() {
        $this->Session->read();

        $url = $this->request->getQuery('url');
        $model = $this->request->getQuery('model');
        $id = $this->request->getQuery('id');
        $action = $this->request->getQuery('action');

        if (!$model) {
            $model = 'link';
        }
        if (!$id) {
            $id = 0;
        }
        if (!$action) {
            $action = '';
        }

        $this->logging($id, $model, $url, $action);

        return $this->redirect($url);
    }
}
