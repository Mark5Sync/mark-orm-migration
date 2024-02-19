<?php

namespace markorm_migration\migration_connect;

class Connection
{

    public $log = [];

    private \PDO $pdo;


    function setPdo(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }


    function query(string $query, array $data = []): \PDOStatement
    {
        $this->log[] = $query;

        $stmt = $this->pdo->prepare($query);
        $stmt->execute($data);

        return $stmt;
    }
}
