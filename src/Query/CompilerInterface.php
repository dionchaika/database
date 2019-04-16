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
     */
    public function compileName($name): string;

    /**
     * Compile an SQL value.
     *
     * @param mixed $value
     * @return string
     */
    public function compileValue($value): string;

    /**
     * Compile an SQL LIMIT clause.
     *
     * @param int      $count
     * @param int|null $offset
     * @return string
     */
    public function compileLimit(int $count, ?int $offset = null): string;
}
