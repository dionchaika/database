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
     * @var \PDOStatement
     */
    protected $stmt;

    /**
     * @var bool
     */
    protected $prepared = false;

    /**
     * @param \PDO $pdo
     */
    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;

        $this->pdo->setAttribute(
            PDO::ATTR_PERSISTENT, true
        );

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
     * @param string $sql
     * @return void
     * @throws \Dionchaika\Database\QueryExceptionInterface
     */
    public function query(string $sql): void
    {
        try {
            $this->stmt = $this->pdo->query($sql);
            $this->prepared = false;
        } catch (PDOException $e) {
            throw new QueryException($e->getMessage());
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
            $this->prepared = true;
        } catch (PDOException $e) {
            throw new QueryException($e->getMessage());
        }
    }

    /**
     * @return bool
     */
    public function isPrepared(): bool
    {
        return $this->prepared;
    }

    /**
     * @param mixed[] $params
     * @return void
     * @throws \Dionchaika\Database\QueryExceptionInterface
     */
    public function execute(array $params = []): void
    {
        if (!$this->prepared) {
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
            throw new QueryException($e->getMessage());
        }
    }

    /**
     * @return mixed[]
     * @throws \Dionchaika\Database\FetchExceptionInterface
     */
    public function fetchAll(): array
    {
        if (!($this->stmt instanceof PDOStatement)) {
            return [];
        }

        try {
            return $this->stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new FetchException($e->getMessage());
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
