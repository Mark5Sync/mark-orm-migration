<?php
namespace markorm_migration\_markers;
use markdi\markdi;
use markorm_migration\csv\CsvTable;
use markorm_migration\csv\TablesMap;
use markorm_migration\csv\TablesSync;

/**
 * @property-read CsvTable $csvTable
 * @property-read TablesMap $tablesMap
 * @property-read TablesSync $tablesSync

*/
trait csv {
    use markdi;

   function csvTable(): CsvTable { return new CsvTable; }
   function tablesMap(): TablesMap { return new TablesMap; }
   function tablesSync(): TablesSync { return new TablesSync; }

}