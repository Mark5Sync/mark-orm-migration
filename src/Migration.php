<?php


namespace markorm_migration;

use markorm_migration\_interfaces\MigrationInterface;
use markorm_migration\_markers\controllers;
use markorm_migration\_markers\migration_connect;
use markorm_migration\csv\TablesSync;

abstract class Migration implements MigrationInterface
{
    use migration_connect;
    use controllers;

    public $tablesFolder = './tables';
    public $migrationsFolder = './migrations';

    final function __construct()
    {
        $this->connection->setPDO($this->getConnection());

        [
            'c' => $command,
            't' => $tables,
            'm' => $migratins,
        ] = getopt("c:t:m:");


        if ($tables) $this->tablesFolder = $tables;
        if ($migratins) $this->migrationsFolder = $migratins;

        if (!$command)
            die('Нужно указать команду для выполнения php ./migration.php -c [command]');


        if (method_exists($this, $command))
            return $this->{$command}();

        echo "undefined command: $command";
    }





    private function dump()
    {
        $tables = $this->tableController->loadTables();
        $naw = date("Y-m-d H:i:s");

        foreach ($tables as $table) {
            $table->saveToCsv("{$this->tablesFolder}/dump {$naw}/");
        }
    }


    private function up()
    {
        $sync = new TablesSync;
        $sync->from($this->tablesFolder)->start($this->migrationsFolder);
    }

}
