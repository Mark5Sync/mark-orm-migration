<?php

namespace markorm_migration\migration_tools;

use markorm_migration\_markers\migration_connect;

class RelationShip
{
    use migration_connect;

    public $tables = [];

    function __construct()
    {
        $this->tables = $this->getRelationship();
    }



    private function getRelationship()
    {
        $smtp = $this->connection->query("
            SELECT
                table_name as originTable,
                column_name as originColl,
                referenced_table_name as linkTable,
                referenced_column_name as linkColl
            FROM
                information_schema.key_column_usage

            WHERE referenced_table_name is not NULL
        ");

        $result = [];
        foreach ($smtp->fetchAll(\PDO::FETCH_ASSOC) as ['originTable' => $originTable, 'originColl' => $originColl, 'linkTable' => $table, 'linkColl' => $coll]) {
            $result[$originTable][$originColl] = ['table' => $table, 'coll' => $coll];
        }


        return $result;
    }
}
