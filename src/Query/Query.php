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

class Query
{
    /**
     * The query
     * SELECT statement.
     */
    const STATEMENT_SELECT = 0;

    /**
     * The query
     * INSERT statement.
     */
    const STATEMENT_INSERT = 1;

    /**
     * The query
     * UPDATE statement.
     */
    const STATEMENT_UPDATE = 2;

    /**
     * The query
     * DELETE statement.
     */
    const STATEMENT_DELETE = 3;

    /**
     * The query statement.
     *
     * @var int
     */
    protected $statement = self::STATEMENT_SELECT;
}
