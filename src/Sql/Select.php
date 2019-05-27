<?php

/**
 * @license MIT
 * @version 1.0.0
 * @package lazy/db
 */

namespace Lazy\Db\Sql;

class Select
{
    /**
     * The SELECT statement
     * ORDER BY clause orders.
     */
    const ORDER_ASC = 'ASC';
    const ORDER_DESC = 'DESC';

    /**
     * The array
     * of SELECT statement parts.
     *
     * @var mixed[]
     */
    protected $parts = [

        'select'   => [],
        'distinct' => false,
        'from'     => null,
        'where'    => [],
        'orderBy'  => [],
        'limit'    => null

    ];

    /**
     * @param mixed $columnNames
     */
    public function __construct($columnNames = '*')
    {
        $columnNames = is_array($columnNames) ? $columnNames : func_get_args();
        $this->parts['select'] = array_map(['static', 'compileName'], $columnNames);
    }

    /**
     * Make the SELECT
     * statement distinct.
     *
     * @return self
     */
    public function distinct(): self
    {
        $this->parts['distinct'] = true;
        return $this;
    }

    /**
     * Set the SELECT
     * statement table.
     *
     * @param mixed $tableName
     * @return self
     */
    public function from($tableName): self
    {
        $this->parts['from'] = $this->compileName($tableName);
        return $this;
    }

    /**
     * Add the SELECT
     * statement ORDER BY clause.
     *
     * @param mixed[]|mixed $columnNames
     * @param string        $order
     * @return self
     */
    public function orderBy($columnNames, string $order = self::ORDER_ASC): self
    {
        $columnNames = is_array($columnNames) ? $columnNames : [$columnNames];
        $this->parts['orderBy'][] = implode(', ', array_map(['static', 'compileName'], $columnNames)).' '.$order;

        return $this;
    }

    /**
     * Add the SELECT
     * statement ORDER BY ASC clause.
     *
     * @param mixed $columnNames
     * @return self
     */
    public function orderByAsc($columnNames): self
    {
        $columnNames = is_array($columnNames) ? $columnNames : func_get_args();
        return $this->orderBy($columnNames, self::ORDER_ASC);
    }

    /**
     * Add the SELECT
     * statement ORDER BY DESC clause.
     *
     * @param mixed $columnNames
     * @return self
     */
    public function orderByDesc($columnNames): self
    {
        $columnNames = is_array($columnNames) ? $columnNames : func_get_args();
        return $this->orderBy($columnNames, self::ORDER_DESC);
    }

    /**
     * Set the SELECT
     * statement LIMIT clause.
     *
     * @param int      $count
     * @param int|null $offset
     * @return self
     */
    public function limit(int $count, ?int $offset = null): self
    {
        $this->parts['limit'] = (null === $offset) ? $count : $offset.', '.$count;
        return $this;
    }

    /**
     * Get the string
     * representation of the SELECT statement.
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->toSQL();
    }

    /**
     * Get the SELECT statement SQL.
     *
     * @return string
     */
    public function toSQL(): string
    {
        if (null === $this->parts['from']) {
            return '';
        }

        if (empty($this->parts['select'])) {
            $this->parts['select'][] = '*';
        }

        $sql = ($this->parts['distinct'] ? 'SELECT DISTINCT' : 'SELECT')
            .' '.implode(', ', $this->parts['select']).' FROM '.$this->parts['from'];

        if (!empty($this->parts['where'])) {
            $sql .= ' WHERE '.implode(' ', $this->parts['where']);
        }

        if (!empty($this->parts['orderBy'])) {
            $sql .= ' ORDER BY '.implode(', ', $this->parts['orderBy']);
        }

        if (null !== $this->parts['limit']) {
            $sql .= ' LIMIT '.$this->parts['limit'];
        }

        return $sql.';';
    }
}
