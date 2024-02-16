<?php

namespace markorm_migration\csv;

use markorm_migration\_markers\csv;
use markorm_migration\_markers\replace;

class Coll
{
    use replace;
    use csv;

    public Table $table;

    public string $field;
    public ?string $type = null;
    public bool $isNull = true;
    public string $key = '';
    public ?string $default = null;
    public string $extra = '';

    public ?string $relationTable;
    public ?string $relationColl;



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

        if ($this->checkString($value, 'AUTO_INCREMENT'))
            $this->extra = 'auto_increment';

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
            $this->setDefault($defaultValue);

            // Удаляю из $value
            $value = str_replace($defSearch, '', $value);
        }
    }


    function setDefault(string $value){
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
    ) {

        if ($type != $this->type || $isNull != $this->isNull || $extra != $this->extra)
            $this->changeColl->changeType();

        if ($key != $this->key)
            $this->changeColl->changeKey();

        if ($default != $this->default)
            $this->changeColl->changeDefault();
    }


    function createQuery()
    {
        $isNull = $this->isNull ? 'NULL' : 'NOT NULL';
        return "{$this->field} {$this->type} $isNull {$this->extra}";
    }


    function __toString()
    {
        return $this->createQuery();
    }
}
