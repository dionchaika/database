<?php

/**
 * The PHP Database Library.
 *
 * @package dionchaika/database
 * @version 1.0.0
 * @license MIT
 * @author Dion Chaika <dionchaika@gmail.com>
 */

namespace Dionchaika\Database;

use mysqli;
use mysqli_stmt;
use mysqli_result;
use Dionchaika\Database\QueryException;

class MySQLiConnection implements ConnectionInterface
{
    /**
     * @var \mysqli
     */
    protected $mysqli;

    /**
     * @var \mysqli_stmt|null
     */
    protected $stmt;

    /**
     * @var \mysqli_result|null
     */
    protected $result;

    /**
     * @param \mysqli $mysqli
     */
    public function __construct(mysqli $mysqli)
    {
        $this->mysqli = $mysqli;
    }

    /**
     * @return \mysqli
     */
    public function getMySQLi(): mysqli
    {
        return $this->mysqli;
    }

    /**
     * @return \mysqli_stmt|null
     */
    public function getStmt(): ?mysqli_stmt
    {
        return $this->stmt;
    }

    /**
     * @return \mysqli_result|null
     */
    public function getResult(): ?mysqli_result
    {
        return $this->result;
    }

    /**
     * @param string $sql
     * @return void
     * @throws \Dionchaika\Database\QueryExceptionInterface
     */
    public function query(string $sql): void
    {
        $this->result = $this->mysqli->query($sql);
        if (false === $this->result) {
            throw new QueryException($this->mysqli->error);
        }
    }

    /**
     * @param string $sql
     * @return void
     * @throws \Dionchaika\Database\QueryExceptionInterface
     */
    public function prepare(string $sql): void
    {
        $this->stmt = $this->mysqli->prepare($sql);
        if (false === $this->result) {
            throw new QueryException($this->mysqli->error);
        }
    }

    /**
     * @param mixed[] $params
     * @return void
     * @throws \Dionchaika\Database\QueryExceptionInterface
     */
    public function execute(array $params = []): void
    {
        if (null === $this->stmt) {
            throw new QueryException(
                'Query is not being prepared before execution!'
            );
        }

        if (!empty($params)) {
            $types = '';
            foreach ($params as $param) {
                if (is_int($param)) {
                    $types .= 'i';
                } else if (is_float($param)) {
                    $types .= 'd';
                } else {
                    $types .= 's';
                }
            }

            if (false === $this->stmt->bind_param($types, ...$params)) {
                throw new QueryException($this->mysqli->error);
            }
        }

        if (false === $this->stmt->execute()) {
            throw new QueryException($this->mysqli->error);
        }

        $this->result = $this->stmt->get_result();
        if (false === $this->result) {
            throw new QueryException($this->mysqli->error);
        }
    }

    /**
     * @return mixed[]|null
     */
    public function fetchAll(): ?array
    {
        if (null === $this->result || is_bool($this->result)) {
            return null;
        }

        return $this->result->fetch_all(\MYSQLI_ASSOC);
    }

    /**
     * @return mixed[]|null
     */
    public function fetchLast(): ?array
    {
        return @array_pop($this->fetchAll());
    }

    /**
     * @return mixed[]|null
     */
    public function fetchFirst(): ?array
    {
        return @array_shift($this->fetchAll());
    }
}
