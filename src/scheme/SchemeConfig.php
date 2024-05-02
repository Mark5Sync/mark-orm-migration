<?php

namespace markorm_migration\scheme;

class SchemeConfig {
    private $tables = [];

    function __construct(string $schemePath) {
        if (!file_exists($schemePath))
            return;

        $scheme = json_decode(file_get_contents($schemePath));

        $this->tables = $scheme->tables;
    }


    function getTable(string $tableName){
        foreach ($this->tables as $table) {
            if ($table->name == $tableName)
                return $table;
        }
    }

}