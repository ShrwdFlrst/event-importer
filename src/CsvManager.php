<?php

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
        array_walk($csv, function(&$header) use ($csv) {
            // Get rid of junk that's not defined in the header
            $csv[0] = array_slice($csv[0], 0, count($header));
            $header = array_combine($csv[0], $header);
        });

        // Remove header
        array_shift($csv);

        return $csv;
    }
}