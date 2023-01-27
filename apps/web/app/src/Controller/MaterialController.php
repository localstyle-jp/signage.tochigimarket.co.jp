<?php

namespace App\Controller;

use Cake\Core\Configure;
use Cake\Network\Exception\ForbiddenException;
use Cake\Network\Exception\NotFoundException;
use Cake\View\Exception\MissingTemplateException;
use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use App\Model\Entity\Material;

/**
 * Static content controller
 *
 * This controller will render views from Template/Pages/
 *
 * @link https://book.cakephp.org/3.0/en/controllers/pages-controller.html
 */
class MaterialController extends AppController {
    private $list = [];

    public function initialize() {
        parent::initialize();

        $this->SiteConfigs = $this->getTableLocator()->get('SiteConfigs');
        $this->Materials = $this->getTableLocator()->get('Materials');

        $this->MachineBoxes = $this->getTableLocator()->get('MachineBoxes');

        $this->modelName = 'Infos';
        $this->set('ModelName', $this->modelName);

        $this->uid = $this->Session->read('uid');
    }

    public function beforeFilter(Event $event) {
        // $this->viewBuilder()->theme('Admin');
        $this->viewBuilder()->setLayout('simple');
        $this->getEventManager()->off($this->Csrf);
    }
    public function detail($id) {
        $material = $this->Materials->find()->where(['Materials.id' => $id])->first();

        if ($material->type != Material::TYPE_PAGE_MOVIE) {
            return $this->error();
        }

        $video = '<video id="page_mp4_' . $id . '"';
        $video .= ' muted';
        $video .= ' width="1000"';
        $video .= ' height="800"';
        $video .= '>';
        $video .= '<source src="' . $material->attaches['file']['src'] . '">';
        $video .= '</video>';

        $this->set(compact('material', 'video'));
        $this->render('detail_' . Material::TYPE_PAGE_MOVIE);
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
            $this->set(array_keys($list), $list);
        }

        $this->list = $list;
        return $list;
    }
}
