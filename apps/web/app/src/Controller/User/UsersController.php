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
namespace App\Controller\User;

use Cake\Core\Configure;
use Cake\Network\Exception\ForbiddenException;
use Cake\Network\Exception\NotFoundException;
use Cake\View\Exception\MissingTemplateException;
use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use Cake\Auth\DefaultPasswordHasher;
use App\Model\Entity\User;
use App\Lib\CsvUtil;

/**
 * Static content controller
 *
 * This controller will render views from Template/Pages/
 *
 * @link https://book.cakephp.org/3.0/en/controllers/pages-controller.html
 */
class UsersController extends AppController {
    public function initialize() {
        parent::initialize();
        $this->modelName = 'Users';

        $this->Companies = $this->getTableLocator()->get('Companies');
    }

    public function beforeFilter(Event $event) {
        // $this->viewBuilder()->theme('Admin');
        $this->viewBuilder()->setLayout('user');

        $this->setCommon();
        $this->getEventManager()->off($this->Csrf);
    }

    public function index() {
        $this->checkLogin();

        $this->setList();

        $query = $this->_getQuery();
        $this->_setView($query);

        $this->set(compact('query'));

        $cond = $this->_getConditions($query);
        $contain = [];

        return parent::_lists($cond, array('order' => array($this->modelName . '.position' => 'ASC'),
            'limit' => 20,
            'contain' => $contain
        ));
    }

    public function edit($id = 0) {
        $this->checkLogin();

        $this->setList();

        $validate = null;

        if ($this->request->is(['post', 'put'])) {
            if ($id) {
                if ($this->request->getData('_password')) {
                    $this->request->data['password'] = $this->request->getData('_password');
                    $validate = 'modifyIsPass';
                } else {
                    $validate = 'modify';
                }
            } else {
                $validate = 'new';
                $this->request->data['password'] = $this->request->getData('_password');
            }
        }

        return parent::_edit($id, ['validate' => $validate]);
    }

    private function _getQuery() {
        $query = [];

        $query['sch_company_id'] = $this->request->getQuery('sch_company_id');
        $query['sch_role'] = $this->request->getQuery('sch_role');
        $query['sch_status'] = $this->request->getQuery('sch_status');

        return $query;
    }

    private function _getConditions($query) {
        $cond = [];

        if ($query['sch_company_id']) {
            $cond['Users.company_id'] = $query['sch_company_id'];
        }

        if ($query['sch_role']) {
            $cond['Users.role'] = $query['sch_role'];
        }

        if ($query['sch_status']) {
            $cond['Users.status'] = $query['sch_status'];
        }

        return $cond;
    }

    public function setList() {
        $list = array();

        $list['role_list'] = User::$role_list;
        $list['status_list'] = User::$status_list;

        $list['company_list'] = $this->Companies->find('list', ['keyField' => 'id', 'valueField' => 'name'])->order(['Companies.position' => 'ASC'])->all()->toArray();

        if (!empty($list)) {
            $this->set(array_keys($list), $list);
        }

        $this->list = $list;
        return $list;
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

    public function import() {
        $this->checkLogin();

        $gamen_mode = 'input';
        $res['info'] = array(
            'errors' => array(),
            'validate_errors' => array(),
            'valid' => false,
        );

        if ($this->request->is(array('post', 'put'))) {
            $gamen_mode = 'import';

            // お知らせデータ
            if (!empty($this->request->data['info_file']['tmp_name']) && $this->request->data['info_file']['error'] == UPLOAD_ERR_OK) {
                $csv = $this->get_csv_info($this->request->data['info_file']['tmp_name']);
                if ($csv) {
                    $res['info']['valid'] = true;
                    $res['info']['errors'] = $csv->getErrors();
                    $res['info']['validate_errors'] = $csv->getErrorsText();
                }
            }
        }

        $this->set(compact('res', 'gamen_mode'));
    }

    public function get_csv_info($filepath) {
        $csv = new CsvUtil($filepath);

        // CSVファイルチェック　インポートに必要なCSVのヘッダー名を指定する
        if (!$csv->checkHeaderColumn(array('ID', '氏名', '所属', '権限', '状態', 'ユーザーID', 'パスワード'))) {
            $this->Flash->set('ユーザーデータのCSVファイルが違います');
            return false;
        }

        if ($csv->exists()) {
            foreach ($csv->getCsv() as $line => $data) {
                $csv->setActive($line);
                $isValid = true;
                $id = $csv->getData('ID', 'numeric');

                $data = [];
                $data['id'] = null;

                if ($id) {
                    // 既存データチェック
                    $entity = $this->{$this->modelName}->find()->where([$this->modelName . '.id' => $id])->first();
                    if (empty($entity)) {
                        $csv->addError('ID:[' . $id . ']がありませんでした');
                        continue;
                    }
                    $data['id'] = $entity->id;
                    $data['password'] = $csv->getData('パスワード', 'string');
                } else {
                    $data['password'] = $csv->getData('パスワード', 'string');
                    $data['_password'] = $csv->getData('パスワード', 'string');
                }
                $data['company_id'] = $csv->getData('所属', 'numeric');
                $data['name'] = $csv->getData('氏名', 'string');
                $data['username'] = $csv->getData('ユーザーID', 'string');
                $data['role'] = $csv->getData('権限', 'string');
                if ($csv->getData('状態', 'numeric') == 1) {
                    $data['status'] = 'publish';
                } else {
                    $data['status'] = 'draft';
                }

                // 所属チェック
                $company = $this->Companies->find()->where(['Companies.id' => $data['company_id']])->first();
                if (empty($company)) {
                    $csv->addError("所属ID:[{$data['company_id']}]がありませんでした");
                    continue;
                }

                // 権限チェック
                if (!array_key_exists($data['role'], User::$role_key_values)) {
                    $csv->addError("権限:[{$data['role']}]がありませんでした");
                    continue;
                } else {
                    $data['role'] = User::$role_key_values[$data['role']];
                }

                // 状態チェック
                if (!in_array($data['status'], ['draft', 'publish'])) {
                    $csv->addError("状態:[{$data['status']}]が正しくありません");
                    continue;
                }

                if ($id) {
                    $user = $this->Users->patchEntity($entity, $data, ['validate' => 'csvImport']);
                } else {
                    $user = $this->Users->newEntity($data, ['validate' => 'csvImport']);
                }

                if ($user->getErrors()) {
                    $csv->addError('登録できませんでした');
                    foreach ($user->getErrors() as $column => $error) {
                        $csv->addErrorText($column, $error);
                    }
                } else {
                    $this->Users->save($user);
                }
            }
        }

        return $csv;
    }
}
