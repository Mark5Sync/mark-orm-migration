<?php

namespace markorm_migration\csv;

use markorm_migration\_markers\migration_connect;
use markorm_migration\_markers\migration_tools;
use markorm_migration\_markers\out;

class TablesMap
{
    use out;
    use migration_connect;
    use migration_tools;


    private $tables = [];


    function add(Table $table)
    {
        $this->tables[] = $table;
    }


    function sync()
    {
        foreach ($this->tables as $table) {
            if (!$this->tableExists($table))
                $this->create($table);
            else
                $this->checkColls($table);
        }
    }


    /** 
     * Проверить существование таблицы
     */
    function tableExists(Table $table)
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
