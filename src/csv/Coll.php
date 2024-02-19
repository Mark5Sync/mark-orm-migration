<?php

namespace markorm_migration\csv;

use markorm_migration\_markers\csv;
use markorm_migration\_markers\migration_tools;
use markorm_migration\_markers\out;

class Coll
{
    use out;
    use migration_tools;
    use csv;

    public Table $table;

    public string $field;
    public ?string $type = null;
    public bool $isNull = true;
    public string $key = '';
    public ?string $default = null;
    public string $extra = '';

    public ?string $relationTable = null;
    public ?string $relationColl = null;



    function __construct(string $field, string $type, Table $table)
    {
        $this->field = $field;
        $this->table = $table;

        $this->auto($type);
    }



    function auto($value)
    {
        if ($type = $this->types->check($value)) {
            $value = $type;
        }

        if ($value == '')
            return;


        $this->checkReferences($value);
        $this->checkDefault($value);


        if ($this->checkString($value, 'PRIMARY KEY')) {
            $this->isNull = false;
            $this->key = 'PRI';
        }

        if ($this->checkString($value, 'AUTO_INCREMENT')) {
            $this->extra = 'auto_increment';
            $this->isNull = false;
            $this->key = 'PRI';
        }


        $value = trim($value);

        if ($value && !$this->type)
            $this->type = strtolower($value);
    }


    private function checkDefault(string &$value)
    {
        $re = '/default (\w+)/mi';

        preg_match_all($re, $value, $matches, PREG_SET_ORDER, 0);

        if (empty($matches))
            return;

        foreach ($matches as [$defSearch, $defaultValue]) {
            $this->setDefault($defaultValue == "NULL" ? null : $defaultValue);

            // Удаляю из $value
            $value = str_replace($defSearch, '', $value);
        }
    }


    function setDefault(?string $value)
    {
        $this->default = $value;
    }


    private function checkReferences(string $value)
    {
        $re = '/REFERENCES\s(\w+)\((\w+)\)/m';

        preg_match_all($re, $value, $matches, PREG_SET_ORDER, 0);

        if (empty($matches))
            return;

        foreach ($matches as [$_, $table, $coll]) {
            $this->createRelationShip($table, $coll);
        }
    }


    private function checkString(string &$value, $search)
    {
        if (!str_contains($value, $search))
            return;

        $value = str_replace($search, '', $value);
        return true;
    }


    function createRelationShip(string $table, string $coll)
    {
        $this->relationTable = $table;
        $this->relationColl = $coll;

        $this->table->addDepends($table);
    }


    function compare(
        string $type,
        bool $isNull = false,
        ?string $key = null,
        ?string $default = null,
        ?string $extra = null,
        $relation = null,
    ) {

        if ($type != $this->type || $isNull != $this->isNull || $extra != $this->extra)
            $this->changeColl->changeType();

        if ($key == 'MUL') {
            if ($relation['table'] != $this->relationTable || $relation['coll'] != $this->relationColl)
                $this->changeColl->changeKey();
        } else
            if ($key != $this->key)
                $this->changeColl->changeKey();

        if ($default != $this->default)
            $this->changeColl->changeDefault();
    }


    function create()
    {
        $collData = $this->createQuery();
        $query = "ALTER TABLE {$this->table->name} ADD $collData";

        $this->output->run($query);
    }


    function createQuery()
    {
        $isNull = $this->isNull ? 'DEFAULT NULL' : 'NOT NULL';

        $primKey = $this->key == 'PRI' ? "PRIMARY KEY" : '';

        $relation = $this->relationTable ? "REFERENCES {$this->relationTable}({$this->relationColl})" : '';

        $result = "{$this->field} {$this->type} $isNull $primKey {$this->extra} $relation";
        return $result;
    }


    function __toString()
    {
        return $this->createQuery();
    }
}
