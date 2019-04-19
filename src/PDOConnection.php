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

use PDO;
use PDOStatement;
use PDOException;
use Dionchaika\Database\QueryException;
use Dionchaika\Database\FetchException;

class PDOConnection implements ConnectionInterface
{
    /**
     * @var \PDO
     */
    protected $pdo;

    /**
     * @var \PDOStatement|null
     */
    protected $stmt;

    /**
     * @param \PDO $pdo
     */
    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;

        $this->pdo->setAttribute(
            PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION
        );
    }

    /**
     * @return \PDO
     */
    public function getPDO(): PDO
    {
        return $this->pdo;
    }

    /**
     * @return \PDOStatement|null
     */
    public function getPDOStatement(): ?PDOStatement
    {
        return $this->stmt;
    }

    /**
     * @param string $sql
     * @return void
     * @throws \Dionchaika\Database\QueryExceptionInterface
     */
    public function query(string $sql): void
    {
        try {
            $this->stmt = $this->pdo->query($sql);
        } catch (PDOException $e) {
            throw new QueryException($e->getMessage(), $e->getCode(), $e->getPrevious());
        }
    }

    /**
     * @param string $sql
     * @return void
     * @throws \Dionchaika\Database\QueryExceptionInterface
     */
    public function prepare(string $sql): void
    {
        try {
            $this->stmt = $this->pdo->prepare($sql);
        } catch (PDOException $e) {
            throw new QueryException($e->getMessage(), $e->getCode(), $e->getPrevious());
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

        try {
            foreach ($params as $key => $value) {
                $type = PDO::PARAM_STR;

                if (is_int($value)) {
                    $type = PDO::PARAM_INT;
                } else if (is_null($value)) {
                    $type = PDO::PARAM_NULL;
                } else if (is_bool($value)) {
                    $type = PDO::PARAM_BOOL;
                }

                if (is_int($key)) {
                    $this->stmt->bindParam($key + 1, $value, $type);
                } else {
                    $this->stmt->bindParam(':'.ltrim($key, ':'), $value, $type);
                }
            }

            $this->stmt->execute();
        } catch (PDOException $e) {
            throw new QueryException($e->getMessage(), $e->getCode(), $e->getPrevious());
        }
    }

    /**
     * @return mixed[]|null
     * @throws \Dionchaika\Database\FetchExceptionInterface
     */
    public function fetchAll(): ?array
    {
        if (null === $this->stmt) {
            return null;
        }

        try {
            return $this->stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new FetchException($e->getMessage(), $e->getCode(), $e->getPrevious());
        }
    }

    /**
     * @return mixed[]|null
     * @throws \Dionchaika\Database\FetchExceptionInterface
     */
    public function fetchLast(): ?array
    {
        return @array_pop($this->fetchAll());
    }

    /**
     * @return mixed[]|null
     * @throws \Dionchaika\Database\FetchExceptionInterface
     */
    public function fetchFirst(): ?array
    {
        return @array_shift($this->fetchAll());
    }
}
