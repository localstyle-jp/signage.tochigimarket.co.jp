<?php

namespace App\Controller\V1;

use Cake\Core\Configure;
use Cake\Network\Exception\ForbiddenException;
use Cake\Network\Exception\NotFoundException;
use Cake\View\Exception\MissingTemplateException;
use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use Cake\Routing\Router;
use Cake\Utility\Hash;
use App\Model\Entity\Material;
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
        $this->MachineContents = $this->getTableLocator()->get('MachineContents');

        // $this->modelName = 'Infos';
        // $this->set('ModelName', $this->modelName);

    }
    
    public function beforeFilter(Event $event) {
        // $this->viewBuilder()->theme('Admin');
        $this->viewBuilder()->setLayout("simple");

        $this->getEventManager()->off($this->Csrf);

    }

    public function index() {
        $machine_id = $this->request->getData('id');
        if (empty($machine_id)) {
            $machine_id = 0;
        }

        // 表示端末
        $machine_box = $this->MachineBoxes->find()->where(['MachineBoxes.id' => $machine_id])->first();
        if (empty($machine_box) || empty($machine_box->content_id)) {
            $status_options = ['material_rows' => 0];
            return $this->rest_custom(400, $status_options, []);
        }

        // コンテンツ
        $content = $this->MachineContents->find()->where(['MachineContents.id' => $machine_box->machine_content_id])
                                    ->contain(['MachineMaterials' => function($q) {
                                        return $q->order(['MachineMaterials.position' => 'ASC']);
                                    }])
                                    ->first();
        if (empty($content) || empty($content->machine_materials)) {
            $status_options = ['material_rows' => 0, 'description' => 'Bad Request or No Materials'];
            return $this->rest_custom(400, $status_options, []);
        }

        // 返却情報配列
        $item = [
            // 'content_id' => $machine_box->content_id,
            // 'serial_no' => $content->serial_no,
            'width' => $machine_box->width,
            'height' => $machine_box->height,
            'caption' => '',
            'materials' => [],
        ]; 
        if ($machine_box->is_vertical == 1) {
            $item['width'] = $machine_box->height;
            $item['height'] = $machine_box->width;
        }
        if ($machine_box->caption_flg == 'machine') {
            $item['caption'] = $machine_box->rolling_caption;
        }

        $item_count = 0;
        $materials_output = [];
        foreach ($content->machine_materials as $material) {
            $item_count++;
            $materials_output[$item_count] = $this->setMaterial($item_count, $material, $machine_box);
        }
        $item['materials'] = Hash::combine($materials_output, '{n}.no', '{n}');
        
        $status_options = ['material_rows' => $item_count];

        $this->rest_custom(200, $status_options, $item);
    }

    public function isReload() {
        $machine_id = ( empty($this->request->getData('id')) ? 0 : $this->request->getData('id') );
        // $content_id = ( empty($this->request->getData('content_id')) ? 0 : $this->request->getData('content_id') );
        // $content_serial_no = ( empty($this->request->getData('serial_no')) ? 0 : $this->request->getData('serial_no') );

        $status_options = [];

        // 表示端末
        $machine_box = $this->MachineBoxes->find()->where(['MachineBoxes.id' => $machine_id])->first();
        if (empty($machine_box)) {
            return $this->rest_custom(400, [], []);
        }

        // コンテンツ
        // $content = $this->Contents->find()->where(['Contents.id' => $machine_box->content_id])
        //                             ->contain(['ContentMaterials' => function($q) {
        //                                 return $q->contain(['Materials'])->order(['ContentMaterials.position' => 'ASC']);
        //                             }])
        //                             ->first();
        // if (empty($content) || empty($content->content_materials)) {
        //     $status_options['description'] = 'Bad Request or No Materials';
        //     return $this->rest_custom(400, $status_options, []);
        // }

        // if ($machine_box->status!='publish') {
        //     return $this->rest_custom(509, [], []);
        // }

        // コンテンツリロード判定
        $content_reload_flag = $machine_box->reload_flag_device;
        // $content_reload_flag = ( $content_serial_no != $content->serial_no || $content_id != $content->id);

        // 返却情報配列
        $item = [
            'content_reload_flag' => $content_reload_flag,
            'caption' => '',
            'width' => $machine_box->width,
            'height' => $machine_box->height,
        ];
        if ($machine_box->is_vertical == 1) {
            $item['width'] = $machine_box->height;
            $item['height'] = $machine_box->width;
        }
        if ($machine_box->caption_flg == 'machine') {
            $item['caption'] = $machine_box->rolling_caption;
        }

        $this->rest_custom(200, [], $item);
    }

    public function disableReload() {
        $id = $this->request->getData('id');

        $machine_box = $this->MachineBoxes->find()->where(['MachineBoxes.id' => $id])->first();
        if (empty($machine_box)) {
            return $this->rest_custom(400, [], []);
        }

        $reload_flag_device = $machine_box->reload_flag_device;

        // フラグが経ってたら戻す
        if ($machine_box->reload_flag_device == 1) {
            $entity = $this->MachineBoxes->patchEntity($machine_box, ['reload_flag_device' => 0]);
            $this->MachineBoxes->save($entity);
        }

        $this->rest_custom(200, [], []);
    }

    public function setMaterial($item_count, $material, $machine) {
        $data = [
            'material_id' => $material->id,
            'no' => $item_count,
            'type' => Material::$type_list_api[$material->type],
            'source' => '',
            'movie_tag' => '',
            'time_sec' => $material->view_second,
            'caption' => '',
        ];

        if ($machine->caption_flg == 'content') {
            $data['caption'] = $material->rolling_caption;
        }

        if ($material->type == Material::TYPE_MOVIE_MP4) {
            $data['source'] = Router::url(DS . UPLOAD_MOVIE_BASE_URL . DS . $material->url, true);
        } elseif ($material->type == Material::TYPE_IMAGE) {
            $data['source'] = Router::url($material->attaches['image']['0'], true);
        } elseif ($material->type == Material::TYPE_URL) {
            $data['source'] = $material->url;
        }

        return $data;
    }

}
