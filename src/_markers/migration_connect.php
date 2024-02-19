<?php
namespace markorm_migration\_markers;
use markdi\markdi;
use markorm_migration\migration_connect\Connection;

/**
 * @property-read Connection $connection

*/
trait migration_connect {
    use markdi;

   function connection(): Connection { return new Connection; }

}