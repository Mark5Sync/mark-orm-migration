<?php
namespace markorm_migration\_markers;
use markdi\markdi;
use markorm_migration\connect\Connection;

/**
 * @property-read Connection $connection

*/
trait connect {
    use markdi;

   function connection(): Connection { return new Connection; }

}