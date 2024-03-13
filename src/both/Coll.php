<?php

namespace markorm_migration\both;

use markorm_migration\_markers\csv;
use markorm_migration\_markers\migration_tools;
use markorm_migration\_markers\out;

class Coll
{
    use out;
    use migration_tools;
    use csv;



    public string $field;
    public ?string $type = null;
    public bool $isNull = true;
    public string $key = '';
    public ?string $default = null;
    public string $extra = '';

    public ?string $relationTable = null;
    public ?string $relationColl = null;



    function __construct(string $field, string $type)
    {
        $this->field = $field;

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


    function set(string $type, string $value)
    {
        switch (strtolower($type)) {
            case '@null':
            case '@isnull':
                $this->isNull = in_array($value, ['yes', '1']);
                break;

            case '@key':
                $this->key = $value;
                break;

            case '@default':
                $this->default = $value == 'null' ? null : $value;
                break;

            case '@extra':
                $this->extra = $value;
                break;

            case '@relation':
            case '@relationtable':
                $this->relationTable = $value;
                break;

            case '@relationcoll':
                $this->relationColl = $value;
                break;

            default:
                throw new \Exception("Неизвестный тип [$type]", 447);
                break;
        }
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



    private function getSqlKey()
    {
        switch ($this->key) {
            case '':
                return '';

            case 'pri':
                return 'PRIMARY KEY';

            case 'uni':
                return 'UNIQUE';

            case 'mul':
                return '';

            default:
                throw new \Exception("Не знаю про такой KEY ($this->key)", 33);
        }
    }


    function toSql()
    {
        $isNull = $this->isNull ? 'DEFAULT NULL' : 'NOT NULL';

        $primKey = $this->getSqlKey();

        $relation = $this->relationTable ? "REFERENCES {$this->relationTable}({$this->relationColl})" : '';

        $result = "{$this->field} {$this->type} $isNull $primKey {$this->extra} $relation";
        return $result;
    }


    function __toString()
    {
        return $this->toSql();
    }
}
