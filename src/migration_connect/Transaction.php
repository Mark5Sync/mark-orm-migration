<?php

namespace markorm_migration\migration_connect;

use markorm_migration\_markers\migration_connect;

class Transaction
{
    use migration_connect;


    function start()
    {
        $this->connection->getPdo()->beginTransaction();
        return $this;
    }

    private function inTransaction()
    {
        return $this->connection->getPdo()->inTransaction();
    }

    function rollBack()
    {
        if ($this->inTransaction())
            $this->connection->getPdo()->rollBack();
    }

    function commit()
    {
        if ($this->inTransaction())
            $this->connection->getPdo()->commit();
    }
}
