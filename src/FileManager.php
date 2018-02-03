<?php

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