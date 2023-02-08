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
namespace App\Controller;

use Cake\Controller\Controller;
use Cake\Event\Event;
use App\Model\Entity\Info;
use App\Model\Entity\User;

/**
 * Application Controller
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @link https://book.cakephp.org/3.0/en/controllers.html#the-app-controller
 */
class AppController extends Controller {
    public $Session;
    public $error_messages;
    /**
     * Initialization hook method.
     *
     * Use this method to add common initialization code like loading components.
     *
     * e.g. `$this->loadComponent('Security');`

     * @return void
     */
    public function initialize() {
        parent::initialize();

        $this->loadComponent('RequestHandler', [
            'enableBeforeRedirect' => false,
        ]);
        $this->loadComponent('Flash');
        $this->loadComponent('Paginator');
        $this->loadComponent('Csrf');

        $this->Session = $this->request->getSession();

        $this->viewBuilder()->setLayout(false);

        /*
         * Enable the following components for recommended CakePHP security settings.
         * see https://book.cakephp.org/3.0/en/controllers/components/security.html
         */
        //$this->loadComponent('Security');
        //$this->loadComponent('Csrf');
    }

    /**
     *
     * API返却
     *
     */
    public function setApi($datas, $code = 200) {
        $result = array_merge(
            [
                'code' => $code
            ],
            $datas
        );

        $this->set($result);
        $this->set('_serialize', array_keys($result));
    }

    /**
     *
     * ZIP出力
     *
     * @param datas = 下述
     * @param zipname = String
     *
     */
    public function output_zip($datas, $zipname) {
        // $datas = [
        //     [
        //         'name' => '/〇〇/filename',
        //         'data' => [
        //             'path' => '/〇〇/filename.pdf', // 基本これだけでいい
        //             'type' => 'json', // 使ってない
        //             'content' => 'テキストです', // 指定するとテキストファイルになる
        //         ]
        //     ]
        // ];
        header('Content-Type: text/html; charset=UTF-8');

        $zip = new \ZipArchive();

        //$zipname = mb_convert_encoding( $zipname, 'SJIS-WIN', 'UTF-8' );
        $tmpZipPath = '/tmp/' . $zipname . '.zip';
        if (file_exists($tmpZipPath)) {
            unlink($tmpZipPath);
        }

        if ($zip->open($tmpZipPath, \ZipArchive::CREATE) === false) {
            throw new IllegalStateException("failed to create zip file. ${tmpZipPath}");
        }

        foreach ($datas as $_ => $data) {
            $filename = $data['name'] ?? '';
            $filedata = $data['data'] ?? [];

            $zip_filepath = $zipname . '/' . $filename;
            $zip_filepath = mb_convert_encoding($zip_filepath, 'SJIS-WIN', 'UTF-8');

            // テキスト
            if ($text = $filedata['content'] ?? '') {
                $zip->addFromString($zip_filepath, $text);
            }
            // ファイル指定
            if ($file = $filedata['path'] ?? '') {
                $zip->addFile($file, $zip_filepath);
            }
        }

        if ($zip->close() === false) {
            throw new IllegalStateException("failed to close zip file. ${tmpZipPath}");
        }

        if (file_exists($tmpZipPath)) {
            $this->response->type('application/zip');
            $this->response->file($tmpZipPath, array('download' => true));
            $this->response->download($zipname . '.zip');
            $this->response->header('Pragma', 'public');
            $this->response->header('Expires', '0');
            $this->response->header('Cache-Control', 'must-revalidate, post-check=0, pre-check=0');
            $this->response->header('Content-Transfer-Encoding', 'binary');
            $this->response->header('Content-Type', 'application/octet-streams');
            $this->response->header('Content-Disposition', 'attachment; filename=' . $zipname . '.zip');

            return $this->response;
        } else {
            return false;
        }
    }

    /**
     * Before render callback.
     *
     * @param \Cake\Event\Event $event The beforeRender event.
     * @return \Cake\Http\Response|null|void
     */
    public function beforeRender(Event $event) {
        // Note: These defaults are just to get started quickly with development
        // and should not be used in production. You should instead set "_serialize"
        // in each action as required.
        if (!array_key_exists('_serialize', $this->viewVars) &&
            in_array($this->response->getType(), ['application/json', 'application/xml'])
        ) {
            $this->set('_serialize', true);
        }

        $this->set('error_messages', $this->error_messages);
    }

    public function beforeFilter(Event $event) {
        if ($this->request->getParam('prefix') === 'admin') {
            // $this->viewBuilder()->theme('Admin');
            $this->viewBuilder()->setLayout('admin');
        } else {
            //Theme 設定
            $this->viewBuilder()->setLayout('simple');
            // $this->theme = 'Pc';

            // 準備
            $this->_prepare();
        }
    }

    protected function _lists($cond = array(), $options = array()) {
        $primary_key = $this->{$this->modelName}->getPrimaryKey();

        $this->paginate = array_merge(
            array('order' => $this->modelName . '.' . $primary_key . ' DESC',
                'limit' => 10,
                'contain' => [],
                'paramType' => 'querystring',
                'url' => [
                    'sort' => null,
                    'direction' => null
                ]
            ),
            $options
        );

        try {
            if ($this->paginate['limit'] === null) {
                unset($options['limit'],
                    $options['paramType']);
                if ($cond) {
                    $options['conditions'] = $cond;
                }
                // $datas = $this->{$this->modelName}->find('all', $options);
                $data_query = $this->{$this->modelName}->find()->where($cond)->order($options['order'])->all();
            } else {
                $data_query = $this->paginate($this->{$this->modelName}->find()->where($cond));
            }
            $datas = $data_query->toArray();
            $count['total'] = $data_query->count();
        } catch (NotFoundException $e) {
            if (!empty($this->request->query['page'])
                && 1 < $this->request->query['page']) {
                $this->redirect(array('action' => $this->request->action));
            }
        }
        $numrows = $this->{$this->modelName}->find()->where($cond)->count();

        $this->set(compact('datas', 'data_query', 'numrows'));
    }

    protected function _setView($lists) {
        $this->set(array_keys($lists), $lists);
    }

    private function _prepare() {
    }
    /**
     * Actionの後、実行される
     * @return [type] [description]
     */

    public function isLogin() {
        $id = $this->Session->read('uid');
        return $id;
    }
    public function isUserLogin() {
        $userid = $this->Session->read('userid');
        if ($this->Session->read('user_role') >= User::ROLE_SHOP) {
            $userid = 0;
        }
        return $userid;
    }
    public function isShopLogin() {
        $userid = $this->Session->read('userid');
        return $userid;
    }
    public function isCustomerLogin() {
        $id = $this->Session->read('customer_id');
        return $id;
    }
    public function getUserId() {
        return $this->Session->read('userid');
    }

    public function checkLogin() {
        if (!$this->isLogin()) {
            return $this->redirectWithException('/admin/');
        }
    }
    public function checkUserLogin() {
        if (!$this->isUserLogin()) {
            return $this->redirectWithException('/user/');
            // return $this->redirect('/user/');
        }
    }
    public function checkShopLogin() {
        if (!$this->isShopLogin()) {
            return $this->redirectWithException('/shop_user/');
            // return $this->redirect('/user/');
        }
    }
    public function checkCustomerLogin() {
        if (!$this->isCustomerLogin()) {
            return $this->redirectWithException('/');
        }
    }

    public function redirectWithException($url, $status = 302) {
        throw new \Cake\Routing\Exception\RedirectException(\Cake\Routing\Router::url($url, true), $status);
    }
    public function startupProcess() {
        try {
            return parent::startupProcess();
        } catch (\Cake\Routing\Exception\RedirectException $e) {
            return $this->redirect($e->getMessage(), $e->getCode());
        }
    }

    public function invokeAction() {
        try {
            return parent::invokeAction();
        } catch (\Cake\Routing\Exception\RedirectException $e) {
            return $this->redirect($e->getMessage(), $e->getCode());
        }
    }

    public function shutdownProcess() {
        try {
            return parent::shutdownProcess();
        } catch (\Cake\Routing\Exception\RedirectException $e) {
            return $this->redirect($e->getMessage(), $e->getCode());
        }
    }

        /**
     * ハイアラーキゼーションと読む！（階層化という意味だ！）
     * １次元のentityデータを階層化した状態の構造にする
     */
    public function toHierarchization($id, $entity, $options = []) {
        // $options = array_merge([
        //     'section_block_ids' => [10]
        // ], $options);
        $data = $this->request->data;
        $content_count = 0;
        $contents = [
            'contents' => []
        ];

        $contents_table = $this->{$this->modelName}->useHierarchization['contents_table'];
        $contents_id_name = $this->{$this->modelName}->useHierarchization['contents_id_name'];

        $sequence_table = $this->{$this->modelName}->useHierarchization['sequence_table'];
        $sequence_id_name = $this->{$this->modelName}->useHierarchization['sequence_id_name'];
        if ($id && $entity->has($contents_table)) {
            $content_count = count($entity->{$contents_table});
            $block_count = 0;
            foreach ($entity->{$contents_table} as $k => $val) {
                $v = $val->toArray();

                // 枠ブロックの中にあるブロック以外　（枠ブロックも対象）
                if (!$v[$sequence_id_name] || ($v[$sequence_id_name] > 0 && in_array($v['block_type'], $options['section_block_ids']))) {
                    $contents['contents'][$v['id']] = $v;
                    $contents['contents'][$v['id']]['_block_no'] = $block_count;
                } else {
                    // 枠ブロックの中身
                    if (!array_key_exists($sequence_table, $v)) {
                        continue;
                    }
                    $info_content_id = $v[$sequence_table][$contents_id_name];
                    if (!array_key_exists($info_content_id, $contents['contents'])) {
                        continue;
                    }
                    if (!array_key_exists('sub_contents', $contents['contents'][$info_content_id])) {
                        $contents['contents'][$info_content_id]['sub_contents'] = null;
                    }
                    $contents['contents'][$info_content_id]['sub_contents'][$v['id']] = $v;
                    $contents['contents'][$info_content_id]['sub_contents'][$v['id']]['_block_no'] = $block_count;
                }
                $block_count++;
            }
        } else {
            if (array_key_exists($contents_table, $data)) {
                $contents['contents'] = $data[$contents_table];
                $content_count = count($data[$contents_table]);
            }
        }
        return [
            'contents' => $contents,
            'content_count' => $content_count
        ];
    }

    /**
     * 正常時のレスポンス
     */
    protected function rest_success($datas) {
        $data = array(
            'result' => array('code' => 0),
            'data' => $datas
        );

        $this->set(compact('data'));
        $this->set('_serialize', 'data');
    }
    /**
     * エラーレスポンス
     */
    protected function rest_error($code = '', $message = '') {
        $http_status = 200;

        $state_list = array(
            '200' => 'empty',
            '400' => 'Bad Request', // タイプミス等、リクエストにエラーがあります。
            '401' => 'Unauthorixed', // 認証に失敗しました。（パスワードを適当に入れてみた時などに発生）
            // '402' => '', // 使ってない
            '403' => 'Forbidden', // あなたにはアクセス権がありません。
            '404' => 'Not Found', // 該当アドレスのページはありません、またはそのサーバーが落ちている。
            '500' => 'Internal Server Error', // CGIスクリプトなどでエラーが出た。
            '501' => 'Not Implemented', // リクエストを実行するための必要な機能をサポートしていない。
            '509' => 'Other', // オリジナルコード　例外処理
        );

        $code2messages = array(
            '1000' => 'パラメーターエラー',
            '1001' => 'パラメーターエラー',
            '1002' => 'パラメーターエラー',
            '2000' => '取得データがありませんでした',
            '2001' => '取得データがありませんでした',
            '9000' => '認証に失敗しました',
            '9001' => '',
        );

        if (!array_key_exists($http_status, $state_list)) {
            $http_status = '509';
        }

        if ($message == '') {
            if (array_key_exists($code, $code2messages)) {
                $message = $code2messages[$code];
            } elseif (array_key_exists($http_status, $state_list)) {
                $message = $state_list[$http_status];
            }
        }
        if ($code == '') {
            $code = $http_status;
        }
        $data['result'] = array(
            'code' => intval($code),
            'message' => $message
        );

        // セットヘッダー
        // $this->header("HTTP/1.1 " . $http_status . ' ' . $state_list[$http_status], $http_status);
        // $this->response->statusCode($http_status);
        // $this->header("Content-Type: application/json;");

        $this->set(compact('data'));
        $this->set('_serialize', 'data');

        return;
    }

    public function getCategoryEnabled() {
        return CATEGORY_FUNCTION_ENABLED;
    }
    public function getCategorySortEnabled() {
        return CATEGORY_SORT;
    }

    public function isCategoryEnabled($page_config) {
        if (!$this->getCategoryEnabled()) {
            return false;
        }

        if (empty($page_config)) {
            return false;
        }

        if ($page_config->is_category == 'Y') {
            return true;
        }

        return false;
    }

    public function isCategorySort($page_config_id) {
        if (!CATEGORY_SORT) {
            return false;
        }

        $page_config = $this->PageConfigs->find()->where(['PageConfigs.id' => $page_config_id])->first();
        if (empty($page_config)) {
            return false;
        }

        if ($page_config->is_category_sort == 'Y') {
            return true;
        }

        return false;
    }

    public function isViewSort($page_config, $category_id = 0) {
        if ($this->getCategoryEnabled() && $page_config->is_category === 'Y'
             && ($this->isCategorySort($page_config->id)) || (!$this->isCategorySort($page_config->id) && !$category_id)) {
            return true;
        }

        return false;
    }

    public function isRoleDocument($document_id) {
        $customer_id = $this->isCustomerLogin();
        $customer = $this->Customers->find()->where(['Customers.id' => $customer_id])->first();

        $document = $this->Documents->find()
                                    ->where(['Documents.id' => $document_id, 'Folders.department_id' => $customer->department_id])
                                    ->contain(['Folders'])
                                    ->first();
        if (!empty($document)) {
            return true;
        }

        return false;
    }

    public function logging($id, $model, $url, $action = '') {
        $session_id = $this->Session->id();
        $customer_id = $this->isCustomerLogin();
        $department_id = 0;

        $customer = $this->Customers->find()->where(['Customers.id' => $customer_id])->first();
        if (!empty($customer)) {
            $department_id = $customer->department_id;
        }

        $now = new \DateTime();

        // $cond = [
        //     'session_id' => md5($session_id),
        //     'created >=' => $now->format('Y-m-d 00:00:00'),
        //     'item_id' => $id
        // ];
        // $entity = $this->CustomerLogs->find()
        //                         ->where($cond)
        //                         ->first();
        // if (!empty($entity)) {
        //     return;
        // }

        if ($model == 'folder') {
            $action = 'view';
        } elseif ($model == 'document') {
            $action = 'download';
        } else {
            if (empty($action)) {
                $action = 'view';
            }
        }

        $save = [
            'session_id' => ($session_id ?: ''),
            'model_name' => $model,
            'model_id' => $id,
            'customer_id' => ($customer_id ?: 0),
            'department_id' => ($department_id ?: 0),
            'ip' => $this->request->clientIp(),
            'log_date' => $now->format('Y-m-d'),
            'action' => $action,
            'url' => $url
        ];

        $entity = $this->CustomerLogs->newEntity($save);
        $this->CustomerLogs->save($entity);

        return;
    }

    public function getSiteId() {
        return 1;
        // return $this->Session->read('current_site_id');
    }
}
