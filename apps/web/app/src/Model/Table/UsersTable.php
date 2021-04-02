<?php 
namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;
use Cake\ORM\TableRegistry;

class UsersTable extends AppTable {

    // テーブルの初期値を設定する
    public $defaultValues = [
        "id" => null
    ];

    public $attaches = array('images' =>
                            array(),
                            'files' => array(),
                            );
    
                            // 
    public function initialize(array $config)
    {

        parent::initialize($config);
        
    }

    public function validationCsvImport(Validator $validator) {
        $validator->setProvider('User', 'App\Validator\UserValidation');

        $validator = $this->validationDefault($validator);

        $validator
            ->notEmpty('password', 'パスワードを入力してください')
            ->add('password', 'maxlength', ['rule' => ['maxLength', 30], 'message' => '30文字以内で入力してください'])
            ->add('password', 'check_password', ['rule' => ['checkPasswordRule'], 'provider' => 'User', 'message' => 'このパスワードは使えません'])
            ;

        return $validator;
    }

    public function validationNew(Validator $validator) {
        $validator = $this->validationDefault($validator);
        $validator = $this->validationFirstPassword($validator);

        return $validator;
    }

    public function validationModify(Validator $validator) {
        $validator = $this->validationDefault($validator);

        return $validator;
    }
    
    public function validationModifyIsPass(Validator $validator) {
        $validator = $this->validationDefault($validator);
        $validator = $this->validationUserRegist($validator);

        return $validator;
    }

    // Validation
    public function validationDefault(Validator $validator)
    {
        $validator->setProvider('User', 'App\Validator\UserValidation');

        $validator
            ->notEmpty('name', '入力してください')
            ->add('name', 'maxLength', [
                'rule' => ['maxLength', 40],
                'message' => '40文字以内で入力してください'])

            // ->notEmpty('email', '入力してください')
            // ->add('email', 'maxLength', [
            //     'rule' => ['maxLength', 200],
            //     'message' => __('200字以内で入力してください')])
            // ->add('email', 'custom', [
            //     'rule' => ['isUnique'],
            //     'provider' => 'User',
            //     'message' => 'このメールアドレスは既に登録済みです'])

            ->notEmpty('username', '入力してください')
            ->add('username', 'chkUserName', [
                'rule' => ['checkUsername'],
                'provider' => 'User',
                'message' => '使えない文字が含まれています'])
            ->add('username', 'Length', [
                'rule' => ['lengthBetween', 3, 30],
                'message' => '3文字以上30文字以内で入力してください'])
            ->add('username', 'unique', [
                'rule' => ['isUnique'],
                'provider' => 'User',
                'message' => 'ご希望のユーザーIDは既に使われております'])

            ->notEmpty('company_id', '選択してください')
            
            ;
        
        return $validator;
    }

    public function validationFirstPassword(Validator $validator) {
        $validator->setProvider('User', 'App\Validator\UserValidation');

        $validator
            ->notEmpty('_password', '入力してください')
            ->add('_password', 'maxlength', ['rule' => ['maxLength', 30], 'message' => '30文字以内で入力してください'])
            ->add('_password', 'check_password', ['rule' => ['checkPasswordRule'], 'provider' => 'User', 'message' => 'このパスワードは使えません'])
            ;
        
        return $validator;
    }

    public function validationUserRegist(validator $validator)
    {
        $validator->setProvider('User', 'App\Validator\UserValidation');

        $validator
            ->notEmpty('password', '入力してください')
            ->add('password', 'comWith', ['rule' => ['compareWith', 'password_confirm'], 'message' => 'パスワードが一致しません'])
            ->add('password', 'maxlength', ['rule' => ['maxLength', 30], 'message' => '30文字以内で入力してください'])
            ->add('password', 'check_password', ['rule' => ['checkPasswordRule'], 'provider' => 'User', 'message' => 'このパスワードは使えません'])
            ;

        return $validator;
    }

    public function getList($cond=[])
    {
        $query = $this->find()->where($cond);
        $list = [];

        if ($query->isEmpty()) {
            return $list;
        }

        $datas = $query->toArray();
        foreach ($datas as $val) {
            $list[$val['id']] = $val['list_name'];
        }

        return $list;

    }


}