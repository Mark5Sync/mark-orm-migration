<?php
namespace markorm_migration\_markers;
use marksync\provider\provider;
use markorm_migration\migration_tools\Content;
use markorm_migration\migration_tools\CompareRow;
use markorm_migration\migration_tools\Types;
use markorm_migration\migration_tools\RemoveFromDesc;
use markorm_migration\migration_tools\RelationShip;
use markorm_migration\migration_tools\Commands;

/**
 * @property-read Content $content
 * @property-read CompareRow $compareRow
 * @property-read Types $types
 * @property-read RemoveFromDesc $removeFromDesc
 * @property-read RelationShip $relationShip
 * @property-read Commands $commands

*/
trait migration_tools {
    use provider;

   function _content(): Content { return new Content($this); }
   function compareRow(): CompareRow { return new CompareRow; }
   function types(): Types { return new Types; }
   function removeFromDesc(): RemoveFromDesc { return new RemoveFromDesc; }
   function relationShip(): RelationShip { return new RelationShip; }
   function commands(): Commands { return new Commands; }

}