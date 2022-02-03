<?php

namespace App\Controller\V1;

use Cake\Core\Configure;
use Cake\Network\Exception\ForbiddenException;
use Cake\Network\Exception\NotFoundException;
use Cake\View\Exception\MissingTemplateException;
use Cake\Event\Event;
// use Cake\ORM\TableRegistry;
use Cake\Auth\DefaultPasswordHasher;
use Cake\Utility\Hash;
/**
 * Static content controller
 *
 * This controller will render views from Template/Pages/
 *
 * @link https://book.cakephp.org/3.0/en/controllers/pages-controller.html
 */
class ManagementController extends AppController
{
    private $list = [];

    public function initialize()
    {
        parent::initialize();

        $this->MachineBoxes = $this->getTableLocator()->get('MachineBoxes');
        $this->SiteConfigs = $this->getTableLocator()->get('SiteConfigs');
        $this->Contents = $this->getTableLocator()->get('Contents');

        $this->modelName = 'MachineBoxes';
        $this->set('ModelName', $this->modelName);

    }
    
    public function beforeFilter(Event $event) {
        // $this->viewBuilder()->theme('Admin');
        $this->viewBuilder()->setLayout("plain");

        $this->getEventManager()->off($this->Csrf);
    
    }

    public function isReload() {
        $id = $this->request->getData('id');

        $machine_box = $this->MachineBoxes->find()->where(['MachineBoxes.id' => $id])->first();
        if (empty($machine_box)) {
            return $this->rest_error(200, 1000);
        }

        $reload_flag = $machine_box->reload_flag;

        $result = [
            'reload_flag' => $reload_flag
        ];
        $this->rest_success($result);
    }

    public function disableReload() {
        $id = $this->request->getData('id');

        $machine_box = $this->MachineBoxes->find()->where(['MachineBoxes.id' => $id])->first();
        if (empty($machine_box)) {
            return $this->rest_error(200, 1000);
        }

        $reload_flag = $machine_box->reload_flag;

        // フラグが経ってたら戻す
        if ($machine_box->reload_flag == 1) {
            $entity = $this->MachineBoxes->patchEntity($machine_box, ['reload_flag' => 0]);
            $this->MachineBoxes->save($entity);
        }

        $this->rest_success([]);
    }

    /**
     * ブラウザ上のコンテンツプレビューをリロードするかどうかを判定する
     */
    public function isReloadContent() {
        $id = $this->request->getData('id');
        $serial_no = $this->request->getData('serial_no');

        $content = $this->Contents->find()->where(['Contents.id' => $id])->first();
        if (empty($content)) {
            return $this->rest_error(200, 1000);
        }

        $reload_flag = ($content->serial_no != $serial_no);

        $result = [
            'reload_flag' => $reload_flag
        ];
        $this->rest_success($result);
    }
}
