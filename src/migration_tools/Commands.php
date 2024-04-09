<?php

namespace markorm_migration\migration_tools;

class Commands
{

    private $list = [
        'c' => 'command',
        'r' => 'reference',
        'b' => 'backups',
        'n' => 'name',
        'y' => 'continue',
        'd' => 'deleteTablesBefore',
    ];


    public $command;
    public $reference;
    public $backups;
    public $name;
    public $continue;
    public $deleteTablesBefore;


    function __construct()
    {
        $options = getopt(...$this->getOptions());

        foreach ($this->list as $short => $command) {
            $this->{$command} = $options[$short] ?? $options[$command] ?? false;
        }
    }



    private function getOptions()
    {
        $short_options =  implode('', array_map(fn ($command) => "$command:", array_keys($this->list)));
        $long_options = array_map(fn ($command) => "$command:", $this->list);

        return [$short_options, $long_options];
    }
}
