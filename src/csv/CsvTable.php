<?php

namespace markorm_migration\csv;

use markdi\NotMark;
use markorm_migration\_markers\migration_tools;
use markorm_migration\both\Header;

#[NotMark]
class CsvTable
{
    use migration_tools;

    public readonly string $name;
    private $body = [];

    public $depends = [];
    public Header $header;

    private ?string $fileSaveAs = null;
    private $handle;

    function __construct(private string $csvFile, ?Header $header = null)
    {
        $info = pathinfo($csvFile);
        $this->name = $info['filename'];


        if ($header)
            return $this->header = $header;

        $this->header = new Header();
        $body = $this->header->initFromCsv($this);
        $this->body = $body;
    }


    function __destruct()
    {
        if ($this->handle)
            $this->close();
    }




    function open($rule = 'r')
    {
        $this->handle = fopen($this->fileSaveAs ? $this->fileSaveAs : $this->csvFile, $rule);

        if (!$this->handle)
            throw new \Exception("Невозможно прочитать файл ($this->csvFile)", 1);
    }


    function close()
    {
        fclose($this->handle);
        $this->handle = false;
        $this->fileSaveAs = false;
    }


    function read()
    {
        return fgetcsv($this->handle, 1000, ",");
    }

    function write(array $data)
    {
        fputcsv($this->handle, $data);
    }




    function getCreateStringHeader(): string
    {
        return implode(",\n\t", $this->header->getCollsSqlFormat());
    }




    function saveAs(string $saveAs)
    {
        $this->fileSaveAs = "$saveAs/{$this->name}.csv";
        $this->save();
    }


    function save()
    {
        $this->open('w');
        $this->whiteHeader();
        $this->writeBody();
        $this->close();
    }


    private function whiteHeader()
    {
        $headersRows = $this->header->getTranspose();

        foreach ($headersRows as $row) {
            $this->write($row);
        }

        $hr = array_fill(0, count($headersRows['field']), '---');
        $this->write($hr);


        return array_slice($headersRows['field'], 1);
    }


    private function writeBody()
    {
        if (!$this->handle)
            throw new \Exception("Файл закрыт для записи ($this->name)", 1);

        foreach ($this->body as $row) {
            $this->write([null, ...$row]);
        }
    }
}
