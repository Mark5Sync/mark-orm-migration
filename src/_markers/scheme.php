<?php
namespace markorm_migration\_markers;
use marksync\provider\provider;
use markorm_migration\scheme\SchemeConfig;
use markorm_migration\scheme\SchemeTypes;

/**
 * @property-read SchemeConfig $schemeConfig
 * @property-read SchemeTypes $schemeTypes

*/
trait scheme {
    use provider;

   function schemeConfig(): SchemeConfig { return new SchemeConfig; }
   function schemeTypes(): SchemeTypes { return new SchemeTypes; }

}