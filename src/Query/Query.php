<?php

/**
 * The PHP Database Library.
 *
 * @package dionchaika/db
 * @version 1.0.0
 * @license MIT
 * @author Dion Chaika <dionchaika@gmail.com>
 */

namespace Dionchaika\Db\Query;

class Query
{
    /**
     * Normalize a query name.
     *
     * @param string $name
     * @return string
     */
    protected function normalizeName(string $name): string
    {
        return '`'.trim($name).'`';
    }
}
