<?php

namespace App\Controller\User;

use Cake\Core\Configure;
use Cake\Network\Exception\ForbiddenException;
use Cake\Network\Exception\NotFoundException;
use Cake\View\Exception\MissingTemplateException;
use Cake\Event\Event;
use Cake\ORM\TableRegistry;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Reader\Xls;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheet\Reader\Csv;

// use PhpOffice\PhpSpreadsheet\Reader\Xlsx;

use App\Model\Entity\Media;
use App\Model\Entity\ImportConfig;


/**
 * Static content controller
 *
 * This controller will render views from Template/Pages/
 *
 * @link https://book.cakephp.org/3.0/en/controllers/pages-controller.html
 */
class MediaController extends AppController
{
    private $list = [];

    public function initialize()
    {
        $this->ImportConfigs = $this->getTableLocator()->get('ImportConfigs');
        $this->Imports = $this->getTableLocator()->get('Imports');
        $this->ImportDetails = $this->getTableLocator()->get('ImportDetails');
        $this->Artists = $this->getTableLocator()->get('Artists');
        $this->Musics = $this->getTableLocator()->get('Musics');
        $this->Companies = $this->getTableLocator()->get('Companies');
        $this->MediaPlans = $this->getTableLocator()->get('MediaPlans');

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

    private function _getQuery() {
        $query = [];

        return $query;
    }

    private function _getConditions($query) {
        $cond = [];

        return $cond;
    }

    private function _getQueryImport() {
        $query = [];

        $query['media_id'] = $this->request->getQuery('media_id');
        $query['sch_artist'] = $this->request->getQuery('sch_artist');
        $query['sch_music'] = $this->request->getQuery('sch_music');
        $query['sch_fixed'] = $this->request->getQuery('sch_fixed');
        if (is_null($query['sch_fixed'])) {
            $query['sch_fixed'] = 'all';
        }
        $query['sch_plan'] = $this->request->getQuery('sch_plan');

        return $query;
    }
    private function _getConditionsImportDetail($query, $cond=[]) {
        $cnt = count($cond);

        if ($query['sch_artist']) {
            $keywords = explode(' ', $query['sch_artist']);
            foreach ($keywords as $word) {
                $cond[$cnt++] = [
                    'ImportDetails.artist_name collate utf8_unicode_ci like' => "%{$word}%"
                ];
            }
        }

        if (!empty($query['sch_music'])) {
            $keywords = explode(' ', $query['sch_music']);
            foreach ($keywords as $word) {
                $cond[$cnt++] = [
                    'ImportDetails.music_name collate utf8_unicode_ci like' => "%{$word}%"
                ];
            }
        }

        if ($query['sch_fixed'] != 'all') {
            $cond[$cnt++]['ImportDetails.fixed'] = $query['sch_fixed'];
        }

        if (!empty($query['sch_plan'])) {
            $cond[$cnt++]['ImportDetails.media_plan_id'] = $query['sch_plan'];
        }

        return $cond;
    }

    public function index() {
        $this->checkLogin();

        $this->setList();

        $cond = ['Media.parent_id' => 0];

        $this->_lists($cond, ['order' => ['position' =>  'ASC'],
                              'limit' => null]);
    }

    public function edit($id=0) {
        $this->checkLogin();

        $this->setList();

        $create = null;
        $associated = ['ImportConfigs'];
        $get_callback = null;
        $callback = null;
        $redirect = ['action' => 'index'];

        $parent = [];
        $children = [];

        if ($this->request->getQuery('parent_id')) {
            $parent = $this->Media->find()->where(['Media.id' => $this->request->getQuery('parent_id')])->first();
        }

        if ($this->request->is(['post', 'put'])) {

        } else {
            if (!$id) {
                $get_callback = function($data)  use($parent) {
                    foreach ($this->list['import_name_list'] as $key => $val) {
                        $data['import_configs'][] = ['key_name' => $key];
                    }

                    if (!empty($parent)) {
                        $data['name'] = $parent->name;
                        $data['full_name'] = $parent->full_name;
                        $data['_rate'] = $parent->_rate;
                    }

                    return $data;
                };
                if (!empty($parent)) {
                    $create['id'] = null;
                    $create['name'] = $parent->name;
                    $create['full_name'] = $parent->full_name;
                    $create['_rate'] = $parent->_rate;
                }
            }
        }

        $callback = function($id) use($redirect) {
            $media = $this->Media->find()->where(['Media.id' => $id])->first();
            if (!empty($media)) {
                $children = $this->Media->find()->where(['Media.parent_id' => $id])->count();
                if ($children) {
                    $this->Media->updateAll(['status' => $media->status], ['Media.parent_id' => $id]);
                }
            }

            // プランはassociatedに設定せずここで保存する
            $data_plans = $this->request->getData("media_plans");
            $save_plan_ids = [];
            if (!empty($data_plans)) {
                foreach ($data_plans as $plan) {
                    if (!empty($plan['name'])) {
                        if ($plan['id']) {
                            $_plan = $this->MediaPlans->find()->where(['MediaPlans.id' => $plan['id']])->first();
                            $plan_entity = $this->MediaPlans->patchEntity($_plan, ['name' => $plan['name'], 'price' => $plan['price']]);
                        } else {
                            $plan_entity = $this->MediaPlans->newEntity(['name' => $plan['name'], 'price' => $plan['price']]);
                        }
                        $plan_entity->media_id = $id;
                        $plan_entity->col_dl_count = $plan['col_dl_count'];
                        $this->MediaPlans->save($plan_entity);
                        $save_plan_ids[] = $plan_entity->id;
                    }
                }
            }

            $this->MediaPlans->deleteAll(['MediaPlans.media_id' => $id, function($exp) use($save_plan_ids){
                if (!empty($save_plan_ids)) {
                    return $exp->notIn('id', $save_plan_ids);
                } else {
                    return [];
                }
            }]);

            return [
                'redirect' => ['action' => 'edit', $id]
            ];
        };

        $options = [
            'associated' => $associated,
            'create' => $create,
            'get_callback' => $get_callback,
            'callback' => $callback,
            'redirect' => $redirect
        ];

        $Media = new Media();

        if ($id && empty($parent)) {
            $children = $this->Media->find()->where(['Media.parent_id' => $id])->order(['Media.position' => 'ASC'])->all()->toArray();
        }

        elseif (!empty($parent)) {
            $children = $this->Media->find()->where(['Media.parent_id' => $parent->id])->order(['Media.position' => 'ASC'])->all()->toArray();
        }

        // インポートデータの存在チェック
        $is_import_data = false;
        $imports = $this->Imports->find()->where(['Imports.media_id' => $id])->count();
        
        if ($imports) {
            $is_import_data = true;
        }

        // プラン
        $plans = $this->MediaPlans->find()->where(['MediaPlans.media_id' => $id])->all()->toArray();

        $plan_row_count = count($plans);
        $plan_row_count += 3;

        // プラン対象のカラムか
        $plan_target_columns = ['dl_count'];


        $this->set(compact('Media', 'children', 'parent', 'is_import_data', 'plans', 'plan_row_count', 'plan_target_columns'));

        parent::_edit($id, $options);

    }

    public function importList($media_id) {
        $this->checkLogin();

        $this->setList();


        $media = $this->Media->find()->where(['Media.id' => $media_id])->first();

        $relation_id = $media->parent_id;
        if (empty($relation_id)) {
            $relation_id = $media->id;
        }
        $media_list = $this->Media->find('list')->where([
            'OR' => [
                ['Media.parent_id' => $relation_id],
                ['Media.id' => $relation_id]
            ]
            ])->order(['Media.parent_id' => 'ASC', 'Media.position' => 'ASC'])->all();

        $this->set(compact('media', 'media_list'));

        $cond = ['Imports.media_id' => $media_id];

        $this->modelName = 'Imports';
        $this->_lists($cond, ['limit' => 20, 'order' => ['Imports.date' => 'DESC', 'Imports.id' => 'DESC']]);
    }

    public function import() {
        $this->checkLogin();

        $this->setList();

        $query = $this->_getQueryImport();

        if (empty($query['media_id'])) {
            return $this->redirect('/');
        }

        $now = new \DateTime();

        $contain = [
            'ImportConfigs',
            'MediaPlans' => function($q) {
                return $q->order(['MediaPlans.position' => 'ASC']);
            }];
        $media = $this->Media->find()->where(['Media.id' => $query['media_id']])->contain($contain)->first();

        $gamen_mode = 'input';
        $res['info'] = array(
            'errors' => array(),
            'validate_errors' => array(),
            'valid' => false,
            );

        if ($this->request->is(array('post', 'put'))) {
            $gamen_mode = 'import';
            if (!empty($this->request->data['info_file']['tmp_name']) && $this->request->data['info_file']['error'] == UPLOAD_ERR_OK){
                if ($media->import_type == Media::IMPORT_TYPE_CSV) {
                    $import_id = $this->save_csv($media, $this->request->data['info_file'], $this->request->getData('import_configs'));
                } elseif ($media->import_type == Media::IMPORT_TYPE_TSV) {
                    $import_id = $this->save_tsv($media, $this->request->data['info_file'], $this->request->getData('import_configs'));
                } elseif ($media->import_type == Media::IMPORT_TYPE_EXCEL) {
                    $import_id = $this->save_excel($media, $this->request->data['info_file'], $this->request->getData('import_configs'));
                }

                // エラー
                if ($import_id < 0) {
                    $res['info']['valid'] = true;
                    $res['info']['errors'] = $csv->getErrors();
                    $res['info']['validate_errors'] = $csv->getErrorsText();
                } else {
                    return $this->redirect(['action' => 'import-details', $import_id, '?' => $query]);
                }
            }
        }

        $input_configs = $this->ImportConfigs->find()->where(['ImportConfigs.media_id' => $query['media_id'], 'ImportConfigs.is_fixed' => 1])->extract('key_name')->toArray();
        $ImportConfig = new ImportConfig();

        $this->set(compact('res', 'gamen_mode', 'media', 'query', 'input_configs', 'ImportConfig', 'now'));
        

    }

    public function importDetails($import_id) {
        $this->checkLogin();

        $query = $this->_getQueryImport();

        $media = $this->Media->find()->where(['Media.id' => $query['media_id']])->first();
        if (empty($media)) {
            return $this->redirect('/');
        }
        // プラン
        $plan_list = [];
        if ($media->is_plan == 1) {
            $plan_list = $this->MediaPlans->find('list')->where(['MediaPlans.media_id' => $media->id])->order(['MediaPlans.position' => 'ASC'])->all()->toArray();
        }

        $import = $this->Imports->find()
                                ->where(['Imports.id' => $import_id])
                                ->first();

        $ImportConfig = new ImportConfig;

        $cond = ['Imports.id' => $import_id];
        $cond = $this->_getConditionsImportDetail($query, $cond);


        $q = $this->ImportDetails->find()->where($cond)->contain(['Imports'])->order(['ImportDetails.id' => 'ASC']);
        $import_details = $this->paginate($q, ['limit' => 100]);

        // setList
        $fixed_list = [
            [
                'value' => 'all',
                'text' => '未指定',
                'checked' => true
            ],
            [
                'value' => '0',
                'text' => '未確定',
            ],
            [
                'value' => '1',
                'text' => '確定済み'
            ]
        ];

        $this->set(compact('media', 'query', 'import', 'ImportConfig', 'import_details', 'fixed_list', 'plan_list'));
    }

    public function importDetailEdit($import_id, $id) {
        $this->checkLogin();

        $query = $this->_getQueryImport();

        $media = $this->Media->find()->where(['Media.id' => $query['media_id']])->first();
        $import = $this->Imports->find()->where(['Imports.id' => $import_id])->first();

        $this->set(compact('query', 'media', 'import'));

        $this->modelName = 'ImportDetails';

        $options = [
            'contain' => ['Artists', 'Musics']];

        $this->_edit($id, $options);
    }

    public function importRelation($import_id) {
        $query = $this->_getQueryImport();
        set_time_limit(0);

        $media = $this->Media->find()->where(['Media.id' => $query['media_id']])->first();

        $details = $this->ImportDetails->find()->where(['ImportDetails.import_id' => $import_id])->order(['ImportDetails.position' => 'ASC'])->all();

        $configs = $this->ImportConfigs->find()->where(['ImportConfigs.media_id' => $query['media_id']])->order(['ImportConfigs.position' => 'ASC'])->all();

        foreach ($details as $detail) {
            $fixed_count = 0;
            $update = [];
            $music = $this->Musics->getDataFromISRC($detail->isrc);
            foreach ($configs as $config) {
                if (!empty($detail->isrc)) {
                    if (!empty($music)) {
                        $update['artist_id'] = $music->artist_id;
                        $update['music_id'] = $music->id;
                        $update['isrc_registed'] = 1;
                        $fixed_count +=2;
                    } else {
                        if ($config->key_name == ImportConfig::ROW_ARTIST_NAME) {
                            $update[str_replace('_name', '_id', $config->key_name)] = $this->getArtistId($detail->{$config->key_name});
                            $fixed_count += ($update[str_replace('_name', '_id', $config->key_name)] ? 1 : 0);
                        } elseif ($config->key_name == ImportConfig::ROW_MUSIC_TITLE) {
                            $update[str_replace('_name', '_id', $config->key_name)] = $this->getMusicId($detail->{$config->key_name});
                            $fixed_count += ($update[str_replace('_name', '_id', $config->key_name)] ? 1 : 0);
                            if ($update[str_replace('_name', '_id', $config->key_name)] > 0) {
                                $this->Musics->saveISRC($update['music_id'], $detail->isrc);
                                $update['isrc_registed'] = 1;
                            }
                        } else {
                        }

                    }

                } else {
                    if ($config->key_name == ImportConfig::ROW_ARTIST_NAME) {
                        $update[str_replace('_name', '_id', $config->key_name)] = $this->getArtistId($detail->{$config->key_name});
                        $fixed_count += ($update[str_replace('_name', '_id', $config->key_name)] ? 1 : 0);
                    } elseif ($config->key_name == ImportConfig::ROW_MUSIC_TITLE) {
                        $update[str_replace('_name', '_id', $config->key_name)] = $this->getMusicId($detail->{$config->key_name});
                        $fixed_count += ($update[str_replace('_name', '_id', $config->key_name)] ? 1 : 0);
                    
                    } elseif ($config->key_name == ImportConfig::ROW_COMPANY_NAME) {
                        $update[str_replace('_name', '_id', $config->key_name)] = $this->getCompanyId($detail->{$config->key_name});
                        $fixed_count += ($update[str_replace('_name', '_id', $config->key_name)] ? 1 : 0);

                    } elseif ($config->key_name == ImportConfig::ROW_ISRC) {

                    } else {

                    }
                }
                
            }

            $update['fixed'] = 0;
            if ($fixed_count >= ImportConfig::FIXED_COUNT && !empty($detail->dl_count) && !empty($detail->price) && !empty($detail->amount)) {
                $update['fixed'] = 1;
            }
            if (!empty($update)) {
                $entity = $this->ImportDetails->patchEntity($detail, $update);
                $this->ImportDetails->save($entity);
                unset($entity);
            }
        }

        $this->redirect(['action' => 'import-details', $import_id, '?' => $query]);
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

        $media = $this->Media->find()->where(['Media.id' => $id])->first();
        if (!empty($media)) {
            $children = $this->Media->find()->where(['Media.parent_id' => $id])->count();
            if ($children) {
                $this->Media->updateAll(['status' => $media->status], ['Media.parent_id' => $id]);
            }
        }

    }

    public function detailAccept($id, $import_id) {
        $this->checkLogin();

        $query = $this->_getQueryImport();

        $redirect = ['action' => 'import-details', $import_id, '?' => $query];

        $options = [
            'redirect' => $redirect,
            'column' => 'fixed',
            'status_true' => '1',
            'status_false' => '0'
        ];

        $this->modelName = 'ImportDetails';
        parent::_enable($id, $options);
    }

    public function delete($id, $type, $columns = null) {
        $this->checkLogin();


        
        $options = [];

        return parent::_delete($id, $type, $columns, $options);
    }

    public function importDelete($id, $type, $columns = null) {
        $this->checkLogin();

        $this->modelName = 'Imports';

        $import = $this->Imports->find()->where(['Imports.id' => $id])->first();
        if (empty($import)) {
            return $this->redirect('/');
        }


        $redirect = ['action' => 'import-list', $import->media_id];
        $options = [
            'redirect' => $redirect
        ];

        return parent::_delete($id, $type, $columns, $options);
    }

    public function detailDelete($id) {
        $this->checkLogin();

        $this->modelName = 'ImportDetails';

        $data = $this->ImportDetails->find()->where(['ImportDetails.id' => $id])->contain(['Imports'])->first();
        if (empty($data)) {
            $this->redirect('/');
        }

        $redirect = ['action' => 'import-details', $data->import_id, '?' => ['media_id' => $data->import->media_id]];

        $options = [
            'redirect' => $redirect
        ];
        return parent::_delete($id, 'content', null, $options);
    }


    public function setList() {
        
        $list = array();

        $list['import_type_list'] = Media::$import_type_list;

        $list['import_name_list'] = ImportConfig::$rows;

        $now = new \DateTime();
        foreach (range(2020,$now->format('Y')) as $year) {
            $list['year_list'][$year] = $year;
        }
        foreach (range(1,12) as $month) {
            $list['month_list'][$month] = $month;
        }


        if (!empty($list)) {
            $this->set(array_keys($list),$list);
        }

        $this->list = $list;
        return $list;
    }

    private function save_tsv($media, $file, $post_config) {
        $file_path = $file['tmp_name'];

        $configs = $this->ImportConfigs->find()->where(['ImportConfigs.media_id' => $media->id])->order(['ImportConfigs.position' => 'ASC']);
        if (empty($configs)) {
            return -1;
        }

        foreach ($configs as $conf) {
            if (!empty($post_config[$conf->id]['location'])) {
                $loc = mb_convert_kana($post_config[$conf->id]['location'], "a");
                if (preg_match('/[0-9a-zA-Z]+/', $loc) !== false) {
                    $conf->location = $loc;
                }
            }
        }

        $objReader = new Csv();
        $objReader->setInputEncoding($media->input_encoding);
        $objReader->setDelimiter("\t");
        $objReader->setEnclosure($media->enclosure);

        $objReader->setSheetIndex(0);
        $spreadsheet = $objReader->load($file_path);


        return $this->_save($media, $file, $configs, $spreadsheet);

    }

    private function save_csv($media, $file, $post_config) {
        $file_path = $file['tmp_name'];

        $configs = $this->ImportConfigs->find()->where(['ImportConfigs.media_id' => $media->id])->order(['ImportConfigs.position' => 'ASC']);
        if (empty($configs)) {
            return -1;
        }
        foreach ($configs as $conf) {
            if (!empty($post_config[$conf->id]['location'])) {
                $loc = mb_convert_kana($post_config[$conf->id]['location'], "a");
                if (preg_match('/[0-9a-zA-Z]+/', $loc) !== false) {
                    $conf->location = $loc;
                }
            }
        }

        $objReader = new Csv();
        $objReader->setInputEncoding($media->input_encoding);
        $objReader->setDelimiter(',');
        $objReader->setEnclosure($media->enclosure);

        $objReader->setSheetIndex(0);
        $spreadsheet = $objReader->load($file_path);
        

        return $this->_save($media, $file, $configs, $spreadsheet);

    }

    private function save_excel($media,$file, $post_config) {
        
        $file_path = $file['tmp_name'];

        $configs = $this->ImportConfigs->find()->where(['ImportConfigs.media_id' => $media->id])->order(['ImportConfigs.position' => 'ASC']);
        if (empty($configs)) {
            return -1;
        }
        foreach ($configs as $conf) {
            if (!empty($post_config[$conf->id]['location'])) {
                $loc = mb_convert_kana($post_config[$conf->id]['location'], "a");
                if (preg_match('/[0-9a-zA-Z]+/', $loc) !== false) {
                    $conf->location = $loc;
                }
            }
        }

        // 拡張子
        $ext = strtolower(substr(strrchr($file['name'], '.'), 1));
        if ($ext == 'xlsx') {
            $reader = new xlsx();
        } else {
            $reader = new xls();
        }
        $reader->setReadDataOnly(true);
        $spreadsheet = $reader->load($file_path);   // .xls ファイルからスプレッドシートをロード

        setlocale(LC_ALL, 'ja_JP.UTF-8');
        mb_language("Japanese");

        $sheet_name = '';
        if ($media->sheet_name) {
            $sheet_name = $media->sheet_name;
        }

        return $this->_save($media, $file, $configs, $spreadsheet, $sheet_name);
        
    }

    /**
     * メモリ不足　対策版
     * @param  [type] $media    [description]
     * @param  [type] $file     [description]
     * @param  [type] $configs  [description]
     * @param  [type] $callback [description]
     * @return [type]           [description]
     */
    private function _save($media, $file, $configs, $spreadsheet, $sheet_name='') {
        set_time_limit(0);

        if (empty($sheet_name)) {
            $worksheet = $spreadsheet->getSheet(0); // 最初のシートを $worksheet に読み込む
        } else {
            $worksheet = $spreadsheet->getSheetByName($sheet_name);
        }


        $last_row = $worksheet->getHighestRow();//最終行（最下段）の取得
        $last_col = $worksheet->getHighestColumn();//最終列（右端）の取得
        $limit = 100;
        $first_row = 0;

        $pages = $this->Round($last_row / $limit, 0, 2); // 切り上げ

        $now = new \DateTime();

        $import = [];
        $import['media_id'] = $media->id;
        $import['date'] = $now->format('Y-m-d');
        $import['import_details'] = [];
        $import['file_name'] = $file['name'];

        $entity = $this->Imports->newEntity($import, ['associated' => ['ImportDetails']]);

        $this->Imports->save($entity);
        $import['id'] = $entity->id;
        $position = 0;

        if ($media->is_plan == 1) {
            $plans = $this->MediaPlans->find()->where(['MediaPlans.media_id' => $media->id])->order(['MediaPlans.position' => 'ASC'])->all()->toArray();
        } else {
            $plans = ['one_loop'];
        }

        $insert_columns = [];

        for ($page = 0; $page < $pages; $page++) {
            $start = $first_row + ($limit * $page) + 1 + $media->import_start_row;;
            $end = $first_row + ($limit * ($page + 1)) + $media->import_start_row;;
            if ($end >= $last_row) {
                $end = $last_row;
            }
            $datas = $worksheet->rangeToArray("A{$start}:{$last_col}{$end}");

            $import['import_details'] = [];
            foreach ($datas as &$data) {
                foreach ($plans as $plan) {
                    //
                    $detail = [
                        'import_id' => $import['id'],
                        'price' => 0,
                        'amount' => 0,
                        'dl_count' => 0,
                        'isrc_registed' => 0
                    ];
                    $fixed_count = 0;
                    $is_data = true;

                    // プラン設定がある場合
                    if ($plan != 'one_loop') {
                        // プランのDL数
                        if (!is_numeric($plan->col_dl_count)) {
                            $plan_col_dl_count = $this->alpha2Num($plan->col_dl_count);
                        } else {
                            $plan_col_dl_count = $plan->col_dl_count - 1;
                        }
                        if (empty($data[$plan_col_dl_count])) {
                            continue;
                        }
                    }

                    foreach ($configs as $config) {
                        $col_number = $config->location;

                        if ($config->is_fixed == 0 && strval($col_number) === '') {
                            continue;
                        }

                        if ($config->is_fixed == 1 && !empty($this->request->getData($config->key_name))) {
                            $detail[$config->key_name] = $this->request->getData($config->key_name);
                            if ($config->key_name == 'ym') {
                                $detail[$config->key_name] = $this->request->getData('_ym_year') . sprintf("%02d",$this->request->getData('_ym_month'));
                                $detail[$config->key_name] = preg_replace('/[^0-9]/', '', $detail[$config->key_name]);
                                if (empty($detail[$config->key_name])) {
                                    $detail[$config->key_name] = 0;
                                }
                            }
                        } else {

                            if ($config->is_fixed == 1) {
                                $detail[$config->key_name] = '';
                                continue;
                            }

                            if (!is_numeric($col_number)) {
                                $col_number = $this->alpha2Num($config->location);
                            } else {
                                $col_number = $col_number - 1;
                            }

                            $detail[$config->key_name] = $data[intval($col_number)];

                            // 配信年月の書式違いに対応
                            if ($config->key_name == 'ym') {
                                if (!is_numeric($detail['ym']) || strlen($detail['ym']) != 6) {
                                    if (preg_match('/(20[0-9]{2})[\/\-\.]([0-9]{1,2})[\/\-\.]([0-9]{1,2})/', $detail['ym'])) {
                                        $_dt = new \DateTime($detail['ym']);
                                        $detail['ym'] = $_dt->format('Ym');
                                    } elseif (preg_match('/(20[0-9]{2})[\/\-\.]([0-9]{1,2})/', $detail['ym'])) {
                                        $_dt = \DateTime::createFromFormat("!Y-m", $detail['ym']);
                                        $detail['ym'] = $_dt->format('Ym');
                                    } elseif (preg_match('/20[0-9]{2}年[0-9]{1,2}月/', $detail['ym'])) {
                                        $detail['ym'] = preg_replace('/[^0-9]/', '', $detail['ym']);
                                    }
                                }
                                if (is_numeric($detail['ym']) && strlen($detail['ym']) == 8) {
                                    $_dt = \DateTime::createFromFormat("!Ymd", $detail['ym']);
                                    $detail['ym'] = $_dt->format('Ym');
                                }
                            }
                        }



                        if ($config->key_name == ImportConfig::ROW_ARTIST_NAME) {
                            if (empty($detail[$config->key_name])) {
                                $is_data = false;
                            }
                            $v = $this->getArtistId($detail[$config->key_name]);
                            $c = str_replace('_name', '_id', $config->key_name);
                            if ($v || empty($detail[$c])) {
                                $detail[$c] = $v;
                            }
                            $fixed_count += ($detail[$c] ? 1 : 0);


                        } elseif ($config->key_name == ImportConfig::ROW_MUSIC_TITLE) {
                            if (empty($detail[$config->key_name])) {
                                $is_data = false;
                            }
                            $v = $this->getMusicId($detail[$config->key_name]);
                            $c = str_replace('_name', '_id', $config->key_name);
                            if ($v || empty($detail[$c])) {
                                $detail[$c] = $v;
                            }
                            $fixed_count += ($detail[$c] ? 1 : 0);
                        
                        }  elseif ($config->key_name == ImportConfig::ROW_ISRC) {
                            $music = $this->Musics->getDataFromISRC($detail[$config->key_name]);
                            if (!empty($music)) {
                                $detail['music_id'] = $music->id;
                                $detail['artist_id'] = $music->artist_id;
                                $detail['isrc_registed'] = 1;
                                $fixed_count += 2;
                            }
                        } else {

                        }

                        $detail['fixed'] = 0;
                        if ($fixed_count >= ImportConfig::FIXED_COUNT) {
                            $detail['fixed'] = 1;
                        }
                    }

                    if (!$is_data) {
                        unset($detail);
                        continue;
                    }

                    if ($media->is_plan == 1  ) {
                        $detail['price'] = $plan->price;
                        $detail['dl_count'] = intval($data[$plan_col_dl_count]);
                        $detail['media_plan_id'] = $plan->id;
                        $detail['amount'] = 0;
                    }

                    // 単価、金額
                    $detail['price'] = $this->convertCurrency($detail['price']);
                    $detail['amount'] = $this->convertCurrency($detail['amount']);
                    if (empty($detail['amount'])) {
                        $detail['amount'] = $this->calcMul($detail['price'], $detail['dl_count'], 2);
                    }

                    $detail['row_data'] = json_encode($data);
                    $detail['position'] = ++$position;

                    $import['import_details'][] = $detail;
                    if (empty($insert_columns)) {
                        $insert_columns = array_keys($detail);
                    }
                    unset($detail);
                }
            }

            $q = $this->ImportDetails->query();
            $q->insert($insert_columns);
            foreach ($import['import_details'] as $d) {
                $q->values($d);
            }

            $q->execute();
                
            unset($datas);
            
        }


        return $entity->id;
    }

    private function _encode($datas) {
        foreach ($datas as $key => $val) {
            $datas[$key] = mb_convert_encoding($val, 'utf-8', 'cp932');
        }

        return $datas;
    }

    private function alpha2Num($str) {
        $alpha = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z'];
        $num = -1;

        if (strlen($str) == 1) {
            foreach ($alpha as $cnt => $a) {
                if ($str == $a) {
                    $num = $cnt;
                    break;
                }
            }
        } elseif (strlen($str) == 2) {
            $cnt = 0;
            foreach ($alpha as $a1) {
                foreach ($alpha as $a2) {
                    if ($str == $a1.$a2) {
                        $num = $cnt;
                        break;
                    }
                    $cnt++;
                }
            }
        }

        if ($num == -1) {
            return false;
        }

        return $num;

    }

    private function getArtistId($str) {
        $id = 0;
        $str = trim($str);

        $cond = [];
        $cond['OR'] = [
                ['Artists.name' => $str],
                ['Artists.kana' => $str],
                ["FIND_IN_SET(:find_str,`Artists`.`predicted_char`)"]
            ];
        $entity = $this->Artists->find()->where($cond)->bind(':find_str', $str, 'string')->first();

        if (!empty($entity)) {
            $id = $entity->id;
        }

        return $id;
    }
    private function getMusicId($str) {
        $id = 0;
        $str = trim($str);

        $cond = [];
        $cond['OR'] = [
                ['Musics.name' => $str],
                ['Musics.kana' => $str],
                ["FIND_IN_SET(:find_str,`Musics`.`predicted_char`)"]
            ];
        $entity = $this->Musics->find()->where($cond)->bind(':find_str', $str, 'string')->first();

        if (!empty($entity)) {
            $id = $entity->id;
        }

        return $id;
    }
    private function getCompanyId($str) {
        $id = 0;
        $str = trim($str);

        $cond = [];
        $cond['OR'] = [
                ['Companies.name' => $str],
                ['Companies.kana' => $str],
                ["FIND_IN_SET(:find_str,`Companies`.`predicted_char`)"]
            ];
        $entity = $this->Companies->find()->where($cond)->bind(':find_str', $str, 'string')->first();

        if (!empty($entity)) {
            $id = $entity->id;
        }

        return $id;
    }

    private function convertCurrency($price) {
        $tmp = explode('.', trim($price));

        if (is_array($tmp) && count($tmp) >= 2) {
            if ($tmp[0] == "") {
                $tmp[0] = '0';
            }

            return $tmp[0] . '.' . $tmp[1];
        }

        $price = trim($price);
        if (empty($price)) {
            $price = 0;
        }

        return $price;
    }
}
