<?php
namespace markorm_migration\_markers;
use markdi\markdi;
use markorm_migration\both\Coll;

/**
 * @property-read Coll $coll

*/
trait both {
    use markdi;

   function coll(): Coll { return new Coll; }

}