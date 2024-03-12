<?php

namespace markorm_migration\both;

use markdi\NotMark;
use markorm_migration\_markers\migration_tools;
use markorm_migration\csv\CsvTable;
use markorm_migration\sql\SQLTable;

#[NotMark]
class Header
{
    use migration_tools;



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
                $body[] = array_map(fn ($val) => $val == 'NULL' ? null : $val, $data);
                continue;
            }

            if (!$titles) {
                if ($data[0] == '@Field') {
                    $data = array_splice($data, 1);
                    $firstCollIsType = true;
                }

                $titles = $data;
                continue;
            }

            foreach ($data as $index => $value) {
                if (str_starts_with($value, '---')) {
                    $readHead = false;
                    break;
                }

                $coll = $titles[$index];

                if (!isset($head[$coll])) {
                    $head[$coll] = new Coll($coll, $value);
                    continue;
                }


                $currentColl = $head[$coll];


                if ($collType) {
                    $currentColl->set($collType, $value);
                } else {
                    $currentColl->auto($value);
                }
            }
        }


        $table->close();

        $this->colls = $head;
        return $body;
    }








    function getTranspose()
    {
        $result = [];


        foreach ($this->colls as $props) {
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
}
