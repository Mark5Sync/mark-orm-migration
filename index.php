<?php

use markorm_migration\_markers\connect;
use markorm_migration\csv\TablesSync;

require './vendor/autoload.php';


new class
{
    use connect;

    function __construct()
    {
        $this->setPDO();

        $sync = new TablesSync;
        $sync->from('./tables')->start();
    }


    function setPDO()
    {
        $server = '127.0.0.1';
        $port = '33060';
        $user = 'user';
        $password = '111';
        $database = 'migration-test';

        $dsn = "mysql:host=$server;port=$port;dbname=$database;";
        
        $this->connection->setPDO(new PDO($dsn, $user, $password));
    }
};
