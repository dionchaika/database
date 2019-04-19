<?php

namespace Dionchaika\Database;

use PDO;
use PDOStatement;
use PDOException;
use Dionchaika\Database\QueryException;
use Dionchaika\Database\FetchException;

class Connection implements ConnectionInterface
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
     * @throws \Dionchaika\Database\QueryException
     */
    public function query(string $sql): void
    {
        try {
            $this->stmt = $this->pdo->query($sql);
        } catch (PDOException $e) {
            throw new QueryException($e);
        }
    }

    /**
     * @param string $sql
     * @return void
     * @throws \Dionchaika\Database\QueryException
     */
    public function prepare(string $sql): void
    {
        try {
            $this->stmt = $this->pdo->prepare($sql);
        } catch (PDOException $e) {
            throw new QueryException($e->getMessage());
        }
    }

    /**
     * @param mixed[] $params
     * @return void
     */
    public function execute(array $params = []): void
    {
        if (null === $this->stmt) {
            throw new QueryException(
                'Query is not being prepared before execution!'
            );
        }

        try {
            if (!empty($params)) {
                foreach ($params as $key => $value) {
                    if (is_int($key)) {
                        $this->stmt->bindParam($key + 1, $value);
                    } else {
                        $this->stmt->bindParam(':'.ltrim($key, ':'), $value);
                    }
                }
            }

            $this->stmt->execute();
        } catch (PDOException $e) {
            throw new QueryException($e->getMessage());
        }
    }

    /**
     * @return mixed[]
     * @throws \FetchException
     */
    public function fetchAll(): array
    {
        try {
            return $this->stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new FetchException($e->getMessage());
        }
    }

    /**
     * @return mixed[]
     * @throws \FetchException
     */
    public function fetchLast(): array
    {
        return @array_pop($this->fetchAll());
    }

    /**
     * @return mixed[]
     * @throws \FetchException
     */
    public function fetchFirst(): array
    {
        return @array_shift($this->fetchAll());
    }
}
