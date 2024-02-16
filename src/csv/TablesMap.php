<?php

namespace markorm_migration\csv;

use markorm_migration\_markers\connect;

class TablesMap
{
    use connect;
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

        $query = "CREATE TABLE `$table->name` ($strColls)";

        $this->connection->query($query);
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

        return $result;
    }
}
