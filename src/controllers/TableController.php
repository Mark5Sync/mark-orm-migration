<?php

namespace markorm_migration\controllers;

use markorm_migration\_markers\migration_connect;
use markorm_migration\_markers\migration_tools;
use markorm_migration\csv\CsvTable;
use markorm_migration\sql\SQLTable;

class TableController
{
    use migration_connect;
    use migration_tools;




    /**
     * @return CsvTable[]
     */
    function referenceTables(string $referenceFolder)
    {
        $tableNames = array_diff(scandir($referenceFolder), ['.', '..']);

        $result = [];

        foreach ($tableNames as $tableName) {
            ['filename' => $filename] = pathinfo("$referenceFolder/$tableName");
            $csvTable = new CsvTable("$referenceFolder/$tableName");
            $result[$filename] = $csvTable;
        }

        return $result;
    }



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
            $result[$tableName] = new SQLTable($tableName);
        }

        return $result;
    }




    /**
     * Сравнить 2 таблицы
     */
    function compareAndSave(string $saveToPath, ?CsvTable $csvTable = null, ?SQLTable $sqlTable = null)
    {
        if (is_null($csvTable))
            throw new \Exception("csv таблица отсутствует", 1);

        $csvTable->saveAs($saveToPath);
        $colls = $csvTable->whiteHeader();



        if (is_null($sqlTable)) {
            $csvTable->writeBody();
            $csvTable->close();
            throw new \Exception("sql таблица отсутствует", 1);
        }



        foreach ($sqlTable->for() as $index => $row) {

        }


        $csvTable->close();

        return false;
    }



    /** 
     * Проверить существование таблицы
     */
    function exists(SqlTable $table)
    {
        $query = "SHOW TABLES LIKE '$table->name'";
        $stmt = $this->connection->query($query);
        $result = $stmt->fetch();

        return !!$result;
    }





    /**
     * Создать таблицу
     */
    function create(CsvTable $table)
    {
        $strColls = $table->getCreateStringHeader();

        $query = "CREATE TABLE `$table->name` (\n\t$strColls\n)\n";
        $this->output->run($query);
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




    function removeAllTables()
    {
        echo "REMOVE ALL TABLES";
    }
}
