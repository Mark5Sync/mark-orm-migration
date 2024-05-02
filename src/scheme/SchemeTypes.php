<?php

namespace markorm_migration\scheme;

class SchemeTypes {

    function convert(string $type) {

        switch ($type) {
            case 'int(2)':
            case 'int(1)':
                return "BOOLEAN";

            case 'int(11)':
            case 'int(20)':
                return "INT";

            case 'varchar(300)':
            case 'varchar(255)':
            case 'varchar(250)':
            case 'varchar(200)':
                return "STRING";

            case 'datetime':
                return "DATE";

            case 'decimal(5,2)':
            case 'decimal(10,2)':
                return "FLOAT";

            case 'text':
            case 'longtext';
                return "TEXT";

            case 'date':
                return "DATEONLY";

            default:
                echo "\n\n - undefined type: $type\n\n";
                return $type;
        }

    }

}