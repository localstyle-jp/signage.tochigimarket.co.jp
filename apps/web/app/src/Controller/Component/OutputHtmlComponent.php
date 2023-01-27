<?php
namespace App\Controller\Component;

use Cake\Controller\Component;
use Cake\Controller\ComponentRegistry;
use Cake\Datasource\ModelAwareTrait;

/**
 * OutputHtml component
 */
class OutputHtmlComponent extends Component {
    /**
     * Default configuration.
     *
     * @var array
     */
    protected $_defaultConfig = [];

    use ModelAwareTrait;

    public function index($slug) {
        $dir = USER_PAGES_DIR . $slug;
        $file = $dir . DS . 'index.html';

        $params = explode('/', $slug); // [0]=site_name [1]=page_name

        $html = $this->_registry->getController()->requestAction(
            ['controller' => 'Contents', 'action' => 'index', 'pass' => ['site_slug' => $params[0], 'slug' => $params[1]]],
            ['return', 'bare' => false]
        );

        file_put_contents($file, $html);

        chmod($file, 0666);
    }

    /**
     * 記事詳細の書き出し　body,meta情報等の外枠のみ
     * @param  [type] $info_id [description]
     * @return [type]          [description]
     */
    public function detail($model, $info_id, $slug) {
        $this->loadModel($model);

        if (empty($slug)) {
            $slug = 'home';
        }

        $params = explode('/', $slug);

        $info = $this->{$model}->find()->contain(['Categories'])->where([$model . '.id' => $info_id])->first();

        if (empty($info)) {
            $dir = USER_PAGES_DIR . $slug;
            $file = $dir . DS . "{$info_id}.html";
            if (is_file($file)) {
                @unlink($file);
            }
            $file = $dir . DS . "data/{$info_id}.json";
            if (is_file($file)) {
                @unlink($file);
            }
            return;
        }

        $dir = USER_PAGES_DIR . $slug;
        $file = $dir . DS . "{$info_id}.html";

        if ($info->status == 'draft' || ($info->category_id > 0 && $info->category->status == 'draft')) {
            if (is_file($file)) {
                @unlink($file);
            }
        } else {
            $html = $this->_registry->getController()->requestAction(
                ['controller' => 'Contents', 'action' => 'detail', 'pass' => ['site_slug' => $params[0], 'slug' => $params[1], 'id' => $info_id]],
                ['return', 'bare' => false]
            );

            file_put_contents($file, $html);

            chmod($file, 0666);
        }
    }

    public function writeJson($data, $info_id, $status, $slug) {
        $json = json_encode($data);

        if (empty($slug)) {
            $slug = 'home';
        }

        $dir = USER_PAGES_DIR . $slug . DS . USER_JSON_URL;
        $file = $dir . DS . "{$info_id}.json";

        if ($status == 'draft') {
            if (is_file($file)) {
                @unlink($file);
            }
        } else {
            file_put_contents($file, $json);

            chmod($file, 0666);
        }
    }

    public function _existsJson($info_id, $slug) {
        $dir = USER_PAGES_DIR . $slug . DS . USER_JSON_URL;
        $file = $dir . DS . "{$info_id}.json";

        if (is_file($file)) {
            return true;
        }
        return false;
    }

    private function userId() {
        return $this->_registry->getController()->getUserId();
    }
}
