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
class ContentController extends AppController {
    private $list = [];

    public function initialize() {
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
        $this->viewBuilder()->setLayout('simple');
        $this->getEventManager()->off($this->Csrf);
    }
    public function index($id) {
        // コンテンツ
        $content = $this->Contents->find()->where(['Contents.id' => $id])
                                    ->contain(['ContentMaterials' => function ($q) {
                                        return $q->contain(['Materials'])->order(['ContentMaterials.position' => 'ASC']);
                                    }])
                                    ->first();

        $query = $this->_getQuery();

        if (empty($content) /*|| empty($content->content_materials)*/) {
            throw new NotFoundException('ページが見つかりません');
        }

        $width = 1920;
        $height = 1080;

        // アイテム
        $items = [];
        $scene_list = [];
        $scene_box_list = [];
        $materials = [];
        $material_youtube = [];
        $material_mp4 = [];
        $material_webm = [];
        $material_webpage = [];
        $item_count = 0;
        foreach ($content->content_materials as $material) {
            $this->setContents($material, $material->material, $items, $scene_list, $materials, $material_youtube, $material_mp4, $material_webm, $material_webpage, $item_count, null, $scene_box_list);
        }

        $this->set(compact('content', 'query', 'items', 'scene_list', 'scene_box_list', 'materials', 'material_youtube', 'material_mp4', 'material_webm', 'material_webpage', 'item_count'));
        $this->set(compact('width', 'height'));
    }

    public function machine($id, $machine_id) {
        // // 表示端末
        // $machine_box = $this->MachineBoxes->find()->where(['MachineBoxes.id' => $machine_id])->first();

        // コンテンツ
        // $content = $this->Contents->find()->where(['Contents.id' => $machine_box->content_id])
        //                             ->contain(['ContentMaterials' => function($q) {
        //                                 return $q->contain(['Materials'])->order(['ContentMaterials.position' => 'ASC']);
        //                             }])
        //                             ->first();
        $content = $this->MachineContents->find()->where(['MachineContents.id' => $id])
                                    ->contain(['MachineMaterials' => function ($q) {
                                        return $q->order(['MachineMaterials.position' => 'ASC']);
                                    }, 'MachineBoxes'])
                                    ->first();

        $query = $this->_getQuery();

        // $width = $machine_box->width;
        // $height = $machine_box->height;

        // if ($machine_box->is_vertical == 1) {
        //     $width = $machine_box->height;
        //     $height = $machine_box->width;
        // }

        $width = $content->machine_box->width;
        $height = $content->machine_box->height;

        if ($content->machine_box->is_vertical == 1) {
            $width = $content->machine_box->height;
            $height = $content->machine_box->width;
        }

        // アイテム
        $items = [];
        $scene_list = [];
        $scene_box_list = [];
        $materials = [];
        $material_youtube = [];
        $material_mp4 = [];
        $material_webm = [];
        $material_webpage = [];
        $item_count = 0;
        // foreach ($content->content_materials as $material) {
        //     $this->setContents($material, $material->material, $items, $scene_list, $materials, $material_youtube, $material_mp4, $material_webm, $material_webpage, $item_count, $machine_box, $scene_box_list);
        // }
        foreach ($content->machine_materials as $material) {
            $this->setContents($material, $material, $items, $scene_list, $materials, $material_youtube, $material_mp4, $material_webm, $material_webpage, $item_count, $content->machine_box, $scene_box_list);
        }

        $this->set(compact('content', 'query', 'items', 'scene_list', 'scene_box_list', 'materials', 'material_youtube', 'material_mp4', 'material_webm', 'material_webpage', 'item_count'));
        $this->set(compact('width', 'height'));

        $this->render('index');
    }

    private function setContents($material, $detail, &$items, &$scene_list, &$materials, &$material_youtube, &$material_mp4, &$material_webm, &$material_webpage, &$item_count, $machine = null, &$scene_box_list) {
        $item_count++;

        $item = [];
        $item['time'] = intval($material->view_second) * 1000;
        $item['caption'] = '';
        if (empty($machine) || $machine->caption_flg == 'content') {
            $item['caption'] = $material->rolling_caption;
        }

        if ($detail->type == Material::TYPE_MOVIE) {
            $item['action'] = 'play_video_' . $item_count;
        } elseif ($detail->type == Material::TYPE_MOVIE_MP4) {
            $item['action'] = 'play_mp4_' . $item_count;
        } elseif ($detail->type == Material::TYPE_MOVIE_WEBM) {
            $item['action'] = 'play_webm_' . $item_count;
        } elseif ($detail->type == Material::TYPE_PAGE_MOVIE) {
            $item['action'] = 'play_page_mp4_' . $detail->id;
        } elseif ($detail->type == Material::TYPE_URL) {
            $item['action'] = 'load_webpage_' . $item_count;
        } else {
            $item['action'] = 'next';
        }

        $items[strval($item_count)] = $item;

        $scene_list[strval($item_count)] = intval($item_count);

        if ($detail->type != Material::TYPE_MOVIE_MP4) {
            $scene_box_list[$item_count] = intval($item_count);
        } else {
            $scene_box_list[$item_count] = 'mp4';
        }

        $width = 1920;
        $height = 1080;
        if (!empty($machine)) {
            $width = $machine->width;
            $height = $machine->height;
            if ($machine->is_vertical == 1) {
                $width = $machine->height;
                $height = $machine->width;
            }
        }

        // 素材
        $data = [];
        $data['class'] = 'box type_' . $item_count;
        if ($detail->type == Material::TYPE_IMAGE) {
            $data['content'] = '<img src="' . $detail->attaches['image']['0'] . '"';
            $data['content'] .= ' style="width: ' . $width . 'px; height: ' . $height . 'px; object-fit: cover;"';
            $data['content'] .= ' alt="">';
            $data['type'] = 'image';
        } elseif ($detail->type == Material::TYPE_URL) {
            $data['content'] = '<iframe ';
            $data['content'] .= 'src=""';
            // $date['content'] .= 'src="'.$detail->url.'" sandbox=""';
            $data['content'] .= ' id="webpage_' . $item_count . '" width="' . $width . '" height="' . $height . '"></iframe>';
            $data['type'] = 'webpage';
            $material_webpage['no' . $item_count] = [
                'type' => 'webpage',
                'no' => $item_count,
                'source' => $detail->url,
                'obj' => null,
                'error_flg' => 0,
            ];
        } elseif ($detail->type == Material::TYPE_MOVIE) {
            $data['content'] = '<div id="player_' . $item_count . '"></div>';
            $data['type'] = 'movie';
            $material_youtube['no' . $item_count] = [
                'no' => $item_count,
                'obj' => null,
                'error_flg' => 0,
                'code' => $detail->movie_tag
            ];
        } elseif ($detail->type == Material::TYPE_MOVIE_MP4) {
            // $data['content'] = '<video id="mp4_' . $item_count . '"';
            $data['content'] = '<video id="mp4_video"';
            $data['content'] .= ' muted';
            $data['content'] .= ($item['time'] == 0 ? ' loop' : '');
            $data['content'] .= '>';
            $data['content'] .= '</video>';
            $data['type'] = 'mp4';
            $material_mp4['no' . $item_count] = [
                'type' => 'mp4',
                'no' => $item_count,
                'source' => DS . UPLOAD_MOVIE_BASE_URL . DS . $detail->url,
                'obj' => null,
                'hls' => null,
                'error_flg' => 0,
                'content' => $data['content']
            ];
        } elseif ($detail->type == Material::TYPE_MOVIE_WEBM) {
            $data['content'] = '<video id="webm_' . $item_count . '"';
            // $data['content'] .= ' muted';
            $data['content'] .= '>';
            $data['content'] .= '</video>';
            $data['type'] = 'webm';
            $material_webm['no' . $item_count] = [
                'type' => 'webm',
                'no' => $item_count,
                'source' => $detail->attaches['file']['src'],
                'obj' => null,
                'error_flg' => 0,
            ];
        } elseif ($detail->type == Material::TYPE_PAGE_MOVIE) {
            $data['content'] = '<iframe src="' . $detail->url . '" width="' . $width . '" height="' . $height . '" id="iframe_page_mp4_' . $item_count . '"></iframe>';
            $data['type'] = 'page_mp4';
            $material_mp4['no' . $item_count] = [
                'type' => 'page_mp4',
                'no' => $detail->id,
                'count' => $item_count,
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
            $this->set(array_keys($list), $list);
        }

        $this->list = $list;
        return $list;
    }
}
