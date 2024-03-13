<?php

namespace markorm_migration\sql;

use markorm_migration\_markers\controllers;
use markorm_migration\_markers\migration_connect;
use markorm_migration\_markers\migration_tools;
use markorm_migration\both\Header;
use markorm_migration\csv\CsvTable;

class SQLTable
{
    use migration_connect;
    use controllers;
    use migration_tools;

    public $colls;
    public Header $header;

    function __construct(public string $name, ?Header $header = null)
    {
        $this->colls = $this->tableController->getColls($this);

        if ($header)
            return $this->header = $header;

        $this->header = new Header();
        $this->header->initFromSql($this);
    }



    function saveToCsv(string $path)
    {
        $fileName = "$path/{$this->name}.csv";

        $table = new CsvTable($fileName, $this->header);

        $table->save(function(){
            foreach($this->for() as $row){
                yield $row;
            }
        });
    }



    public function for()
    {
        $smtp = $this->connection->query("SELECT * FROM {$this->name}");
        foreach ($smtp->fetchAll(\PDO::FETCH_ASSOC) as $index => $row) {
            yield $index => $row;
        }
    }
}
