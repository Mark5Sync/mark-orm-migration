<?php
namespace markorm_migration\_markers;
use marksync\provider\provider;
use markorm_migration\both\Coll;
use markorm_migration\both\Header;

/**
 * @property-read Coll $coll
 * @property-read Header $header

*/
trait both {
    use provider;

   function coll(): Coll { return new Coll; }
   function header(): Header { return new Header; }

}