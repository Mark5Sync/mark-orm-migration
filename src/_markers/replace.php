<?php
namespace markorm_migration\_markers;
use markdi\markdi;
use markorm_migration\replace\Types;

/**
 * @property-read Types $types

*/
trait replace {
    use markdi;

   function types(): Types { return new Types; }

}