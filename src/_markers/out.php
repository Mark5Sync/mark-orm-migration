<?php
namespace markorm_migration\_markers;
use marksync\provider\provider;
use markorm_migration\out\Output;
use markorm_migration\out\Log;

/**
 * @property-read Output $output
 * @property-read Log $log

*/
trait out {
    use provider;

   function output(): Output { return new Output; }
   function log(): Log { return new Log; }

}