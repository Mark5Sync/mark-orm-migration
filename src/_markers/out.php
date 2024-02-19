<?php
namespace markorm_migration\_markers;
use markdi\markdi;
use markorm_migration\out\Output;

/**
 * @property-read Output $output

*/
trait out {
    use markdi;

   function output(): Output { return new Output; }

}