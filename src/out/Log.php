<?php

namespace markorm_migration\out;

class Log
{

    private $stack = [];

    function write(string $message, ?string $description = null)
    {
        $desc = $description ? '' : "\n\t\t| $description";

        echo " - $message\n";
        $this->stack[] = <<<LOG
            - $message$desc
        LOG; 
    }
}
