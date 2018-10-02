<?php

namespace Src\Database;


interface AdapterInterface
{
    /**
     * Connect to database.
     * @param Instance $instanceConfig
     */
    public function connect($instanceConfig);

    /**
     * Execute query.
     * @param string $sql SQL query string
     * @param array $parameters
     *
     */
    public function query($sql, $parameters = []);

    /**
     * Fetch one row from result set.
     * @param string $sql SQL query string
     * @param array $parameters
     * @return array
     */
    public function fetch($sql, $parameters = []);

    /**
     * Fetch all rows from result set.
     * @param string $sql SQL query string
     * @param array $parameters
     * @return array
     */
    public function fetchAll($sql, $parameters = []);

    /**
     * Fetch only one requested column value from result set.
     * @param string $sql SQL query string
     * @param array $parameters
     * @return mixed
     */
    public function fetchColumn($sql, $parameters = []);

    /**
     * Fetch a two-column result into an array where the first column is a key and the second column
     * is the value.
     * @param string $sql SQL query string
     * @param array $parameters
     * @return array
     */
    public function fetchKeyValue($sql, $parameters = []);

}