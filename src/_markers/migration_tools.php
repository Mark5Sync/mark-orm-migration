<?php
namespace markorm_migration\_markers;
use marksync\provider\provider;
use markorm_migration\migration_tools\RemoveFromDesc;
use markorm_migration\migration_tools\Content;
use markorm_migration\migration_tools\Commands;
use markorm_migration\migration_tools\RelationShip;
use markorm_migration\migration_tools\Types;
use markorm_migration\migration_tools\CompareRow;

/**
 * @property-read RemoveFromDesc $removeFromDesc
 * @property-read Content $content
 * @property-read Commands $commands
 * @property-read RelationShip $relationShip
 * @property-read Types $types
 * @property-read CompareRow $compareRow

*/
trait migration_tools {
    use provider;

   function removeFromDesc(): RemoveFromDesc { return new RemoveFromDesc; }
   function _content(): Content { return new Content($this); }
   function commands(): Commands { return new Commands; }
   function relationShip(): RelationShip { return new RelationShip; }
   function types(): Types { return new Types; }
   function compareRow(): CompareRow { return new CompareRow; }

}