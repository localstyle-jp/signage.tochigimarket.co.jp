<?php

namespace App\Controller\V1;

use Cake\Core\Configure;
use Cake\Network\Exception\ForbiddenException;
use Cake\Network\Exception\NotFoundException;
use Cake\View\Exception\MissingTemplateException;
use Cake\Event\Event;
use Cake\ORM\TableRegistry;
/**
 * Static content controller
 *
 * This controller will render views from Template/Pages/
 *
 * @link https://book.cakephp.org/3.0/en/controllers/pages-controller.html
 */
class LoggingController extends AppController
{
    private $list = [];

    public function initialize()
    {
        parent::initialize();

        $this->Customers = $this->getTableLocator()->get('Customers');
        $this->CustomerLogs = $this->getTableLocator()->get('CustomerLogs');

        $this->modelName = 'Customers';
        $this->set('ModelName', $this->modelName);

    }
    
    public function beforeFilter(Event $event) {
        // $this->viewBuilder()->theme('Admin');
        $this->viewBuilder()->setLayout("simple");

        $this->getEventManager()->off($this->Csrf);

    }

    public function write() {
        $this->Session->read();// セッションIDを取得するために書いておく

        $model = $this->getJson('model');
        $id = $this->getJson('id');
        $url = $this->getJson('url');

        if (empty($model)) {
            $model = '';
            $id = 0;
        }

        $this->logging($id, $model, $url);

        $this->rest_success([]);
    }

}
