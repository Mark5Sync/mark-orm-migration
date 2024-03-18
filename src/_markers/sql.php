<?php
namespace markorm_migration\_markers;
use marksync\provider\provider;
use markorm_migration\sql\SQLTable;

/**
 * @property-read SQLTable $sQLTable

*/
trait sql {
    use provider;

   function sQLTable(): SQLTable { return new SQLTable; }

}