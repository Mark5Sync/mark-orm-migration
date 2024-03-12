<?php
namespace markorm_migration\_markers;
use markdi\markdi;
use markorm_migration\migration_tools\Content;
use markorm_migration\migration_tools\Types;
use markorm_migration\migration_tools\RemoveFromDesc;
use markorm_migration\migration_tools\RelationShip;

/**
 * @property-read Content $content
 * @property-read Types $types
 * @property-read RemoveFromDesc $removeFromDesc
 * @property-read RelationShip $relationShip

*/
trait migration_tools {
    use markdi;

   function _content(): Content { return new Content($this); }
   function types(): Types { return new Types; }
   function removeFromDesc(): RemoveFromDesc { return new RemoveFromDesc; }
   function relationShip(): RelationShip { return new RelationShip; }

}