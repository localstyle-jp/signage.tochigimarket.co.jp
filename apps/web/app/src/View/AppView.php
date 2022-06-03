<?php
/**
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link      https://cakephp.org CakePHP(tm) Project
 * @since     3.0.0
 * @license   https://opensource.org/licenses/mit-license.php MIT License
 */
namespace App\View;

use Cake\View\View;
use App\Model\Entity\User;

/**
 * Application View
 *
 * Your application’s default view class
 *
 * @link https://book.cakephp.org/3.0/en/views.html#the-app-view
 */
class AppView extends View
{

    /**
     * Initialization hook method.
     *
     * Use this method to add common initialization code like loading helpers.
     *
     * e.g. `$this->loadHelper('Html');`
     *
     * @return void
     */
    public function initialize()
    {
        parent::initialize();
        $this->loadHelper('Common');
        $this->loadHelper('Html', ['className' => 'MyHtml']);
        $this->loadHelper('Form', ['className' => 'MyForm',
                                   'templates' => [
                                       'inputContainer' => '{{content}}',
                                        'inputContainerError' => '{{content}}<div class="error-message">{{error}}</div>',
                                        'nestingLabel' => '{{input}}<label{{attrs}}>{{text}}</label>',
                                        'radio' => '<input type="radio" name="{{name}}" value="{{value}}"{{attrs}}>',
                                        'radioWrapper' => '<span style="margin-right:10px;color:#000;">{{label}}</span>'
                                   ]]);

        $user_roles = [
            'develop' => User::ROLE_DEVELOP,
            'admin' => User::ROLE_ADMIN,
            'cms' => User::ROLE_CMS
        ];
        $this->set(compact('user_roles'));

        $this->setOptions();

    }

    private function setOptions() {
        $search_templates = [
            'inputContainer' => '{{content}}',
            'inputContainerError' => '{{content}}{{error}}',
            'nestingLabel' => '{{input}}<label{{attrs}} class="form-check-label">{{text}}</label>',
            
            'radio' => '<input type="radio" name="{{name}}" value="{{value}}"{{attrs}}>',
            'radioWrapper' => '<div class="radio icheck-midnightblue d-inline mr-2">{{label}}</div>',

            'error' => '<p class="error-msg">{{content}}</p>',
            
            'checkbox' => '<input type="checkbox" name="{{name}}" value="{{value}}"{{attrs}}>',
            'checkboxFormGroup' => '{{label}}',
            'checkboxWrapper' => '<div class="checkbox-item checkbox-item--small checkbox-item--white">{{label}}</div>',
         ];
         
         $this->set(compact('search_templates'));
    }
}
