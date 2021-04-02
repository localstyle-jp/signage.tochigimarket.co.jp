<?php

namespace App\Model\Entity;

class Music extends AppEntity
{

    protected function _setRate($value) {
        $rate = $value * 100;
        if (array_key_exists('_rate', $this->_properties)) {
            $rate = ( $this->_properties['_rate'] * 100);
        }

        return $rate;
    }

    protected function _get_rate($value) {
        $rate = 0;
        if (!empty($this->_properties['rate'])) {
            $rate = $this->_properties['rate'] * 0.01;
        }

        return $rate;
    }


}
