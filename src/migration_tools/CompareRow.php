<?php

namespace markorm_migration\migration_tools;



class CompareRow
{

    function merge(array $fields, ?array $csvRow, array $sqlRow)
    {
        $result = [];

        foreach ($fields as $filed) {
            $result[$filed] = $this->compare($filed, $sqlRow[$filed] ?? null, is_null($csvRow) ? false : $csvRow[$filed] ?? null);
        }

        return $result;
    }


    private function compare(string $filed, ?string $sqlItem, false | string | null $csvItem)
    {
        if ($csvItem === false)
            return $sqlItem;

        if ($sqlItem != $csvItem) {

            if (is_null($sqlItem)) 
                return $csvItem;


            return $csvItem;
            // throw new \Exception("Не знаю как обработать различие в значениях $filed (csv:$csvItem != sql:$sqlItem)", 51);
        }


        return $csvItem;
    }
}
