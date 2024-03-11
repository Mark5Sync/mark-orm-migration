<?php

namespace markorm_migration\controllers;

use markorm_migration\_markers\migration_connect;
use markorm_migration\csv\Table;

class TableController
{
    use migration_connect;



    function tables()
    {
        $query = "SHOW TABLES";
        $stmt = $this->connection->query($query);
        $result = $stmt->fetchAll(\PDO::FETCH_COLUMN);

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
     * Проверить столбцы, если столбцы отсутствуют - будут созданы новые
     */
    function checkColls(Table $table)
    {
        $colls = $this->getCollsFromTable($table);
        $table->compare($colls);
    }






    private function getCollsFromTable(Table $table)
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
