<?php

namespace Dionchaika\Database;

interface ConnectionInterface
{
    /**
     * Execute an SQL query.
     *
     * @param string $sql
     * @return void
     * @throws \Dionchaika\Database\QueryExceptionInterface
     */
    public function query(string $sql): void;

    /**
     * Prepare an SQL query.
     *
     * @param string $sql
     * @return void
     * @throws \Dionchaika\Database\QueryExceptionInterface
     */
    public function prepare(string $sql): void;

    /**
     * Execute a prepared SQL query.
     *
     * @param mixed[] $params
     * @return void
     * @throws \Dionchaika\Database\QueryExceptionInterface
     */
    public function execute(array $params = []): void;

    /**
     * Set a positional parameter.
     *
     * @param mixed $value
     * @return void
     */
    // public function setPositionalParameter(&$value): void;

    /**
     * Set a named parameter.
     *
     * @param string $name
     * @param mixed  $value
     * @return void
     */
    // public function setNamedParameter(string $name, &$value): void;

    /**
     * Fetch all rows as associative arrays.
     *
     * @return mixed[]
     * @throws \Dionchaika\Database\FetchExceptionInterface
     */
    public function fetchAll(): array;

    /**
     * Fetch last row as associative array.
     *
     * @return mixed[]
     * @throws \Dionchaika\Database\FetchExceptionInterface
     */
    public function fetchLast(): array;

    /**
     * Fetch first row as associative array.
     *
     * @return mixed[]
     * @throws \Dionchaika\Database\FetchExceptionInterface
     */
    public function fetchFirst(): array;
}
