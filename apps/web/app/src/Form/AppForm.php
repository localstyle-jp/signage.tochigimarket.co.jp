<?php
namespace App\Form;

use Cake\Form\Form;
use Cake\Form\Schema;
use Cake\Validation\Validator;

class AppForm extends Form {

    public function checkEmail($value, $context) {

        return (bool) preg_match('/\A[a-zA-Z0-9_-]([a-zA-Z0-9_\!#\$%&~\*\+-\/\=\.]+)*@[a-zA-Z0-9-]+(\.[a-zA-Z0-9-]+)*\.([a-zA-Z]{2,20})\z/', $value);
    }

    public function checkPostcode($value, $context) {

        return (bool) preg_match('/[0-9]{3}-[0-9]{4}/', $value);
    }

    public function checkTel($value, $context) {

        return (bool) preg_match('/^(0\d{1,4}[\s-]?\d{1,4}[\s-]?\d{4})$/', $value);
    }

    public function getErrors() {
      return $this->errors();
    }
}

?>
