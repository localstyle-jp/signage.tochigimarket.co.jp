<?php

namespace App\Controller\V1;

use Cake\Core\Configure;
use Cake\Network\Exception\ForbiddenException;
use Cake\Network\Exception\NotFoundException;
use Cake\View\Exception\MissingTemplateException;
use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use Cake\Utility\Hash;
use Cake\Mailer\Email;
use App\Form\ContactForm;

/**
 * Static content controller
 *
 * This controller will render views from Template/Pages/
 *
 * @link https://book.cakephp.org/3.0/en/controllers/pages-controller.html
 */
class ContactController extends AppController {
    private $list = [];

    public function initialize() {
        parent::initialize();

        $this->modelName = 'Infos';
        $this->set('ModelName', $this->modelName);
    }

    public function beforeFilter(Event $event) {
        // $this->viewBuilder()->theme('Admin');
        $this->viewBuilder()->setLayout('simple');

        $this->getEventManager()->off($this->Csrf);
    }

    public function index() {
    }

    /**
     * お問い合わせ確認
     * POST
     * @return [type] [description]
     */
    public function confirm() {
        if ($this->request->is(['post', 'put'])) {
            $data = $this->_checkForm();

            if ($data['is_valid'] == 1) {
                $this->session_write('contact.token', $data['token']);
            }

            $this->rest_success($data);
            return;
        }

        $this->rest_error(200, 1000);
    }

    /**
     * お問い合わせ送信
     * POST
     * @return [type] [description]
     */
    public function post() {
        if ($this->request->is(['post', 'put'])) {
            $data = $this->_checkForm();

            if ($data['is_valid'] == 1) {
                if ($this->request->getData('token') != $this->session_read('contact.token')) {
                    $this->rest_error(200, 1000, '送信トークンが異なります');
                    return;
                }
                $r = $this->_sendmail();
                if ($r) {
                    $this->rest_success(['is_valid' => 1]);
                    return;
                } else {
                    $this->rest_error(200, 1000, '送信できませんでした');
                    return;
                }
            }
        }

        $this->rest_error(200, 1000);
    }
    public function _checkForm() {
        $is_valid = 1;
        $contact_form = new ContactForm();
        $columns = $contact_form->schema()->fields();

        $post_data = $this->getJson();
        $isValid = $contact_form->validate($post_data);
        $token = '';
        $form = [];
        if ($isValid) {
            $token = $this->token();
            foreach ($columns as $col) {
                $value = $post_data[$col];
                if (is_null($value) || $value === 'null') {
                    $value = '';
                }
                $form[$col] = [
                    'error' => 0,
                    'value' => $value
                ];
            }
        } else {
            $errors = $contact_form->errors();
            foreach ($columns as $col) {
                $value = $post_data[$col];
                if (is_null($value) || $value === 'null') {
                    $value = '';
                }
                if (array_key_exists($col, $errors)) {
                    $errArgs = [];
                    foreach ($errors[$col] as $err) {
                        $errArgs[] = $err;
                    }

                    $form[$col] = [
                        'error' => 1,
                        'message' => implode('、', $errArgs),
                        'value' => $value
                    ];
                    $is_valid = 0;
                } else {
                    $form[$col] = [
                        'error' => 0,
                        'value' => $value
                    ];
                }
            }
        }

        $data = [
            'is_valid' => $is_valid,
            'token' => $token,
            'form' => $form
        ];

        return $data;
    }

    /**
     * メール送信
     * @return [type] [description]
     */
    private function _sendmail() {
        $r = false;

        $form = $this->getJson(null);

        // 管理者へメール
        $email = new Email('default');
        $email->setCharset('ISO-2022-JP');
        $r = $email->setFrom(ContactForm::ORDER_MAIL_FROM)
                  ->setTo(ContactForm::ORDER_MAIL_ADMIN)
                  ->setSubject(ContactForm::ORDER_MAIL_SUBJECT_ADMIN)
                  ->setTemplate('contact_admin')
                  ->setViewVars(['form' => $form])
                  ->send();

        // ユーザーへメール
        if ($r) {
            $email = new Email('default');
            $email->setCharset('ISO-2022-JP');
            $r = $email->setFrom(ContactForm::ORDER_MAIL_FROM)
                      ->setTo($form['email'])
                      ->setSubject(ContactForm::ORDER_MAIL_SUBJECT_USER)
                      ->setTemplate('contact_user')
                      ->setViewVars(['form' => $form])
                      ->send();
        }

        return $r;
    }
}
