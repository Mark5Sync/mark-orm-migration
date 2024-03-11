<?php

namespace markorm_migration\migration_tools;


class Types
{

    private $codes = [
        '@id' => "int(11) PRIMARY KEY AUTO_INCREMENT",
        'string' => 'varchar(300)',
        'int' => 'int(11)',
    ];
    private $types;

    function __construct()
    {
        $this->typeToCodes();
    }

    function check($value)
    {
        $lowValue = strtolower($value);
        if (isset($this->codes[$lowValue]))
            return $this->codes[$lowValue];

        return false;
    }


    function typeCode(string $type)
    {
        $lowType = strtolower($type);
        if (isset($this->types[$lowType]))
            return $this->types[$lowType];

        return false;
    }







    private function typeToCodes()
    {
        $result = [];

        foreach ($this->codes as $code => $type) {
            $result[$type] = $code;
        }

        $this->types = $result;
    }
}
