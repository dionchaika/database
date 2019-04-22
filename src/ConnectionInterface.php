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

interface ConnectionInterface
{
    /**
     * @param string $sql
     * @return void
     * @throws \Dionchaika\Database\QueryExceptionInterface
     */
    public function query(string $sql): void;

    /**
     * @param string $sql
     * @return void
     * @throws \Dionchaika\Database\QueryExceptionInterface
     */
    public function prepare(string $sql): void;

    /**
     * @return bool
     */
    public function isPrepared(): bool;

    /**
     * @param mixed[] $params
     * @return void
     * @throws \Dionchaika\Database\QueryExceptionInterface
     */
    public function execute(array $params = []): void;

    /**
     * @return mixed[]
     * @throws \Dionchaika\Database\FetchExceptionInterface
     */
    public function fetchAll(): array;

    /**
     * @return mixed[]|null
     * @throws \Dionchaika\Database\FetchExceptionInterface
     */
    public function fetchLast(): ?array;

    /**
     * @return mixed[]|null
     * @throws \Dionchaika\Database\FetchExceptionInterface
     */
    public function fetchFirst(): ?array;
}
