<?php

namespace markorm_migration\csv;

use markorm_migration\_markers\out;

class TablesSync
{  
    use out; 

    private ?string $folder;


    function from(string $folder)
    {
        $this->folder = $folder;
        return $this;
    }



    function start(?string $outputFile = null)
    {
        if (!$this->folder)
            throw new \Exception("Нужно указать исходную папку с таблицами ->from(...)", 1);

        if (!file_exists($this->folder))
            throw new \Exception("Папка ненайдена ($this->folder)", 1);

        if ($outputFile)
            $this->output->setOutputFile($outputFile);


        $map = new TablesMap;


        $files = array_diff(scandir($this->folder), ['.', '..']);
        foreach ($files as $file) {
            $map->add(new Table("$this->folder/$file"));
        }


        $map->sync($outputFile);
    }



}
