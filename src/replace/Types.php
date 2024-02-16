<?php

namespace markorm_migration\replace;


class Types
{

    private $codes = [
        '@id' => "INT(11) PRIMARY KEY AUTO_INCREMENT",
        'string' => 'VARCHAR(300)',
        'int' => 'INT(11)',
    ];


    function check($value)
    {
        $lowValue = strtolower($value);
        if (isset($this->codes[$lowValue]))
            return $this->codes[$lowValue];

        return false;
    }

}
