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
namespace App\Controller\ShopUser;

use Cake\Core\Configure;
use Cake\Network\Exception\ForbiddenException;
use Cake\Network\Exception\NotFoundException;
use Cake\View\Exception\MissingTemplateException;
use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use Cake\Routing\Router;

use Cake\Auth\DefaultPasswordHasher;

use App\Model\Entity\User;
/**
 * Static content controller
 *
 * This controller will render views from Template/Pages/
 *
 * @link https://book.cakephp.org/3.0/en/controllers/pages-controller.html
 */
class HomeController extends AppController
{
    public function initialize()
    {

        $this->MachineBoxes = $this->getTableLocator()->get('MachineBoxes');
        $this->MachineBoxesUsers = $this->getTableLocator()->get('MachineBoxesUsers');

        parent::initialize();

    }
    
    public function beforeFilter(Event $event) {
        // $this->viewBuilder()->theme('Admin');
        $this->viewBuilder()->setLayout("shop");

        $this->setCommon();
        $this->getEventManager()->off($this->Csrf);
    }

    public function index() {

        $this->User = $this->getTableLocator()->get('Users');
        
        $this->viewBuilder()->setLayout("plain");
        $view = "login";
        $r = array();
        if ($this->request->is('post') || $this->request->is('put')) {
            $data = $this->request->getData();
            if (!empty($data['username']) && !empty($data['password'])) {
                $query = $this->User->find('all', array('conditions' => array('username' => $data['username'],
                                                                              'status' => 'publish'
                                                                             ),
                                                         'limit' => 1));
                $r = $query->first();
                $is_login = false;
                if ($r) {
                    
                    $hasher = new DefaultPasswordHasher();
                    if ($hasher->check($data['password'], $r->password) && $r->role == User::ROLE_SHOP) {
                        $is_login = true;
                    }
                }

                if ($r && $is_login) {
                    $this->Session->write(array('userid' => $r->id,
                                                'data' => array(
                                                    'name' => $r->name,
                                                    'face_image' => $r->attaches['face_image']['s']
                                                ),
                                                'user_role' => $r->role
                                            ));
                    $this->AdminMenu->init();
                } else {
                    $r = false;
                }
            }
            if (empty($r)) {
                $this->Flash->error('ユーザー名またはパスワードが違います');
            }
        }
        if (0 < $this->Session->read('userid')) {
            $this->viewBuilder()->setLayout("shop");
            $view = "index";

            $this->setCommon();

            $this->setList();

            $machines = $this->getMachines();
            $this->set(compact('machines'));
        }
        $this->render($view);
    }

    private function getMachines() {
        $machines = [];

        $site_config_id = $this->getSiteId();
        $user_id = $this->Session->read('userid');

        $machines_ids = [0];
        $_machines_ids = $this->MachineBoxesUsers->find()->where(['MachineBoxesUsers.user_id' => $user_id])->all();
        if (!$_machines_ids->isEmpty()) {
            $machines_ids = $_machines_ids->extract('machine_box_id')->toArray();
        }

        $machines = $this->MachineBoxes->find()->where(['MachineBoxes.site_config_id' => $site_config_id, 'MachineBoxes.id in' => $machines_ids])
                                        ->contain([
                                            'SiteConfigs',
                                            'Contents',
                                            'MachineContents',
                                            ])
                                        ->order(['MachineBoxes.position' => 'ASC'])
                                        ->all();

        return $machines;
    }

    public function logout() {
        if (0 < $this->Session->read('userid')) {
            $this->Session->delete('userid');
            $this->Session->delete('role');
            $this->Session->destroy();
        }
        $this->redirect(['_name' => 'shopAdmin']);
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
