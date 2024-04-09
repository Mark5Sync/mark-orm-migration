<?php


namespace markorm_migration;

use markorm_migration\_interfaces\MigrationInterface;
use markorm_migration\_markers\controllers;
use markorm_migration\_markers\migration_connect;
use markorm_migration\_markers\migration_tools;


abstract class
Migration implements MigrationInterface
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


        $pdo = $this->getConnection();
        $this->connection->setPDO($pdo);




        $this->transaction->start();

        try {
            if ($this->commands->deleteTablesBefore=='y')
                $this->tableController->removeAllTables();

            if (method_exists($this, $this->commands->command))
                return $this->{$this->commands->command}();

            $this->transaction->commit();
        } catch (\Throwable $th) {
            $this->transaction->rollBack();

            throw $th;
        }



        die("undefined command: {$this->commands->command}\n");
    }


    function createBackup(string $path)
    {
        $backupName = '.autosave/autosave_' . date("Y-m-d_H:i:s");
        $this->dump("$path/$backupName");
    }


    private function dump(?string $backupPath = null)
    {
        if (!$backupPath)
            if ($this->commands->continue != '1')
                if ($userInput = trim(strtolower(readline("Dump to $this->referenceData? [y|N]:"))))
                    if ($userInput != 'y')
                        return;


        $tables = $this->tableController->loadTables();
        if (empty($tables)) {
            if ($backupPath)
                return;
            exit("Таблицы отсутствуют\n");
        }


        $saveTo = $backupPath ? $backupPath : $this->referenceData;

        $this->removeFromDesc->removeFolders($saveTo, true);


        foreach ($tables as $table) {
            $table->saveToCsv($saveTo);
        }
    }



    private function backup()
    {
        if (!file_exists($this->referenceData))
            exit("Папка отсутствует $this->referenceData\n");

        $csvTables = $this->tableController->referenceTables($this->referenceData);
        $sqlTables = $this->tableController->loadTables();

        $backupName = 'backup_' . date("Y-m-d_H:i:s");


        if (!file_exists($this->backupData))
            mkdir($this->backupData, 0777, true);

        $realBackupDataPath = realpath($this->backupData);
        $backupPath = "$realBackupDataPath/$backupName";

        if (!file_exists($backupPath)) {
            mkdir($backupPath, 0777, true);

            $symlink = "$realBackupDataPath/current";
            if (file_exists($symlink) && linkinfo($symlink))
                unlink($symlink);

            symlink($backupPath, $symlink);
        }

        foreach (array_keys($csvTables) as $tableName) {
            try {
                $this->tableController->compareAndSave($backupPath, $csvTables[$tableName] ?? null, $sqlTables[$tableName] ?? null);
            } catch (\Throwable $th) {
                echo " - $tableName: {$th->getMessage()}\n";
            }
        }
    }


    private function testReference()
    {
        // $this->tableController->removeAllTables();

        try {
            $this->backup();
            $this->up('current');
        } catch (\Throwable $th) {
            throw $th;
        } finally {
            // $this->tableController->removeAllTables();
        }
    }


    private function up(?string $backupName = null)
    {
        if (!$this->backupName && !$backupName)
            die("Нужно указать backupName php migration -c up -n ... \n");

        $path = "$this->backupData/" . ($backupName ? $backupName : $this->backupName);

        if (!file_exists($path))
            die("$path - ненайден\n");


        $this->createBackup($this->backupData);


        $csvTables = $this->tableController->referenceTables($path);
        if (empty($csvTables))
            exit("Таблицы отсутствуют $path\n");

        $this->tableController->removeAllTables();


        foreach ($csvTables as $table) {
            try {
                $this->tableController->upload($table);
            } catch (\Throwable $th) {
                echo "Ошибка в таблице \"{$table->name}\" \n";
                throw $th;
            }
        }
    }


    private function createScheme()
    {
        $csvTables = $this->tableController->referenceTables($this->referenceData);


        $scheme = file_exists("$this->referenceData/scheme.json")
            ? json_decode(file_get_contents("$this->referenceData/scheme.json"), true)
            : ['tables' => []];


        $tables = [];
        foreach (array_values($csvTables) as $index => $table) {
            try {
                $schemeTable = current(array_filter($scheme['tables'], function ($itm) use ($table) {
                    return $table->name == $itm['name'];
                }));
                $colls = $table->header->exportColls();


                $tables[] = [
                    'name' => $table->name,
                    'position' => $schemeTable ? $schemeTable['position'] : [0, $index * 300],
                    'colls' => $colls,
                    'test' => $table->body,
                ];
            } catch (\Throwable $th) {
                echo " - {$table->name}: {$th->getMessage()}\n";
            }
        }

        file_put_contents("$this->referenceData/scheme.json", json_encode(['tables' => $tables]));
    }
}
