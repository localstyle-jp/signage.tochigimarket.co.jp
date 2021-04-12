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
class ContentController extends AppController
{
    private $list = [];

    public function initialize()
    {
        parent::initialize();

        $this->SiteConfigs = $this->getTableLocator()->get('SiteConfigs');
        $this->Contents = $this->getTableLocator()->get('Contents');
        $this->UserSites = $this->getTableLocator()->get('UserSites');
        $this->MachineBoxes = $this->getTableLocator()->get('MachineBoxes');
        $this->MachineContents = $this->getTableLocator()->get('MachineContents');




        $this->modelName = 'Infos';
        $this->set('ModelName', $this->modelName);

        $this->uid = $this->Session->read('uid');


    }
    
    public function beforeFilter(Event $event) {
        // $this->viewBuilder()->theme('Admin');
        $this->viewBuilder()->setLayout("simple");
        $this->getEventManager()->off($this->Csrf);

    }
    public function index($id) {

        // コンテンツ
        $content = $this->Contents->find()->where(['Contents.id' => $id])
                                    ->contain(['ContentMaterials' => function($q) {
                                        return $q->contain(['Materials'])->order(['ContentMaterials.position' => 'ASC']);
                                    }])
                                    ->first();

        $query = $this->_getQuery();

        if (empty($content) || empty($content->content_materials)) {
            throw new NotFoundException('ページが見つかりません');
        }

        // アイテム
        $items = [];
        $scene_list = [];
        $materials = [];
        $material_youtube = [];
        $material_mp4 = [];
        $item_count = 0;
        foreach ($content->content_materials as $material) {
            $this->setContents($material, $material->material, $items, $scene_list, $materials, $material_youtube, $material_mp4, $item_count);
        }

        $this->set(compact('content', 'query', 'items', 'scene_list', 'materials', 'material_youtube', 'material_mp4'));


        
    }

    public function machine($id) {

        // コンテンツ
        $content = $this->MachineContents->find()->where(['MachineContents.id' => $id])
                                    ->contain(['MachineMaterials' => function($q) {
                                        return $q->order(['MachineMaterials.position' => 'ASC']);
                                    }])
                                    ->first();

        $query = $this->_getQuery();


        // アイテム
        $items = [];
        $scene_list = [];
        $materials = [];
        $material_youtube = [];
        $item_count = 0;
        foreach ($content->machine_materials as $material) {
            $this->setContents($material, $material, $items, $scene_list, $materials, $material_youtube, $material_mp4, $item_count);
        }

        $this->set(compact('content', 'query', 'items', 'scene_list', 'materials', 'material_youtube', 'material_mp4'));

        $this->render('index');
        
    }

    private function setContents($material, $detail, &$items, &$scene_list, &$materials, &$material_youtube, &$material_mp4, &$item_count) {
        $item_count++;

        $item = [];
        $item['time'] = intval($material->view_second) * 1000;
        if ($detail->type == Material::TYPE_MOVIE) {
            $item['action'] = 'play_video_' . $item_count;
        } elseif ($detail->type == Material::TYPE_MOVIE_MP4) {
            $item['action'] = 'play_mp4_' . $item_count;
        } else {
            $item['action'] = 'next';
        }

        $items[strval($item_count)] = $item;
        $scene_list[] = intval($item_count);

        // 素材
        $data = [];
        $data['class'] = 'box type_' . $item_count;
        if ($detail->type == Material::TYPE_IMAGE) {
            $data['content'] = '<img src="' . $detail->attaches['image']['0'] . '" alt="">';
        } elseif ($detail->type == Material::TYPE_URL) {
            $data['content'] = '<iframe src="' . $detail->url . '" width="1920" height="1080"></iframe>';
        } elseif ($detail->type == Material::TYPE_MOVIE) {
            $data['content'] = '<div id="player_' . $item_count . '"></div>';
            $material_youtube['no' . $item_count] = [
                'no' => $item_count,
                'obj' => null,
                'error_flg' => 0,
                'code' => $detail->movie_tag
            ];
        } elseif ($detail->type == Material::TYPE_MOVIE_MP4) {
            $data['content'] = '<video id="mp4_' . $item_count . '"';
            $data['content'] .= ' muted';
            $data['content'] .= '>';
            $data['content'] .= '<source src="' . $detail->attaches['file']['src'] . '">';
            $data['content'] .= '</video>';
            $material_mp4['no' . $item_count] = [
                'no' => $item_count,
                'obj' => null,
                'error_flg' => 0,
            ];
        }
        
        $materials[] = $data;
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
