<?php

/**
 * Class DbManager
 */
class DbManager
{
    /** @var  PDO */
    private $connection;

    /**
     * DbManager constructor.
     * @param string $host
     * @param string $db
     * @param string $user
     * @param string $password
     */
    public function __construct(string $host, string $db, string $user, string $password)
    {
        $dsn = sprintf('mysql:host=%s;dbname=%s', $host, $db);
        $this->connection = new PDO($dsn, $user, $password);
    }

    /**
     * @return bool
     */
    public function beginTransaction()
    {
        return $this->connection->beginTransaction();
    }

    /**
     * @return bool
     */
    public function commitTransaction()
    {
        return $this->connection->commit();
    }

    /**
     * @param string $query
     * @return PDOStatement
     */
    public function prepare(string $query): PDOStatement
    {
        return $this->connection->prepare($query);
    }

    /**
     * @param string $query
     */
    public function query(string $query): void
    {
        $this->connection->exec($query);
    }

    /**
     * Close db connection
     */
    public function close(): void
    {
        $this->connection = null;
    }
}