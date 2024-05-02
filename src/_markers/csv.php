<?php
namespace markorm_migration\_markers;
use marksync\provider\provider;
use markorm_migration\csv\TablesSync;
use markorm_migration\csv\CsvTable;
use markorm_migration\csv\TablesMap;

/**
 * @property-read TablesSync $tablesSync
 * @property-read CsvTable $csvTable
 * @property-read TablesMap $tablesMap

*/
trait csv {
    use provider;

   function tablesSync(): TablesSync { return new TablesSync; }
   function csvTable(): CsvTable { return new CsvTable; }
   function tablesMap(): TablesMap { return new TablesMap; }

}