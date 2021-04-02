<?php

namespace App\Model\Entity;

class ImportDetail extends AppEntity
{

    protected function _get_ymDate($value) {
        if (!empty($this->_properties['ym']) && strlen($this->_properties['ym']) == 6) {
            $dt = \DateTime::createFromFormat("!Ym", $this->_properties['ym']);
            return $dt;
        }

        return '';
    }


}
