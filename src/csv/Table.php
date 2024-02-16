<?php

namespace markorm_migration\csv;

use markdi\NotMark;
use markorm_migration\_markers\replace;

#[NotMark]
class Table
{
    use replace;

    public readonly string $name;
    private $head = [];
    private $body = [];

    public $depends = [];



    function __construct(private string $csvFile)
    {
        $info = pathinfo($csvFile);
        $this->name = $info['filename'];
        $this->read();
    }



    private function read(): void
    {
        $handle = fopen($this->csvFile, "r");
        if (!$handle)
            throw new \Exception("Невозможно прочитать файл ($this->csvFile)", 1);

        $readHead = true;
        $titles = false;

        while (($data = fgetcsv($handle, 1000, ",")) !== false) {
            if (!$readHead) {
                $this->body[] = $data;
                continue;
            }

            if (!$titles) {
                $titles = $data;
                continue;
            }

            foreach ($data as $index => $value) {
                if (str_starts_with($value, '---')) {
                    $readHead = false;
                    break;
                }


                if (!isset($this->head[$titles[$index]])) {
                    $this->head[$titles[$index]] = new Coll($titles[$index], $value, $this);
                    continue;
                }

                $this->head[$titles[$index]]->auto($value);
            }
        }



        fclose($handle);
    }



    function getCreateStringHeader(): string
    {
        return implode(', ', $this->head);
    }



    function compare(array $colls)
    {
        $currentColls = [];
        $notExistsColls = [];

        foreach ($colls as $coll) {
            $currentColls[$coll['Field']] = $coll;
        }


        foreach ($this->head as $field => $coll) {
            if (!isset($currentColls[$field]))
                $coll->create();

            [
                'Type' => $type,
                'Null' => $null,
                'Key' => $key,
                'Default' => $default,
                'Extra' => $extra
            ] = $currentColls[$field];


            $coll->compare(
                $type,
                $null == 'YES',
                $key,
                $default,
                $extra,
            );
        }
    }



    function addDepends(string $table)
    {
        $this->depends[] = $table;
    }
}
