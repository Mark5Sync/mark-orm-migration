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

    public  $referenceData = './referenceData';
    public  $backupData    = './backupData';
    public  $logs          = './logs';

    private $backupName    = false;



    final function __construct()
    {
        $this->connection->setPDO($this->getConnection());

        [
            'c' => $command,
            'r' => $reference,
            'b' => $backups,
            'n' => $backupName,
        ] = [
            'c' => false, 
            'r' => false, 
            'b' => false,
            'n' => false,
            ...getopt("c:t:m:")
        ];


        if ($reference)  $this->referenceData = $reference;
        if ($backups)    $this->backupData = $backups;
        if ($backupName) $this->backupName = $backupName;


        if (!$command)
            die("Нужно указать команду для выполнения php ./migration.php -c [command]\n");


        if (method_exists($this, $command))
            return $this->{$command}();

        die("undefined command: $command\n");
    }



    private function dump()
    {
        $userInput = trim(strtolower(readline("Dump to $this->referenceData? [y|N]:")));

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

        $backupName = 'backup ' . date("Y-m-d H:i:s");
        $backupPath = "$this->backupData/$backupName";

        if (!file_exists($backupPath))
            mkdir($backupPath, 0777, true);

        foreach (array_keys($csvTables) as $tableName) {
            try {
                $this->tableController->compareAndSave($backupPath, $csvTables[$tableName], $sqlTables[$tableName]);
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
            $this->tableController->create($table);
        }
    }
}
