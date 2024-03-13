<?php

namespace markorm_migration\controllers;

use markorm_migration\_markers\migration_connect;
use markorm_migration\_markers\migration_tools;
use markorm_migration\_markers\out;
use markorm_migration\csv\CsvTable;
use markorm_migration\sql\SQLTable;

class TableController
{
    use migration_connect;
    use migration_tools;
    use out;



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

        $csvTable->saveAs($saveToPath, function () use ($csvTable, $sqlTable) {
            if (!$sqlTable)
                throw new \Exception("csv таблица отсутствует", 1);

            foreach ($sqlTable->for() as $index => $sqlRow) {
                $csvRow = null;

                if (isset($sqlRow['id']))
                    $csvRow = $csvTable->findId($sqlRow['id']);
                else
                    echo "--\n";

                $merged = $this->compareRow->merge($csvTable->header->headerFields, $csvRow, $sqlRow);

                yield $merged;
            }
        });
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
    function create(SQLTable $table)
    {
        $strColls = implode(",\n\t", $table->header->getCollsSqlFormat());

        $query = "CREATE TABLE `$table->name` (\n\t$strColls\n)\n";

        $this->log->write("$table->name create table", $query);
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



    function upload(CsvTable $table)
    {
        $sqlTable = new SQLTable($table->name, $table->header);
        $this->create($sqlTable);

        $sqlTable->insertRows($table->header->headerFields, $table->body);

        $this->log->write("$table->name - insert data");
    }



    /** 
     * Удалить все таблицы
     */
    function removeAllTables()
    {
        $tables = array_keys($this->loadTables());
        if (empty($tables))
            return;

        $query = "DROP table " . implode(', ', $tables);
        $this->log->write("# Удаляю все таблицы", $query);
        $this->connection->query('SET FOREIGN_KEY_CHECKS = 0;');
        $this->connection->query($query);
        $this->connection->query('SET FOREIGN_KEY_CHECKS = 1;');
    }
}
