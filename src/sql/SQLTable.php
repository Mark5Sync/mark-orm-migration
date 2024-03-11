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

        $header = $this->transpose($this->colls);


        $file = fopen("$pathTo/{$this->name}.csv", 'w');
        $colls = $header['Field'];

        $this->writeFile($file, [
            ...$header,
            array_fill(0, count($colls), '---'),
        ]);



        $data = [];

        foreach ($this->for() as $index => $row) {

            foreach ($colls as $coll) {
                $data[$index][$coll] = is_null($row[$coll]) ? 'NULL' : $row[$coll];
            }

            if (count($data) > 10) {
                $this->writeFile($file, $data);
                $data = [];
            }
        }

        if (!empty($data))
            $this->writeFile($file, $data);

        fclose($file);
    }


    private function transpose(array $array)
    {
        $result = [];
        foreach ($array as $props) {
            foreach ($props as $key => $value) {
                if (empty($result[$key]))
                    $result[$key][] = "@$key";

                if ($key == 'Type' && $newType = $this->types->typeCode($value))
                    $value = $newType;

                $result[$key][] = $value;
            }
        }


        return $result;
    }





    private function writeFile($fileStream, array $data, $append = false)
    {

        foreach ($data as $row) {
            fputcsv($fileStream, $row);
        }
    }



    private function for()
    {
        $smtp = $this->connection->query("SELECT * FROM {$this->name}");
        foreach ($smtp->fetchAll(\PDO::FETCH_ASSOC) as $index => $row) {
            yield $index => $row;
        }
    }
}
