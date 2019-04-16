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

use Throwable;

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
        'from'     => null,
        'where'    => [],
        'orderBy'  => [],
        'limit'    => null,
        'into'     => null,
        'values'   => [],
        'update'   => null,
        'set'      => []

    ];

    /**
     * The query compiler.
     *
     * @var \Dionchaika\Database\Query\CompilerInterface
     */
    protected $compiler;

    /**
     * @param \Dionchaika\Database\Query\CompilerInterface $compiler
     */
    public function __construct(CompilerInterface $compiler)
    {
        $this->compiler = $compiler;
    }

    /**
     * Invoke a query
     * SELECT statement.
     *
     * @param mixed|null $columnNames
     * @return self
     * @throws \InvalidArgumentException
     */
    public function select($columnNames = null): self
    {
        $this->invokeStatement(self::STATEMENT_SELECT);

        if (null !== $columnNames) {
            $columnNames = is_array($columnNames)
                ? $columnNames
                : func_get_args();

            foreach ($columnNames as $columnName) {
                $this->parts['select'][]
                    = $this->compiler->compileName($columnName);
            }
        }

        return $this;
    }

    /**
     * Invoke a query
     * SELECT statement.
     *
     * @param string $expression
     * @return self
     */
    public function selectRaw(string $expression): self
    {
        $this->invokeStatement(self::STATEMENT_SELECT);
        $this->parts['select'][] = $expression;

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
     * @param mixed $tableName
     * @return self
     * @throws \InvalidArgumentException
     */
    public function from($tableName): self
    {
        $this->parts['from'] = $this->compiler->compileName($tableName);
        return $this;
    }

    /**
     * Set a query table.
     *
     * @param string $expression
     * @return self
     */
    public function fromRaw(string $expression): self
    {
        $this->parts['from'] = $expression;
        return $this;
    }

    /**
     * Add a query ORDER BY (ASC) clause.
     *
     * @param mixed $columnNames
     * @return self
     * @throws \InvalidArgumentException
     */
    public function orderBy($columnNames): self
    {
        $columnNames = is_array($columnNames)
            ? $columnNames
            : func_get_args();

        $this->parts['orderBy'][] = $this->compiler->compileOrderBy($columnNames, 'ASC');
        return $this;
    }

    /**
     * Add a query ORDER BY clause.
     *
     * @param string $expression
     * @return self
     */
    public function orderByRaw(string $expression): self
    {
        $this->parts['orderBy'][] = $expression;
        return $this;
    }

    /**
     * Add a query ORDER BY (DESC) clause.
     *
     * @param mixed $columnNames
     * @return self
     * @throws \InvalidArgumentException
     */
    public function orderByDesc($columnNames): self
    {
        $columnNames = is_array($columnNames)
            ? $columnNames
            : func_get_args();

        $this->parts['orderBy'][] = $this->compiler->compileOrderBy($columnNames, 'DESC');
        return $this;
    }

    /**
     * Set a query LIMIT clause.
     *
     * @param int      $count
     * @param int|null $offset
     * @return self
     * @throws \InvalidArgumentException
     */
    public function limit(int $count, ?int $offset = null): self
    {
        $this->parts['limit'] = $this->compiler->compileLimit($count, $offset);
        return $this;
    }

    /**
     * Set a query LIMIT clause.
     *
     * @param string $expression
     * @return self
     */
    public function limitRaw(string $expression): self
    {
        $this->parts['limit'] = $expression;
        return $this;
    }

    /**
     * Get the query SQL.
     *
     * @return string
     * @throws \InvalidArgumentException
     */
    public function getSql(): string
    {
        $sql = '';

        if ($this->statement === self::STATEMENT_SELECT) {
            $sql .= $this->compiler->compileSelect($this->parts);
        }

        return $sql;
    }

    /**
     * Get the string
     * representation of the query.
     *
     * @return string
     */
    public function __toString(): string
    {
        try {
            return $this->getSql();
        } catch (Throwable $e) {
            return '';
        }
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
        $this->parts['from']     = null;
        $this->parts['where']    = [];
        $this->parts['orderBy']  = [];
        $this->parts['limit']    = null;
        $this->parts['into']     = null;
        $this->parts['values']   = [];
        $this->parts['update']   = null;
        $this->parts['set']      = [];
    }
}
