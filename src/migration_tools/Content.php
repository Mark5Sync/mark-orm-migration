<?php

namespace markorm_migration\migration_tools;

use marksync\provider\Mark;
use markorm_migration\_markers\migration_connect;
use markorm_migration\_markers\out;
use markorm_migration\csv\Table;

#[Mark(mode: Mark::LOCAL, args: ['parent'])]
class Content
{
    use migration_connect;
    use out;

    private $insertData = [];
    private $updateData = [];


    function __construct(private Table $table)
    {
    }



    function compare()
    {
        if (count($this->table->body) == 0)
            return;

        foreach ($this->runCompare() as $fileData => $sqlData) {
            if (empty($sqlData)) {
                $this->insert($fileData);
            }
        }

        $this->runInsert();
    }



    private function runCompare()
    {
        foreach ($this->table->body as $row) {
            $data = [];
            foreach (array_keys($this->table->head) as $index => $coll) {
                $data[$coll] = $row[$index];
            }

            yield $data => $this->sqlData($data);
        }
    }



    private function sqlData($fileData)
    {
        $where = [];
        foreach ($fileData as $coll => $value) {
            $where[] = "$coll = " . var_export($value, true);
        }

        $query = "SELECT * FROM `{$this->table->name}` WHERE " . implode(' AND ', $where);


        $result = $this->connection->query($query)->fetchAll(\PDO::FETCH_ASSOC);
        return $result;
    }



    private function insert($rowData)
    {
        $head = [];
        $body = [];

        foreach ($rowData as $coll => $value) {
            $head[] = $coll;
            $body[":$coll"] = var_export($value, true);
        }

        $headStr = implode(', ', $head);
        $bodyStr = implode(', ', array_values($body));

        $dublicateHeader = implode(", \n", array_map(fn ($itm) => "$itm=VALUES($itm)", array_filter($head, fn($itm) => $itm!='id')));

        $this->insertData[$this->table->name][$headStr]['data'][] = $bodyStr;
        $this->insertData[$this->table->name][$headStr]['updateDublicate'] = "ON DUPLICATE KEY UPDATE $dublicateHeader";
    }



    private function runInsert()
    {
        $queryes = [];

        foreach ($this->insertData as $tableName => $headers) {
            foreach ($headers as $header => $values) {
                $query = "INSERT INTO $tableName ($header) VALUES \n";
                $valQr = [];
                foreach ($values['data'] as $value) {
                    $valQr[] = "($value)";
                }

                $queryes[] = "$query " . implode(", \n", $valQr) . " \n\n " . $values['updateDublicate'];
            }

            $queryes[] = '';
        }

        $this->output->run(implode("\n\n\n", $queryes));
    }
}
