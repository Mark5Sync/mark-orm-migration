<?php

namespace markorm_migration\csv;

use markorm_migration\_markers\controllers;
use markorm_migration\_markers\migration_connect;
use markorm_migration\_markers\migration_tools;
use markorm_migration\_markers\out;

class TablesMap
{
    use out;
    use migration_connect;
    use migration_tools;
    use controllers;


    private $tables = [];


    function __construct()
    {
        // $this->connection->query();
    }



    function add(Table $table)
    {
        $this->tables[] = $table;
    }


    function sync()
    {
        foreach ($this->tables as $table) {
            if (!$this->tableController->exists($table))
                $this->tableController->create($table);
            else
                $this->tableController->checkColls($table);
        }


        foreach ($this->tables as $table) {
            $table->content->compare();
        }

    }






}
