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

class RawSql
{
    /**
     * The raw SQL.
     *
     * @var string
     */
    protected $rawSql;

    /**
     * @param string $sql
     */
    public function __construct(string $rawSql)
    {
        $this->rawSql = $rawSql;
    }

    /**
     * Get the raw SQL.
     *
     * @return string
     */
    public function getRawSql(): string
    {
        return $this->rawSql;
    }

    /**
     * Get the string
     * representation of the raw SQL.
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->getRawSql();
    }
}
