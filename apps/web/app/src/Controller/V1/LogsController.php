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

// use App\Model\Entity\Material;
// use App\Model\Entity\User;

/**
 * Static content controller
 *
 * This controller will render views from Template/Pages/
 *
 * @link https://book.cakephp.org/3.0/en/controllers/pages-controller.html
 */
class LogsController extends AppController {
    private $list = [];

    public function initialize() {
        parent::initialize();

        $this->Logs = $this->getTableLocator()->get('Logs');
    }

    public function beforeFilter(Event $event) {
        // $this->viewBuilder()->theme('Admin');
        $this->viewBuilder()->setLayout('simple');

        $this->getEventManager()->off($this->Csrf);
    }

    /**
     *
     * ログを収集する
     *
     */
    public function add() {
        $content = $this->request->getData('text') ?? 'テスト';

        $entity = $this->Logs->newEntity(['content' => $content]);
        $saved = $this->Logs->save($entity);
        $code = $saved ? 200 : 400;
        return $this->setApi(['message' => 'エラーログを集計しました。', 'content' => $content], $code);
    }
}
