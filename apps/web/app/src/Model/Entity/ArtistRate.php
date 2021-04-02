<?php

namespace App\Model\Entity;

class ArtistRate extends AppEntity
{

    protected function _setRate($value) {
        $rate = $value * 100;
        if (array_key_exists('_rate', $this->_properties)) {
            $rate = ( $this->_properties['_rate'] * 100);
        }

        return $rate;
    }

    protected function _get_rate($value) {
        $rate = $this->_properties['rate'] * 0.01;

        return $rate;
    }


}
