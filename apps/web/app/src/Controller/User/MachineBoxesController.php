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
use App\Model\Entity\MachineBox;

/**
 * Static content controller
 *
 * This controller will render views from Template/Pages/
 *
 * @link https://book.cakephp.org/3.0/en/controllers/pages-controller.html
 */
class MachineBoxesController extends AppController
{
    private $list = [];

    public function initialize()
    {

        $this->Contents = $this->getTableLocator()->get('Contents');
        $this->ContentMaterials = $this->getTableLocator()->get('ContentMaterials');
        $this->SiteConfigs = $this->getTableLocator()->get('SiteConfigs');
        $this->MachineContents = $this->getTableLocator()->get('MachineContents');
        $this->MachineMaterials = $this->getTableLocator()->get('MachineMaterials');
        $this->Materials = $this->getTableLocator()->get('Materials');


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
        $query['sch_content'] = $this->request->getQuery('sch_content');


        return $query;
    }

    protected function _getConditions($query) {
        $cond = [];
        $cnt = 0;

        if ($query['sch_content']) {
            $cond[$cnt++]['MachineBoxes.content_id'] = $query['sch_content'];
        }

        return $cond;
    }

    public function index() {
        $this->checkLogin();

        $this->setList();

        $query = $this->_getQuery();
        $cond = $this->_getConditions($query);

        $contain = [
            'Contents',
            'MachineContents'
        ];

        $is_search = false;
        foreach ($query as $k => $v) {
            if (empty($v)) {
                continue;
            }
            if (preg_match('/^sch_/', $k)) {
                $is_search = true;
            }
        }
        // $is_search = ($this->request->getQuery() ? true : false);

        $site_config_id = $this->getSiteId();
        $site_config = $this->SiteConfigs->find()->where(['SiteConfigs.id' => $site_config_id])->first();

        $this->set(compact('query', 'is_search', 'site_config'));

        $this->_lists($cond, ['order' => ['MachineBoxes.position' =>  'ASC'],
                              'limit' => null,
                              'contain' => $contain
                          ]);
    }

    public function edit($id=0) {
        $this->checkLogin();

        $this->setList();
        $get_callback = null;
        $callback = null;
        $redirect = ['action' => 'index'];
        $rates = [];

        $associated = [];

        $site_config_id = $this->getSiteId();
        $site_config = $this->SiteConfigs->find()->where(['SiteConfigs.id' => $site_config_id])->first();

        if ($this->request->is(['put', 'post'])) {
            $this->request->data['site_config_id'] = $site_config_id;
            // 解像度
            if ($this->request->getData('resolution') > 0) {
                $_resolution = $this->list['resolution_list'][$this->request->getData('resolution')];
                $resolution = explode('x', $_resolution);
                $this->request->data['width'] = $resolution[0];
                $this->request->data['height'] = $resolution[1];
            }
        }

        $callback = function($id) {
            // MachineContentsなければ作る
            $entity = $this->MachineBoxes->find()->where(['MachineBoxes.id' => $id])->first();
            if (empty($entity->machine_content_id)) {
                $machine_content = $this->MachineContents->newEntity($this->MachineContents->defaultValues + ['reload_flag' => 1, 'reload_flag_device' => 1]);
                $this->MachineContents->save($machine_content);
                // MachineBox紐付け
                $machine_box = $this->MachineBoxes->patchEntity($entity, ['machine_content_id' => $machine_content->id, 'reload_flag' => 1, 'reload_flag_device' => 1]);
                $this->MachineBoxes->save($machine_box);
            } else {
                $machine_content = $this->MachineContents->find()->where(['MachineContents.id' => $entity->machine_content_id])->first();
                $machine_box = $this->MachineBoxes->patchEntity($entity, ['machine_content_id' => $machine_content->id, 'reload_flag' => 1, 'reload_flag_device' => 1]);
                $this->MachineBoxes->save($machine_box);
            }
            // Contents → MachineContents
            $this->transferMachine($entity->content_id, $machine_content->id);
        };

        $options = [
            'callback' => $callback,
            'get_callback' => $get_callback,
            'redirect' => $redirect,
            'associated' => $associated
        ];

        $this->set(compact('site_config'));

        parent::_edit($id, $options);

    }

    public function updateContent($id) {

        $box = $this->MachineBoxes->find()->where(['MachineBoxes.id' => $id])->first();

        if (!empty($box)) {
            $machine_box = $this->MachineBoxes->patchEntity($box, [/*'reload_flag' => 1, */'reload_flag_device' => 1]);
            $this->MachineBoxes->save($machine_box);

            $this->transferMachine($box->content_id, $box->machine_content_id);
        }

        return $this->redirect(['action' => 'index']);
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
        
        $list = array();

        $config_id = $this->getSiteId();
        $list['content_list'] = $this->Contents->find('list')->where(['Contents.site_config_id' => $config_id])->order(['Contents.position' => 'ASC'])->all()->toArray();

        $list['resolution_list'] = MachineBox::$resolution_list;

        if (!empty($list)) {
            $this->set(array_keys($list),$list);
        }

        $this->list = $list;
        return $list;
    }

    /**
     * [transferMachine description]
     * @param  [type] $source_id 転送元コンテンツID
     * @param  [type] $dest      転送先マシンコンテンツEntity
     * @return [type]            [description]
     */
    private function transferMachine($source_id, $dest_id) {
        if (empty($source_id)) {
            return false;
        }
        // try {
            $source = $this->Contents->get($source_id);
            if (empty($source)) {
                return false;
            }

            $update = $source->toArray();
            unset($update['id']);
            unset($update['created']);
            unset($update['modified']);

            // 転送先
            $dest = $this->MachineContents->find()->where(['MachineContents.id' => $dest_id])->first();
            if (empty($dest)) {
                return false;
            }

            // 転送先コンテンツ素材を削除
            $dest_materials = $this->MachineMaterials->find()->where(['MachineMaterials.machine_content_id' => $dest_id])->all();
            if (!empty($dest_materials)) {
                foreach ($dest_materials as $data) {
                    $this->modelName = 'MachineMaterials';
                    $this->_delete($data->id, 'content', null, ['redirect' => false]);
                }
            }

            $entity = $this->MachineContents->patchEntity($dest, $update);

            $r = $this->MachineContents->save($entity);

            if (!$r) {
                return false;
            }

            $content_materials = $this->ContentMaterials->find()->where(['ContentMaterials.content_id' => $source_id])->contain(['Materials'])->all();
            if ($content_materials->isEmpty()) {
                return false;
            }

            foreach ($content_materials as $source_material) {
                $create = $source_material->material->toArray();
                unset($create['id']);
                unset($create['created']);
                unset($create['modified']);
                $material = $this->MachineMaterials->newEntity($create);
                $material->machine_content_id = $entity->id;
                $material->position = $source_material->position;
                $material->view_second = $source_material->view_second;
                $material->rolling_caption = $source_material->rolling_caption;
                $material->sound = $source_material->sound;
                $this->MachineMaterials->save($material);

                // 画像コピー
                $this->Materials->copyAttachement($source_material->material->id, 'MachineMaterials');
            }
        // } catch (Exception $e) {

        // }

        return true;
    }
}
