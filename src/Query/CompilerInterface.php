<?php

/**
 * The PHP Database Library.
 *
 * @package dionchaika/database
 * @version 1.0.0
 * @license MIT
 * @author Dion Chaika <dionchaika@gmail.com>
 */

namespace Dionchaika\Database\Query;

interface CompilerInterface
{
    /**
     * Compile an SQL name.
     *
     * @param mixed $name
     * @return string
     * @throws \InvalidArgumentException
     */
    public function compileName($name): string;

    /**
     * Compile an SQL value.
     *
     * @param mixed $value
     * @return string
     * @throws \InvalidArgumentException
     */
    public function compileValue($value): string;

    /**
     * Compile an SQL ORDER BY clause.
     *
     * @param array  $columnNames
     * @param string $direction
     * @return string
     * @throws \InvalidArgumentException
     */
    public function compileOrderBy(array $columnNames, string $direction): string;

    /**
     * Compile an SQL LIMIT clause.
     *
     * @param int      $count
     * @param int|null $offset
     * @return string
     * @throws \InvalidArgumentException
     */
    public function compileLimit(int $count, ?int $offset = null): string;

    /**
     * Compile an SQL SELECT statement.
     *
     * @param array $parts
     * @return string
     * @throws \InvalidArgumentException
     */
    public function compileSelect(array $parts): string;
}
