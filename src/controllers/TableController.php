<?php

namespace markorm_migration\controllers;

use markorm_migration\_markers\migration_connect;
use markorm_migration\csv\Table;
use markorm_migration\sql\SQLTable;

class TableController
{
    use migration_connect;

    /**
     * @return SQLTable[]
     */
    function loadTables()
    {
        $query = "SHOW TABLES";
        $stmt = $this->connection->query($query);
        $tableNames = $stmt->fetchAll(\PDO::FETCH_COLUMN);

        $result = [];

        foreach ($tableNames as $tableName) {
            $result[] = new SQLTable($tableName);
        }

        return $result;
    }



    /** 
     * Проверить существование таблицы
     */
    function exists(Table $table)
    {
        $query = "SHOW TABLES LIKE '$table->name'";
        $stmt = $this->connection->query($query);
        $result = $stmt->fetch();

        return !!$result;
    }





    /**
     * Создать таблицу
     */
    function create(Table $table)
    {
        $strColls = $table->getCreateStringHeader();

        $query = "CREATE TABLE `$table->name` (\n\t$strColls\n)\n";
        $this->output->run($query);
    }



    /**
     * Сравнить две таблицы
     */
    function campare(SQLTable $sqlTable, Table $csvTable)
    {
        $colls = $this->getColls($sqlTable);
        // $table->compare($colls);
    }



    function getColls(SQLTable $table)
    {
        $query = "DESC `$table->name`";

        $stmt = $this->connection->query($query);
        $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);


        foreach ($result as &$coll) {
            if ($coll['Key'] == 'MUL')
                $coll['relation'] = $this->relationShip->tables[$table->name][$coll['Field']];
        }

        return $result;
    }



}
