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
    private $backupName    = false;




    final function __construct()
    {
        $this->connection->setPDO($this->getConnection());

        [
            'c' => $command,
            'r' => $reference,
            'b' => $backups,
            'n' => $backupName,
        ] = getopt("c:t:m:");


        if ($reference)  $this->referenceData = $reference;
        if ($backups)    $this->backupData = $backups;
        if ($backupName) $this->backupName = $backupName;


        if (!$command)
            die('Нужно указать команду для выполнения php ./migration.php -c [command]');


        if (method_exists($this, $command))
            return $this->{$command}();

        echo "undefined command: $command";
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
    }


    private function up()
    {
    }
}
