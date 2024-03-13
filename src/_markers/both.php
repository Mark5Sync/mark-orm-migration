<?php
namespace markorm_migration\_markers;
use markdi\markdi;
use markorm_migration\both\Coll;
use markorm_migration\both\Header;

/**
 * @property-read Coll $coll
 * @property-read Header $header

*/
trait both {
    use markdi;

   function coll(): Coll { return new Coll; }
   function header(): Header { return new Header; }

}