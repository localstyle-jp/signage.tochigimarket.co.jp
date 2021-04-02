<?php

namespace App\Controller\User;

use Cake\Core\Configure;
use Cake\Network\Exception\ForbiddenException;
use Cake\Network\Exception\NotFoundException;
use Cake\View\Exception\MissingTemplateException;
use Cake\Event\Event;
use Cake\ORM\TableRegistry;

use App\Model\Entity\Media;

/**
 * Static content controller
 *
 * This controller will render views from Template/Pages/
 *
 * @link https://book.cakephp.org/3.0/en/controllers/pages-controller.html
 */
class ArtistsController extends AppController
{
    private $list = [];

    public function initialize()
    {
        $this->ImportDetails = $this->getTableLocator()->get('ImportDetails');
        $this->ArtistRates = $this->getTableLocator()->get('ArtistRates');
        $this->Musics = $this->getTableLocator()->get('Musics');

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

        $query['sch_artist'] = $this->request->getQuery('sch_artist');
        $query['sch_artist'] = str_replace('　', ' ', $query['sch_artist']);

        return $query;
    }

    private function _getConditions($query) {
        $cond = [];
        $cnt = 0;

        if ($query['sch_artist']) {
            $keywords = explode(' ', $query['sch_artist']);
            foreach ($keywords as $word) {
                $cond[$cnt++]['OR'] = [
                    ['Artists.name collate utf8_unicode_ci like' => "%{$word}%"],
                    ['Artists.kana collate utf8_unicode_ci like' => "%{$word}%"],
                    ['Artists.predicted_char like' => "%{$word}%"]
                ];
            }
        }

        return $cond;
    }

    public function index() {
        $this->checkLogin();

        $this->setList();

        $query = $this->_getQuery();
        $cond = $this->_getConditions($query);

        $is_search = ($this->request->getQuery() ? true : false);

        $this->set(compact('query', 'is_search'));

        $this->_lists($cond, ['order' => 'position ASC',
                              'limit' => null]);
    }

    public function edit($id=0) {
        $this->checkLogin();

        $this->setList();
        $get_callback = null;
        $callback = null;
        $redirect = ['action' => 'index'];
        $rates = [];

        if ($this->request->getQuery('detail_id')) {
            $import_detail = $this->ImportDetails->find()->where(['ImportDetails.id' => $this->request->getQuery('detail_id')])->first();
            $get_callback = function ($data) use($import_detail) {
                $data['name'] = $import_detail->artist_name;
                return $data;
            };
            // $redirect = ['controller' => 'media', 'action' => 'import-details', $import_detail->import_id, '?' => ['media_id' => $this->request->getQuery('media_id')]];
        }

        if ($this->request->is(['post','put'])) {
        } else {

            $rates = $this->_getRates($id);
        }

        $callback = function($id) {
            $entity = $this->Artists->find()->where(['Artists.id' => $id])->first();
            $this->ArtistRates->deleteAll(['ArtistRates.artist_id' => $id, 'ArtistRates.position >' => $entity->member_count]);
        };

        $associated = ['ArtistRates'];

        $options = [
            'callback' => $callback,
            'get_callback' => $get_callback,
            'redirect' => $redirect,
            'associated' => $associated
        ];

        // インポートデータの存在チェック
        $is_import_data = false;
        $imports = $this->ImportDetails->find()->where(['ImportDetails.artist_id' => $id])->count();
        
        if ($imports) {
            $is_import_data = true;
        } else {
            $musics = $this->Musics->find()->where(['Musics.artist_id' => $id])->count();
            if (!empty($musics)) {
                $is_import_data = true;
            }
        }


        parent::_edit($id, $options);

        $this->set(compact('rates', 'is_import_data'));

    }

    public function addRate() {
        $this->viewBuilder()->setLayout("plain");

        $post = $this->request->getData();
        
        $rates = $this->_getRates($post['artist_id'], false, $post['member_count']);

        $this->set(compact('rates'));
    }

    private function _getRates($artist_id, $is_readonly = true, $member_count = 0) {
        $alpha = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z'];

        $rates = [];
        $rate_adjust = 0;
        $datas = $this->ArtistRates->find()->where(['ArtistRates.artist_id' => $artist_id])->all()->toArray();

        if ($is_readonly) {
            $artist = $this->Artists->find()->where(['Artists.id' => $artist_id])->first();
            if (empty($artist)) {
                return $rates;
            }
            $member_count = $artist->member_count;
            $rate = false;
        } else {
            $rate = $this->Round($this->calcDiv('100', strval($member_count)), 1);
            // $rate_adjust = 100 - ($rate * $member_count);
        }
        if (intval($member_count) === 1) {
            return $rates;
        }

        for ($i=0; $i < $member_count; $i++) {
            if (empty($datas[$i])) {
                $rates[] = [
                    'id' => null,
                    'position' => ($i + 1),
                    'name' => $alpha[$i],
                    'rate' => $rate
                ];
            } else {
                $rates[] = [
                    'id' => $datas[$i]->id,
                    'position' => ($i + 1),
                    'name' => $datas[$i]->name,
                    'rate' => ($rate === false ? $datas[$i]->_rate : $rate)
                ];
            }
        }

        if (!empty($rates[0]['rate'])) {
            $rates[0]['rate'] += $rate_adjust;
        }

        return $rates;
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

        $list['import_type_list'] = Media::$import_type_list;


        if (!empty($list)) {
            $this->set(array_keys($list),$list);
        }

        $this->list = $list;
        return $list;
    }

    public function popList() {
        $this->viewBuilder()->setLayout("pop");

        $query = $this->_getQueryPop();
        $cond = $this->_getConditionsPop($query);
        $this->set(compact('query'));

        $this->_lists($cond, ['limit' => 10, 'order' => ['Artists.position' => 'ASC']]);

    }
    private function _getQueryPop() {
        $query = [];

        $query = $this->_getQuery();

        return $query;
    }

    private function _getConditionsPop($query) {
        $cond = [];

        $cond = $this->_getConditions($query);
        return $cond;
    }

    public function addTag() {
        $this->viewBuilder()->setLayout("plain");

        $id = $this->request->getData('id');
        $num = $this->request->getData('num');
        $tag = [
            'id' => 0,
            'name' => ''
        ];

        $data = $this->Artists->find()->where(['Artists.id' => $id])->first();
        if (!empty($data)) {
            $tag['id'] = $data->id;
            $tag['name'] = $data->name;
        }

        $this->set(compact('tag', 'num'));
        
    }

}
