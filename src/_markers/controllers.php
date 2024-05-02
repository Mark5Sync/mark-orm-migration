<?php
namespace markorm_migration\_markers;
use marksync\provider\provider;
use markorm_migration\controllers\TableController;

/**
 * @property-read TableController $tableController

*/
trait controllers {
    use provider;

   function tableController(): TableController { return new TableController; }

}