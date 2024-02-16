<?php

namespace markorm_migration\csv;

use markdi\Mark;
use markorm_migration\_markers\connect;

#[Mark(mode: Mark::LOCAL, args: ['parent'])]
class ChangeColl
{
    use connect;

    function __construct(private Coll $coll)
    {
    }


    function changeType()
    {
        echo "change type in coll {$this->coll->field} \n";

        $collStr = $this->coll->createQuery();

        $sql = "ALTER TABLE 
            `{$this->coll->table->name}`
            MODIFY COLUMN $collStr 
        ";

        $this->connection->query("/* CHANGE TYPE */\n$sql");
    }



    function changeKey()
    {
        echo "change primarykey in coll {$this->coll->field} \n";


        $sql = "ALTER TABLE 
            `{$this->coll->table->name}`
            DROP PRIMARY KEY
        ";

        $this->connection->query("/* -- Сначала удаляем текущий первичный ключ */\n$sql");



        $sql = "ALTER TABLE 
            `{$this->coll->table->name}`
            ADD PRIMARY KEY `{$this->coll->field}`
        ";

        $this->connection->query("/* -- Затем добавляем новый первичный ключ */\n$sql");
    }


    function changeDefault()
    {
        $sql = "ALTER TABLE 
        `{$this->coll->table->name}`
        ALTER COLUMN `{$this->coll->field}` SET DEFAULT :def";

        $this->connection->query(
            "/* -- Затем добавляем новый первичный ключ */\n$sql",
            ['def' => $this->default],
        );
    }
}
