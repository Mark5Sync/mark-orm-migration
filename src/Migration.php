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

    final function __construct()
    {
        $this->connection->setPDO($this->getConnection());
        $tables = $this->getTablesFolder();
        $migrations = $this->getMigrationsFolder();


        $command = getopt("c:t:o:");
        $command = $command['c'] ?? false;



        switch ($command) {
            case 'dump':
                $this->dump($tables);
                break;

            case 'diff':
                $this->diff($tables, $migrations);
                break;

            default:
                echo "undefined command: $command";
                break;
        }
    }


    private function dump()
    {
        $tables = $this->tableController->tables();

        
        print_r($tables);
    }


    private function diff($from, $to)
    {
        $sync = new TablesSync;
        $sync->from($from)->start($to);
    }
}
