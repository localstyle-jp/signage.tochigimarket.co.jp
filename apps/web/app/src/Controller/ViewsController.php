<?php

namespace App\Controller;

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
class ViewsController extends AppController
{
    private $list = [];

    public function initialize()
    {
        parent::initialize();

        $this->SiteConfigs = $this->getTableLocator()->get('SiteConfigs');
        $this->Contents = $this->getTableLocator()->get('Contents');
        $this->UserSites = $this->getTableLocator()->get('UserSites');
        $this->MachineBoxes = $this->getTableLocator()->get('MachineBoxes');


        $this->modelName = 'Infos';
        $this->set('ModelName', $this->modelName);

        $this->uid = $this->Session->read('uid');


    }
    
    public function beforeFilter(Event $event) {
        // $this->viewBuilder()->theme('Admin');
        $this->viewBuilder()->setLayout("simple");
        $this->getEventManager()->off($this->Csrf);

    }
    public function index($slug, $path) {

        $site_config = $this->SiteConfigs->find()->where(['SiteConfigs.slug' => $slug])->first();
        if (empty($site_config)) {
            throw new NotFoundException('ページが見つかりません');
        }

        // マシンBOX
        $_path[] = trim($path, '/');
        $_path[] = '/' . $_path[0] . '/';
        $_path[] = $_path[0] . '/';
        $_path[] = '/' . $_path[0];

        $cond = ['OR' => [
            ['MachineBoxes.url' => $_path[0]],
            ['MachineBoxes.url' => $_path[1]],
            ['MachineBoxes.url' => $_path[2]],
            ['MachineBoxes.url' => $_path[3]],
        ]];
        $machine = $this->MachineBoxes->find()->where($cond)->first();
        if (empty($machine)) {
            throw new NotFoundException('ページが見つかりません');
        }

        // コンテンツ
        $content = $this->Contents->find()->where(['Contents.id' => $machine->content_id])
                                    ->contain(['ContentMaterials' => function($q) {
                                        return $q->contain(['Materials'])->order(['ContentMaterials.position' => 'ASC']);
                                    }])
                                    ->first();

        $query = $this->_getQuery();

        $this->set(compact('site_config', 'machine', 'content', 'query'));


        
    }

    public function error() {
        throw new NotFoundException('ページが見つかりません');
    }


    private function _getQuery() {
        $query = [];

        return $query;
    }



  

    public function setList() {
        
        $list = array();

        $list['block_type_list'] = Info::getBlockTypeList();

        if (!empty($list)) {
            $this->set(array_keys($list),$list);
        }

        $this->list = $list;
        return $list;
    }



 

}
