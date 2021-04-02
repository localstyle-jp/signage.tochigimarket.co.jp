<?php

namespace App\Controller\User;

use App\Controller\AppController as BaseController;
use Cake\ORM\TableRegistry;
use App\Model\Entity\Info;
use App\Lib\Util;

class AppController extends BaseController
{
    public $helpers = [
        'Paginator' => ['templates' => 'paginator-user']
    ];


    protected function _lists($cond = array(), $options = array()) {
        
        $primary_key = $this->{$this->modelName}->getPrimaryKey();

        $this->paginate = array_merge(array('order' => $this->modelName . '.' . $primary_key . ' DESC',
                                            'limit' => 10,
                                            'contain' => [],
                                            'paramType' => 'querystring',
                                            'url' => [
                                                'sort' => null,
                                                'direction' => null
                                            ]
                                            ),
                            $options);
        if (!array_key_exists('contain',$options)) {
            $options['contain'] = [];
        }

        try {
            if ($this->paginate['limit'] === null) {
                unset($options['limit'],
                        $options['paramType']);
                if ($cond) {
                    $options['conditions'] = $cond;
                }
                // $datas = $this->{$this->modelName}->find('all', $options);
                $query = $this->{$this->modelName}->find()->where($cond)->order($options['order']);
                if ($options['contain']) {
                    $query->contain($options['contain']);
                }
                $data_query = $query->all();

            } else {
                $data_query = $this->paginate($this->{$this->modelName}->find()->where($cond));
                // if ($options['contain']) {
                //     $data_query->contain($options['contain']);
                // }
            }
            $datas = $data_query->toArray();
            $count['total'] = $data_query->count();

        } catch (NotFoundException $e)  {
            if (!empty($this->request->query['page'])
                && 1 < $this->request->query['page']) {
                $this->redirect(array('action' => $this->request->action));
            }
        }
        $q = $this->{$this->modelName}->find()->where($cond);
        if (!empty($options['contain'])) {
            $q->contain($options['contain']);
        }
        $numrows = $q->count();
        
        $this->set(compact('datas', 'data_query', 'numrows'));
    }

    protected function _edit($id = 0, $option = array()) {
        $option = array_merge(array('create' => null,
                                    'callback' => null,
                                    'redirect' => array('action' => 'index'),
                                    'contain' => [],
                                    'success_message' => '保存しました',
                                    'validate' => 'default',
                                    'associated' => [],
                                    'get_callback' => null,
                                ),
                              $option);
        extract($option);

        $primary_key = $this->{$this->modelName}->getPrimaryKey();

        if (empty($contain) && !empty($associated)) {
            $contain = $associated;
        }

        if ($this->request->is(array('post', 'put'))
            && $this->request->getData() //post_max_sizeを越えた場合の対応(空になる)
            )
        {    
            $isValid = true;
            
            $entity_options = [];
            if (!empty($associated)) {
                $entity_options['associated'] = $associated;
            }
            if (!empty($validate)) {
                $entity_options['validate'] = $validate;
            }
            
            if ($id) {
                $q = $this->{$this->modelName}->find()->where([$this->modelName.'.id' => $id]);
                if ($contain) {
                    $q->contain($contain);
                }
                $old = $q->first();
                $entity = $this->{$this->modelName}->patchEntity($old, $this->request->getData(), $entity_options);
            } else {
                $entity = $this->{$this->modelName}->newEntity($this->request->getData(), $entity_options);
            }

            if ($entity->getErrors()) {
                $data = $this->request->getData();
                if (!array_key_exists('id', $data)) {
                    $data['id'] = $id;
                }
                if (property_exists($this->{$this->modelName}, 'useHierarchization') && !empty($this->{$this->modelName}->useHierarchization)) {
                    $vals = $this->{$this->modelName}->useHierarchization;
                    $_model = $vals['sequence_model'];
                    if (array_key_exists($vals['contents_table'], $entity)) {
                        foreach ($entity[$vals['contents_table']] as $k => $v) {
                            if ($v[$vals['sequence_id_name']]) {
                                $seq = $this->{$_model}->find()->where([$_model.'.id' => $v[$vals['sequence_id_name']]])->first();
                                $entity[$vals['contents_table']][$k][$vals['sequence_table']] = $seq;
                            }
                        }
                    }
                }
                // TODO::
                // $this->redirect($this->referer());
                // $request = $this->getRequest()->withParsedBody($this->{$this->modelName}->toFormData($entity));
                $request = $this->getRequest()->withParsedBody($data);
                $this->setRequest($request);
                $this->set('data', $data);
                $isValid = false;
            }
                      
            /**
             * 添付ファイルのバリデーションを行うためここで実行
             */
            // $this->{$this->modelName}->set($this->request->data);
            // if ($saveMany) {
            //     if (!$this->{$this->modelName}->validateMany($this->request->data)) {
            //         $isValid = false;
            //     }
            // } else {
            //     $isValid = $this->{$this->modelName}->validates();
            // }

            if ($isValid) {

                $r = $this->{$this->modelName}->save($entity);
                
                if ($r) {
                    if ($success_message) {
                        $this->Flash->set($success_message);
                    }
                    if ($callback) {
                        $callback_options = $callback($entity->id);
                        if (!empty($callback_options) && array_key_exists('redirect', $callback_options)) {
                            $redirect = $callback_options['redirect'];
                        }
                    }
                    // exit;
                    if ($redirect) {
                        return $this->redirect($redirect);
                    }
                }

            } else {
                $data = $this->request->getData();
                if (!array_key_exists('id', $data)) {
                    $data['id'] = $id;
                }
                $this->set('data', $data);
                $this->Flash->set('正しく入力されていない項目があります');
            }
        } else {

            $query = $this->{$this->modelName}->find()->where([$this->modelName.'.'.$primary_key => $id])->contain($contain);

            
            if (!$query->isEmpty()) {
                $entity = $query->first();
                $request = $this->getRequest()->withParsedBody($this->{$this->modelName}->toFormData($entity));
                $this->setRequest($request);
            } elseif ($create) {
                $request = $this->getRequest()->withParsedBody($create);
                $this->setRequest($request);
                $entity = $this->{$this->modelName}->newEntity($create);
            } else {
                $entity = $this->{$this->modelName}->newEntity();
                $entity->{$this->{$this->modelName}->getPrimaryKey()} = null;
                $request = $this->getRequest()->withParsedBody($this->{$this->modelName}->toFormData($entity));
                $this->setRequest($request);
                if (property_exists($this->{$this->modelName}, 'defaultValues')) {
                    $request = $this->getRequest()->withParsedBody(array_merge($this->request->data, $this->{$this->modelName}->defaultValues));
                    $this->setRequest($request);
                }
            }

            if ($get_callback) {
                $request = $this->getRequest()->withParsedBody($get_callback($this->request->data));
                $this->setRequest($request);
            }

            
            $this->set('data', $this->request->getData());
        }

        if (property_exists($this->{$this->modelName}, 'useHierarchization') && !empty($this->{$this->modelName}->useHierarchization)) {
            $block_waku_list = array_keys(Info::BLOCK_TYPE_WAKU_LIST);
            $contents = $this->toHierarchization($id, $entity, ['section_block_ids' => $block_waku_list]);
            $this->set(array_keys($contents), $contents);
            // pr($contents);exit;
        }

        $this->set('entity', $entity);  
    }

    public function _detail($id, $option = []) {
        $option = array_merge(array(
                                    'callback' => null,
                                    'redirect' => array('action' => 'index'),
                                    'contain' => []
                                ),
                              $option);
        extract($option);

        $primary_key = $this->{$this->modelName}->getPrimaryKey();



        $query = $this->{$this->modelName}->find()->where([$this->modelName.'.'.$primary_key => $id])->contain($contain);

        if (!$query->isEmpty()) {
            $entity = $query->first();
            $request = $this->getRequest()->withParsedBody($this->{$this->modelName}->toFormData($entity));
            $this->setRequest($request);
        } else {
            $entity = $this->{$this->modelName}->newEntity();
            $entity->{$this->{$this->modelName}->getPrimaryKey()} = null;
            $request = $this->getRequest()->withParsedBody($this->{$this->modelName}->toFormData($entity));
            $this->setRequest($request);
            if (property_exists($this->{$this->modelName}, 'defaultValues')) {
                $request = $this->getRequest()->withParsedBody(array_merge($this->request->data, $this->{$this->modelName}->defaultValues));
                $this->setRequest($request);
            }
        }

        
        $this->set('data', $this->request->getData());


        if (property_exists($this->{$this->modelName}, 'useHierarchization') && !empty($this->{$this->modelName}->useHierarchization)) {
            $block_waku_list = array_keys(Info::BLOCK_TYPE_WAKU_LIST);
            $contents = $this->toHierarchization($id, $entity, ['section_block_ids' => $block_waku_list]);
            $this->set(array_keys($contents), $contents);
        }

        $this->set('entity', $entity);
    }

    public function isLogin() {
        $id = $this->Session->read('userid');
        return $id;
    }

    public function checkLogin(){
        return parent::checkUserLogin();
    }

    /**
     * 順番並び替え
     * */
     protected function _position($id, $pos, $options=array()) {
        $options = array_merge(array(
            'redirect' => array('action' => 'index', '#' => 'content-' . $id)
            ), $options);
        extract($options);
        
        $primary_key = $this->{$this->modelName}->getPrimaryKey();
        $query = $this->{$this->modelName}->find()->where([$this->modelName.'.'.$primary_key => $id]);
        
        if (!$query->isEmpty()) {
            // $entity = $this->{$this->modelName}->get($id);
            $this->{$this->modelName}->movePosition($id, $pos);
        }
        if ($redirect) {
            $this->redirect($redirect);
        }

        // $this->OutputHtml->index($this->getUsername());

    }

    /**
     * 掲載中/下書き トグル
     * */
     protected function _enable($id, $options = array()) {
        $options = array_merge(array(
            'redirect' => array('action' => 'index', '#' => 'content-' . $id),
            'column' => 'status',
            'status_true' => 'publish',
            'status_false' => 'draft'
            ), $options);
        extract($options);

        $primary_key = $this->{$this->modelName}->getPrimaryKey();
        $query = $this->{$this->modelName}->find()->where([$this->modelName.'.'.$primary_key => $id]);

        if (!$query->isEmpty()) {
            $entity = $query->first();
            $status = ($entity->get($column) == $status_true)? $status_false: $status_true;
            $this->{$this->modelName}->updateAll(array($column => $status), array($this->{$this->modelName}->getPrimaryKey() => $id));
        }
        if ($redirect) {
            $this->redirect($redirect);
        }

    }

    /**
     * ファイル/記事削除
     *
     * */
     protected function _delete($id, $type, $columns = null, $option = array()) {
        $option = array_merge(array('redirect' => null),
                              $option);
        extract($option);

        $primary_key = $this->{$this->modelName}->getPrimaryKey();
        $query = $this->{$this->modelName}->find()->where([$this->modelName.'.'.$primary_key => $id]);

        if (!$query->isEmpty() && in_array($type, array('image', 'file', 'content'))) {
            $entity = $query->first();
            $data = $entity->toArray();

            if ($type === 'image' && isset($this->{$this->modelName}->attaches['images'][$columns])) {
               if (!empty($data['attaches'][$columns])) {
                    foreach($data['attaches'][$columns] as $_) {
                        $_file = WWW_ROOT . $_;
                        if (is_file($_file)) {
                            @unlink($_file);
                        }
                    }
                }
                $this->{$this->modelName}->updateAll(array($columns => ''),
                                                     array($this->modelName.'.'.$this->{$this->modelName}->getPrimaryKey() => $id));

            } else if ($type === 'file' && isset($this->{$this->modelName}->attaches['files'][$columns])) {
                if (!empty($data['attaches'][$columns][0])) {
                    $_file = WWW_ROOT . $data['attaches'][$columns][0];
                    if (is_file($_file)) {
                        @unlink($_file);
                    }

                    $this->{$this->modelName}->updateAll(array($columns => '',
                                                               $columns.'_name' => '',
                                                               $columns.'_size' => 0,
                                                               ),
                                                         array($this->modelName . '.' . $this->{$this->modelName}->getPrimaryKey() => $id));
                }

            } else if ($type === 'content') {
                $image_index = array_keys($this->{$this->modelName}->attaches['images']);
                $file_index = array_keys($this->{$this->modelName}->attaches['files']);

                foreach($image_index as $idx) {
                    foreach($data['attaches'][$idx] as $_) {
                        $_file = WWW_ROOT . $_;
                        if (is_file($_file)) {
                            @unlink($_file);
                        }
                    }
                }

                foreach($file_index as $idx) {
                    $_file = WWW_ROOT . $data['attaches'][$idx][0];
                    if (is_file($_file)) {
                        @unlink($_file);
                    }
                }

                $this->{$this->modelName}->delete($entity);

                $id = 0;
            }
        }


        if ($redirect) {
            $this->redirect($redirect);
        }

        if ($redirect !== false) {
            if ($id) {
                $this->redirect(array('action' => 'edit', $id));
            } else {
                $this->redirect(array('action' => 'index'));
            }
        }

        return;
    }

    /**
     * 中身は各コントローラに書く
     * @param  [type] $info_id [description]
     * @return [type]          [description]
     */
    protected function _htmlUpdate($info_id) {

    }


    /**
     * ログインユーザーの記事かチェック
     * @param  [type] $info_id [description]
     * @return [type]          [description]
     */
    protected function checkOwner($info_id) {
        $result = false;

        $cond = [
            'UserInfos.id' => $info_id,
            'UserInfos.user_id' => $this->isLogin()
        ];
        $info = $this->UserInfos->find()->where($cond);
        if (!$info->isEmpty()) {
            $result = true;
        }

        return $result;
    }

    protected function getUsername() {
        return $this->Session->read('data.username');
    }
    public function getUserId() {
        return $this->isLogin();
    }

    public function array_asso_chunk($datas, $num) {
        $res = [];
        $max = count($datas);

        $count = 0;
        $i = 0;
        foreach ($datas as $k => $v) {
            $res[$i][$k] = $v;
            $count++;
            if (!($count%$num)) {
                $i++;
            }
        }

        return $res;
    }

    public function setCommon() {


    }

    public function _getUserSite($user_id) {
        $user_sites = $this->UserSites->find()
                                      ->where(['UserSites.user_id' => $user_id])
                                      ->contain(['SiteConfigs'])
                                      ->all();

        $user_site_list = [];
        if (!empty($user_sites)) {
            foreach ($user_sites as $site) {
                $user_site_list[$site->site_config->id] = $site->site_config->site_name;
            }
        }
        if (!$this->Session->read('current_site_id')) {
            foreach ($user_site_list as $site_id => $config) {
                $this->Session->write('current_site_id', $site_id);
                if (!$this->Session->read('current_site_slug')) {
                    foreach ($user_sites as $site) {
                        if ($site->site_config_id == $site_id) {
                            $this->Session->write('current_site_slug', $site->site_config->slug);
                        }
                    }
                }
                break;
            }
        }

        return $user_site_list;
    }

    protected function isUserRole($role_key, $isOnly = false) {
        
        $role = $this->Session->read('user_role');
        
        if (intval($role) === 0) {
            $res = 'develop';
        }
        elseif ($role < 10) {
            $res = 'admin';
        } 
        /** 必要に応じて追加 */
        else {
            $res = 'staff';
        }

        if (!$isOnly) {
            if ($role_key == 'admin') {
                $role_key = array('develop', 'admin');
            } elseif ($role_key == 'staff') {
                $role_key = array('develop', 'admin', 'staff');
            }
        } 

        if (in_array($res, (array)$role_key)) {
            return true;
        } else {
            return false;
        }

    }


    protected function calcDiv($value, $div, $decimal = 0) {
        if (function_exists('bcdiv')) {
            $rate = bcdiv(strval($value), strval($div) ,2);
        } else {
            $rate = $value / $div;
        }

        return $rate;
    }

    protected function calcMul($value1, $value2, $decimal=0) {

        if (empty($value1)) {
            $value1 = 0;
        }
        if (empty($value2)) {
            $value2 = 0;
        }

        // 消費税
        if (function_exists('bcmul')) {
            $result = bcmul(strval($value1), strval($value2) ,6);
        } else {
            $result = (float)$value1 * (float)$value2;
        }
        $result = $this->Round($result, $decimal);

        return $result;
    }

    protected function calcTax($price, $tax_rate, $decimal=0) {

        // 消費税
        if (function_exists('bcmul')) {
            $rate = bcmul(strval($tax_rate), '0.01',2);
            $tax = bcmul(strval($price), strval($rate),2);
        } else {
            $rate = (float)$tax_rate * 0.01;
            $tax = (float)$price * $rate;
        }
        $tax = $this->Round($tax, $decimal);

        return $tax;
    }

    /**
     * 端数処理
     * @param [type] $value [description]
     */
    protected function Round($number, $decimal=0, $type=1) {

        return Util::Round($number, $decimal, $type);

    }
    protected function wareki($date) {
        return Util::wareki($date);
    }

    public function getData() {

        $id = $this->request->getData('id');
        $columns = $this->request->getData('columns');
        $append_columns = $this->request->getData('append_columns');

        $columns = str_replace(' ', '', $columns);
        $cols = explode(",", $columns);

        $data = $this->{$this->modelName}->find()->where([$this->modelName.'.id' => $id])->select($cols)->first();

        if (!empty($append_columns)) {
            $append_columns = str_replace(' ', '', $append_columns);
            $cols = explode(",", $append_columns);
            foreach ($cols as $col) {
                $data[$col] = $data->{$col};
            }
        }

        $this->rest_success($data);
    }

    public function getSiteId() {
        return $this->Session->read('current)site_id');
    }
}
