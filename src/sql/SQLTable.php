<?php

namespace markorm_migration\sql;

use markorm_migration\_markers\controllers;
use markorm_migration\_markers\migration_connect;
use markorm_migration\_markers\migration_tools;

class SQLTable
{
    use migration_connect;
    use controllers;
    use migration_tools;

    private $colls;

    function __construct(public string $name)
    {
        $this->colls = $this->tableController->getColls($this);
    }


    function saveToCsv(string $pathTo)
    {
        if (!file_exists($pathTo))
            mkdir($pathTo, 0777, true);

        $this->writeFile("$pathTo/{$this->name}.csv", $this->transpose($this->colls));
    }


    private function transpose(array $array)
    {
        $result = [];
        foreach ($array as $props) {
            foreach ($props as $key => $value) {
                // if (empty($result[$key]))
                //     $result[$key][] = "@$key";

                if ($key == 'Type' && $newType = $this->types->typeCode($value))
                    $value = $newType;

                $result[$key][] = $value;
            }
        }

        return $result;
    }





    private function writeFile(string $fileName, array $data)
    {
        $df = fopen($fileName, 'w');
        foreach ($data as $row) {
            fputcsv($df, $row);
        }
        fclose($df);
    }
}
