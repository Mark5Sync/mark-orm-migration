<?php


namespace markorm_migration;

use markorm_migration\_interfaces\MigrationInterface;
use markorm_migration\_markers\controllers;
use markorm_migration\_markers\migration_connect;
use markorm_migration\_markers\migration_tools;


abstract class Migration implements MigrationInterface
{
    use migration_connect;
    use controllers;
    use migration_tools;


    public $referenceData = './referenceData';
    public $backupData    = './backupData';
    public $logs          = './logs';

    private $backupName    = false;



    final function __construct()
    {
        if (!$this->commands->command)
            die("Нужно указать команду для выполнения php ./migration.php -c [command]\n");

        if ($ref = $this->commands->reference)
            $this->referenceData = $ref;

        if ($back = $this->commands->backups)
            $this->backupData = $back;

        $this->backupName = $this->commands->name;



        $this->connection->setPDO($this->getConnection());




        $this->transaction->start();

        try {
            if (method_exists($this, $this->commands->command))
                return $this->{$this->commands->command}();

            $this->transaction->commit();
        } catch (\Throwable $th) {
            $this->transaction->rollBack();

            throw $th;
        }



        die("undefined command: {$this->commands->command}\n");
    }



    private function dump()
    {
        if ($this->commands->continue != '1')
            if ($userInput = trim(strtolower(readline("Dump to $this->referenceData? [y|N]:"))))
                if ($userInput != 'y')
                    return;


        $tables = $this->tableController->loadTables();
        if (empty($tables))
            exit("Таблицы отсутствуют\n");


        $this->removeFromDesc->removeFolders($this->referenceData, true);


        foreach ($tables as $table) {
            $table->saveToCsv($this->referenceData);
        }
    }



    private function backup()
    {
        if (!file_exists($this->referenceData))
            exit("Папка отсутствует $this->referenceData\n");

        $csvTables = $this->tableController->referenceTables($this->referenceData);
        $sqlTables = $this->tableController->loadTables();

        $backupName = 'backup_' . date("Y-m-d_H:i:s");
        $backupPath = "$this->backupData/$backupName";

        if (!file_exists($backupPath))
            mkdir($backupPath, 0777, true);

        foreach (array_keys($csvTables) as $tableName) {
            try {
                $this->tableController->compareAndSave($backupPath, $csvTables[$tableName] ?? null, $sqlTables[$tableName] ?? null);
            } catch (\Throwable $th) {
                echo " - $tableName: {$th->getMessage()}\n";
            }
        }
    }





    private function up()
    {
        if (!$this->backupName)
            die("Нужно указать backupName php migration -c up -n ... \n");

        $path = "$this->backupData/$this->backupName";

        if (!file_exists($path))
            die("$this->backupName - ненайден\n");





        $csvTables = $this->tableController->referenceTables($path);
        if (empty($csvTables))
            exit("Таблицы отсутствуют $path\n");

        $this->tableController->removeAllTables();


        foreach ($csvTables as $table) {
            $this->tableController->upload($table);
        }
    }
}
