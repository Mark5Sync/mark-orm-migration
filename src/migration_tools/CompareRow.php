<?php

namespace markorm_migration\migration_tools;



class CompareRow
{

    function merge(array $sqlRow, array $csvRow)
    {
        $result = [];

        foreach ($csvRow as $key => $csvRowItem) {
            $sqlRowItem = $sqlRow[$key];
            if ($csvRowItem != $sqlRowItem)
                throw new \Exception("Не знаю как обработать различие в значениях (csv:$csvRowItem != sql:$sqlRowItem)", 55);
                

            $result[$key] = $csvRowItem;
        }

        return $result;
    }
}
