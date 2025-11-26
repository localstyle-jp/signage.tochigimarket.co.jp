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
use App\Model\Entity\User;

/**
 * Static content controller
 *
 * This controller will render views from Template/Pages/
 *
 * @link https://book.cakephp.org/3.0/en/controllers/pages-controller.html
 */
class ViewsController extends AppController {
    private $list = [];

    public function initialize() {
        parent::initialize();

        $this->SiteConfigs = $this->getTableLocator()->get('SiteConfigs');
        $this->Contents = $this->getTableLocator()->get('Contents');
        $this->UserSites = $this->getTableLocator()->get('UserSites');
        $this->MachineBoxes = $this->getTableLocator()->get('MachineBoxes');
        $this->MachineContents = $this->getTableLocator()->get('MachineContents');
        $this->MachineBoxesUsers = $this->getTableLocator()->get('MachineBoxesUsers');

        // $this->modelName = 'Infos';
        // $this->set('ModelName', $this->modelName);
    }

    public function beforeFilter(Event $event) {
        // $this->viewBuilder()->theme('Admin');
        $this->viewBuilder()->setLayout('simple');

        $this->getEventManager()->off($this->Csrf);
    }

    /**
     *
     * プログレス状況を返す
     *
     */
    public function progressBuild() {
        $machine_box_id = $this->request->getQuery('id');

        // 端末の表示権限チェック
        if (!$this->checkMachineSupported($machine_box_id)) {
            return $this->setApi(['message' => '権限がありません'], 400);
        }

        $build_progress = $this->MachineBoxes->getProgress($machine_box_id);
        return $this->setApi(['data' => ['progress' => $build_progress]]);
    }

    /**
     *
     * ビルドデータをダウンロードする
     *
     */
    public function downloadBuild() {
        $machine_box_id = $this->request->getQuery('id');

        // デバッグログ
        error_log("[DEBUG] downloadBuild called with id: {$machine_box_id}");

        // 端末の表示権限チェック
        if (!$this->checkMachineSupported($machine_box_id)) {
            error_log("[DEBUG] Permission denied for id: {$machine_box_id}");
            return $this->setApi(['message' => '権限がありません'], 400);
        }

        // 生成経過
        $build_progress = $this->MachineBoxes->getProgress($machine_box_id);
        error_log("[DEBUG] Build progress: " . var_export($build_progress, true));
        
        if ($build_progress === false) {
            error_log("[DEBUG] Machine box not found: {$machine_box_id}");
            return $this->setApi(['message' => '存在しません'], 400);
        }
        if ($build_progress === 100) {
            $dest = $this->MachineBoxes->getUploadZipPath($machine_box_id);
            
            error_log("[DEBUG] Checking zip file: {$dest}");
            error_log("[DEBUG] File exists: " . (file_exists($dest) ? 'YES' : 'NO'));
            error_log("[DEBUG] File size: " . (file_exists($dest) ? filesize($dest) : 'N/A'));
            
            // ZIPファイルの存在確認
            if (!file_exists($dest)) {
                error_log("[ERROR] Zip file not found: {$dest}");
                return $this->setApi(['message' => 'ファイルが見つかりません'], 404);
            }
            
            // 直接ZIPファイルURLにリダイレクト（タイムアウト回避）
            $fileUrl = "/upload/MachineBoxes/{$machine_box_id}.zip";
            error_log("[DEBUG] Redirecting to: {$fileUrl}");
            return $this->redirect($fileUrl);
        } else {
            error_log("[DEBUG] Build not complete: {$build_progress}%");
            return $this->setApi(['message' => '生成中です'], 400);
        }
    }

    /**
     *
     * ビルドデータをZIPで返す
     *
     */
    public function build() {
        $machine_box_id = $this->request->getQuery('id');

        // 端末の表示権限チェック
        if (!$this->checkMachineSupported($machine_box_id)) {
            return $this->setApi(['message' => '権限がありません'], 400);
        }

        return $this->MachineBoxes->buildZip($machine_box_id);
    }

    // 端末の表示権限チェック
    public function checkMachineSupported($machine_box_id) {
        $user_id = $this->getUserId();  // ユーザーID
        if (!$user_id) {
            return false;
        }
        $isAdmin = $this->Session->read('user_role') <= User::ROLE_ADMIN;
        $isSupported = $this->MachineBoxesUsers->isSupported($user_id, $machine_box_id);
        if (!$isAdmin && !$isSupported) {
            return false;
        }
        return true;
    }

    /**
     *
     *
     *
     */
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
                                    ->contain(['MachineMaterials' => function ($q) {
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
            'is_vertical' => $machine_box->is_vertical,
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
        $machine_id = (empty($this->request->getData('id')) ? 0 : $this->request->getData('id'));
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
            'no' => intval($item_count),
            'type' => Material::$type_list_api[$material->type],
            'source' => '',
            'movie_tag' => '',
            'time_sec' => $material->view_second,
            'caption' => ''
        ];

        if ($machine->caption_flg == 'content') {
            $data['caption'] = $material->rolling_caption;
        }

        if ($material->type == Material::TYPE_MOVIE_MP4) {
            $data['source'] = Router::url(DS . UPLOAD_MOVIE_BASE_URL . DS . $material->url, true);
        } elseif ($material->type == Material::TYPE_IMAGE) {
            $data['source'] = Router::url($material->attaches['image']['0'], true);
            $data['sound'] = '';
            if ($material->sound) {
                $data['sound'] = Router::url("/upload/Materials/files/{$material->sound}", true);
            }
        } elseif ($material->type == Material::TYPE_URL) {
            $data['source'] = $material->url;
        }

        return $data;
    }
}
