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
     * The query SELECT type.
     */
    const TYPE_SELECT = 0;

    /**
     * The query INSERT type.
     */
    const TYPE_INSERT = 1;

    /**
     * The query UPDATE type.
     */
    const TYPE_UPDATE = 2;

    /**
     * The query DELETE type.
     */
    const TYPE_DELETE = 3;

    /**
     * The query compiler.
     *
     * @var \Dionchaika\Database\CompilerInterface
     */
    protected $compiler;

    /**
     * The query type.
     *
     * @var int
     */
    protected $type = self::TYPE_SELECT;

    /**
     * The array of query parts.
     *
     * @var array
     */
    protected $parts = [
        'select' => [],
        'distinct' => false,
        'from' => null,
        'where' => [],
        'orderBy' => [],
        'limit' => null,
        'into' => null,
        'values' => [],
        'update' => null,
        'set' => []
    ];

    /**
     * @param \Dionchaika\Database\CompilerInterface $compiler
     */
    public function __construct(CompilerInterface $compiler)
    {
        $this->compiler = $compiler;
    }

    /**
     * Invoke the query SELECT statement.
     *
     * @param mixed|null $columnNames
     * @return self
     */
    public function select($columnNames = null): self
    {
        $this->type = self::TYPE_SELECT;

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
     * Invoke the raw query SELECT statement.
     *
     * @param string $raw
     * @return self
     */
    public function selectRaw(string $raw): self
    {
        $this->type = self::TYPE_SELECT;
        $this->parts['select'][] = $raw;

        return $this;
    }

    /**
     * Make the query SELECT statement distinct.
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
     */
    public function from($tableName): self
    {
        $this->parts['from'] = $this->compiler->compileName($tableName);
        return $this;
    }

    /**
     * Set a raw query table.
     *
     * @param string $raw
     * @return self
     */
    public function fromRaw(string $raw): self
    {
        $this->parts['from'] = $raw;
        return $this;
    }

    /**
     * Add a query ORDER BY clause.
     *
     * @param mixed $columnNames
     * @return self
     */
    public function orderBy($columnNames): self
    {
        $columnNames = is_array($columnNames)
            ? $columnNames
            : func_get_args();

        $this->parts['orderBy'][] = $this->compileOrderBy($columnNames, 'ASC');
        return $this;
    }

    /**
     * Add a query ORDER BY DESC clause.
     *
     * @param mixed $columnNames
     * @return self
     */
    public function orderByDesc($columnNames): self
    {
        $columnNames = is_array($columnNames)
            ? $columnNames
            : func_get_args();

        $this->parts['orderBy'][] = $this->compileOrderBy($columnNames, 'DESC');
        return $this;
    }

    /**
     * Set a query LIMIT clause.
     *
     * @param int $count
     * @param int|null $offset
     * @return self
     */
    public function limit(int $count, ?int $offset = null): self
    {
        $this->parts['limit'] = $this->compiler->compileLimit($count, $offset);
        return $this;
    }

    /**
     * Get the query SQL.
     *
     * @return string
     */
    public function getSql(): string
    {
        $sql = '';

        if ($this->type === self::TYPE_SELECT) {
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
        return $this->getSql();
    }
}
