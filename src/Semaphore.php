<?php

class Semaphore
{
    /**
     * @var string
     */
    private $path;

    /**
     * Semaphore constructor.
     * @param string $path
     */
    public function __construct(string $path)
    {
        $this->path = $path;
    }

    /**
     * @return bool
     */
    public function get()
    {
        if ($this->check()) {
            file_put_contents($this->path, date('Y-m-d H:i:s'));

            return true;
        }

        return false;
    }

    /**
     * @return bool
     */
    public function release(): bool
    {
        if (is_file($this->path) && !$this->check()) {
            unlink($this->path);

            return true;
        }

        return false;
    }

    /**
     * @return bool
     */
    private function check(): bool
    {
        return !is_file($this->path) && !is_dir($this->path);
    }
}