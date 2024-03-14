<?php
namespace markorm_migration\_markers;
use markdi\markdi;
use markorm_migration\migration_connect\Connection;
use markorm_migration\migration_connect\Transaction;

/**
 * @property-read Connection $connection
 * @property-read Transaction $transaction

*/
trait migration_connect {
    use markdi;

   function connection(): Connection { return new Connection; }
   function transaction(): Transaction { return new Transaction; }

}