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

    /**
     * The array
     * of query parts.
     *
     * @var array
     */
    protected $parts = [

        'select'   => [],
        'distinct' => false,
        'from'     => ['db' => null, 'table' => null, 'alias' => null],
        'where'    => [],
        'orderBy'  => [],
        'limit'    => ['count' => null, 'offset' => null],
        'into'     => ['db' => null, 'table' => null, 'alias' => null],
        'values'   => [],
        'update'   => ['db' => null, 'table' => null, 'alias' => null],
        'set'      => []

    ];

    /**
     * Invoke a query
     * SELECT statement.
     *
     * @param mixed|null $columnNames
     * @return self
     */
    public function select($columnNames = null): self
    {
        $this->invokeStatement(self::STATEMENT_SELECT);

        if (null !== $columnNames) {
            $columnNames = is_array($columnNames)
                ? $columnNames
                : func_get_args();

            $this->parts['select'] = array_merge(
                $this->parts['select'], $columnNames
            );
        }

        return $this;
    }

    /**
     * Invoke a query
     * SELECT statement.
     *
     * @param mixed $expression
     * @return self
     */
    public function selectRaw($expression): self
    {
        $this->invokeStatement(self::STATEMENT_SELECT);
        $this->parts['select'][] = new Raw($expression);

        return $this;
    }

    /**
     * Make a query
     * SELECT statement distinct.
     *
     * @return self
     */
    public function distinct(): self
    {
        $this->parts['distinct'] = true;
        return $this;
    }

    /**
     * Set a query table.
     *
     * @param mixed      $tableName
     * @param mixed|null $aliasName
     * @param mixed|null $databaseName
     *
     * @return self
     */
    public function from($tableName, $aliasName = null, $databaseName = null): self
    {
        $this->parts['from']['db'] = $databaseName;
        $this->parts['from']['table'] = $tableName;
        $this->parts['from']['alias'] = $aliasName;

        return $this;
    }

    /**
     * Set a query table.
     *
     * @param mixed $expression
     * @return self
     */
    public function fromRaw($expression): self
    {
        $this->parts['from']['table'] = new Raw($expression);
        return $this;
    }

    /**
     * Invoke a query statement.
     *
     * @param int $statement
     * @return void
     */
    protected function invokeStatement(int $statement): void
    {
        $this->statement = $statement;

        $this->parts['select']   = [];
        $this->parts['distinct'] = false;
        $this->parts['from']     = ['db' => null, 'table' => null, 'alias' => null];
        $this->parts['where']    = [];
        $this->parts['orderBy']  = [];
        $this->parts['limit']    = ['count' => null, 'offset' => null];
        $this->parts['into']     = ['db' => null, 'table' => null, 'alias' => null];
        $this->parts['values']   = [];
        $this->parts['update']   = ['db' => null, 'table' => null, 'alias' => null];
        $this->parts['set']      = [];
    }
}
