<?php

namespace markorm_migration\out;

use markorm_migration\_markers\migration_connect;

class Output {
    use migration_connect;

    private $outputs = [];
    private ?string $outputFile = null; 


    function setOutputFile(?string $file = null){
        if (!file_exists($file))
            mkdir($file, 0777, true);
            // throw new \Exception("Папка не существует или не является папкой ($file)", 1);
            
        $date = date("Y-m-d H:i:s");
        $this->outputFile = "$file/{$date}.sql";
    }


    function run(string $query){
        if ($this->outputFile)
            file_put_contents($this->outputFile, "$query;\n", FILE_APPEND);

        $this->connection->query($query);
    }


}