<?php
namespace markorm_migration\_markers;
use markdi\markdi;
use markorm_migration\Migrate;

/**
 * @property-read Migrate $migrate

*/
trait main {
    use markdi;

   function migrate(): Migrate { return new Migrate; }

}