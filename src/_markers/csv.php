<?php
namespace markorm_migration\_markers;
use markdi\markdi;
use markorm_migration\csv\ChangeColl;
use markorm_migration\csv\TablesMap;
use markorm_migration\csv\Coll;
use markorm_migration\csv\Table;
use markorm_migration\csv\TablesSync;

/**
 * @property-read ChangeColl $changeColl
 * @property-read TablesMap $tablesMap
 * @property-read Coll $coll
 * @property-read Table $table
 * @property-read TablesSync $tablesSync

*/
trait csv {
    use markdi;

   function _changeColl(): ChangeColl { return new ChangeColl($this); }
   function tablesMap(): TablesMap { return new TablesMap; }
   function coll(): Coll { return new Coll; }
   function table(): Table { return new Table; }
   function tablesSync(): TablesSync { return new TablesSync; }

}