<?php


namespace markorm_migration\migration_tools;

class RemoveFromDesc
{

    function removeFolders(string $path, $createAgain = false)
    {
        if (file_exists($path))
            if (PHP_OS === 'Windows') {
                exec(sprintf("rd /s /q %s", escapeshellarg($path)));
            } else {
                exec(sprintf("rm -rf %s", escapeshellarg($path)));
            }



        if ($createAgain)
            mkdir($path, 0777, true);
    }
}
