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
            ['filename' => $filename, 'extension' => $extension] = pathinfo("$referenceFolder/$tableName");
            if ($extension != 'csv')
                continue;

            $csvTable = new CsvTable("$referenceFolder/$tableName");
            $result[$csvTable->name] = $csvTable;
        }

        $result = $this->relationSort($result);

        return $result;
    }



    /** 
     * @var CsvTable[] $tables
     * @return CsvTable[]
     */
    private function relationSort(array $tables)
    {
        $result = [];
        $iteration = count($tables) ** 2;


        while (!empty($tables)) {
            $iteration--;

            if ($iteration < 0) {
                print_r(array_keys($tables));
                echo "  |  |  |  |  |  |  |\n";
                echo "  V  V  V  V  V  V  V\n";
                print_r(array_keys($result));
                echo "---------------------\n";

                throw new \Exception("Не получается отсортировать таблицы, проверьте связи", 1);
            }


            /** @var CsvTable $table */
            foreach ($tables as $table) {
                $push = true;

                foreach ($table->header->relationsTables as $relationTableName) {
                    if (!isset($result[$relationTableName])) {
                        $push = false;
                        break;
                    }
                }

                if (!$push)
                    continue;

                unset($tables[$table->name]);
                $result[$table->name] = $table;
            }


            echo ".";
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
            $exceptionsId = [];

            foreach ($csvTable->body as $row) {
                if (isset($row['id'])){
                    $exceptionsId[$row['id']] = true;
                } else {
                    echo " -- {$csvTable->name}.csv - отсутствует столбец id\n";
                }
                yield $row;
            }

            if (!$sqlTable)
                return;

            $this->log->write("$csvTable->name - сравниваю таблицы");

            foreach ($sqlTable->for() as $index => $sqlRow) {
                $csvRow = null;

                if (isset($sqlRow['id'])){
                    if (isset($exceptionsId[$sqlRow['id']]))
                        continue;
                } else echo " -- {$csvTable->name}.sql - отсутствует столбец id\n";

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
            if (isset($this->relationShip->tables[$table->name][$coll['Field']])) {
                $relation = $this->relationShip->tables[$table->name][$coll['Field']];

                $coll['Relation'] = $relation;
            } else {
                $coll['Relation'] = null;
            }
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
