<?php

$basePath = $argv[1] ?? "./";

/**
 * Class FileManager
 */
class FileManager
{
    /**
     * @param string $path
     * @return array
     */
    public function readDir(string $path): array {
        $filePattern = $path . "*";
        $files = [];

        foreach (glob($filePattern) as $filename) {
            $files[] = $filename;
        }

        return $files;
    }
}

/**
 * Class CsvManager
 */
class CsvManager
{
    /**
     * @param string $path
     * @return array
     * @see http://php.net/manual/en/function.str-getcsv.php#117692
     */
    public function readFile(string $path): array
    {
        $csv = array_map('str_getcsv', file($path));
        array_walk($csv, function(&$a) use ($csv) {
            $a = array_combine($csv[0], $a);
        });
        array_shift($csv);

        return $csv;
    }
}

$fileManager = new FileManager();
$csvManager = new CsvManager();
$files = $fileManager->readDir($basePath."uploaded/");

foreach($files as $f) {
    $csv = $csvManager->readFile($f);
    var_dump($csv);
}