<?php
namespace markorm_migration\_markers;
use markdi\markdi;
use markorm_migration\sql\SQLTable;

/**
 * @property-read SQLTable $sQLTable

*/
trait sql {
    use markdi;

   function sQLTable(): SQLTable { return new SQLTable; }

}