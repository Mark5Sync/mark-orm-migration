<?php


namespace markorm_migration\both;

use markdi\NotMark;
use markorm_migration\_markers\controllers;
use markorm_migration\_markers\migration_tools;
use markorm_migration\csv\CsvTable;
use markorm_migration\sql\SQLTable;

#[NotMark]
class Header
{
    use migration_tools;
    use controllers;

    public $headerFields = [];
    public $relationsTables = [];

    /** 
     * @var Coll[]
     */
    private $colls = [];


    function initFromCsv(CsvTable $table)
    {
        $table->open();


        $readHead = true;
        $titles = false;
        /** 
         * @var ?string[][]
         */
        $body = [];
        /** 
         * @var Coll[]
         */
        $head = [];

        $firstCollIsType = false;
        $collType = false;

        while (($data = $table->read()) !== false) {
            if ($firstCollIsType)
                $collType = array_shift($data);

            if (!$readHead) {
                $row = [];

                foreach ($data as $index => $val) {
                    $field = $this->headerFields[$index];
                    $row[$field] = $val == 'NULL' ? null : $val;
                }

                $body[] = $row;
                continue;
            }

            if (!$titles) {
                if (strtolower($data[0]) == '@field') {
                    $data = array_splice($data, 1);
                    $firstCollIsType = true;
                }

                $titles = $data;
                continue;
            }

            foreach ($data as $index => $value) {
                if (str_starts_with($value, '---')) {
                    $this->headerFields = array_keys($head);
                    $readHead = false;
                    break;
                }

                $coll = $titles[$index];

                if (!isset($head[$coll])) {
                    $head[$coll] = new Coll($coll, $value);
                    continue;
                }


                $currentColl = $head[$coll];
                $valueLowerCase = strtolower($value);

                if ($collType) {
                    $currentColl->set($collType, $valueLowerCase);
                } else {
                    $currentColl->auto($valueLowerCase);
                }


                if ($currentColl->relationTable)
                    $this->relationsTables[] = $currentColl->relationTable;
            }
        }


        $table->close();

        $this->colls = $head;
        return $body;
    }



    function initFromSql(SQLTable $table)
    {
        $colls = $this->tableController->getColls($table);

        foreach ($colls as [
            'Field' => $field, 
            'Type' => $type, 
            'Null' => $isNull, 
            'Key' => $key, 
            'Default' => $default, 
            'Extra' => $extra,
            'Relation' => $relation,
        ]) {
            $coll = new Coll($field, $type);

            $coll->set('@null', $isNull);
            $coll->set('@key',  $key);
            if ($default)
                $coll->set('@default',  $default);


            if ($extra)
                $coll->set('@extra',  $extra);

            if ($relation){
                $coll->set('@relationTable', $relation['table']);
                $coll->set('@relationColl',  $relation['coll']);

                $this->relationsTables[] = $relation['table'];
            }

            $this->colls[$field] = $coll;
        }

    }




    function getTranspose()
    {
        $result = [];


        foreach ($this->colls as $coll) {
            foreach ($coll->props as $prop) {
                $value = $coll->{$prop};
                if (empty($result[$prop]))
                    $result[$prop][] = "@$prop";

                if ($prop == 'type' && $newType = $this->types->typeCode($value))
                    $value = $newType;

                $result[$prop][] = $value;
            }
        }


        return $result;
    }





    function getCollsSqlFormat()
    {
        $result = [];

        foreach ($this->colls as $coll) {
            $result[] = $coll->toSql();
        }

        return $result;
    }
}
