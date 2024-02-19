<?php

namespace markorm_migration\out;

class Output {

    private $outputs = [];
    private ?string $outputFile = null; 


    function setOutputFile(?string $file = null){
        if (!file_exists($file) || !is_dir($file))
            throw new \Exception("Папка не существует или не является папкой ($file)", 1);
            
        $date = date("Y-m-d H:i:s");
        $this->outputFile = "$file/{$date}.sql";
    }


    function run(string $query){
        if ($this->outputFile)
            file_put_contents($this->outputFile, "$query;\n", FILE_APPEND);
    }


}