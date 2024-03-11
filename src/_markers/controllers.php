<?php
namespace markorm_migration\_markers;
use markdi\markdi;
use markorm_migration\controllers\TableController;

/**
 * @property-read TableController $tableController

*/
trait controllers {
    use markdi;

   function tableController(): TableController { return new TableController; }

}