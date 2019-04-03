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

use InvalidArgumentException;

class Select
{
    /**
     * The selecting rows.
     *
     * @var string
     */
    protected $rows;

    /**
     * The selecting table.
     *
     * @var string
     */
    protected $table;

    /**
     * The selection order.
     *
     * @var string|null
     */
    protected $order;

    /**
     * The selection condition.
     *
     * @var string|null
     */
    protected $condition;

    /**
     * @param array|mixed $rows
     */
    public function __construct($rows)
    {
        if ('*' !== $rows) {
            $rows = implode(', ', array_map(
                ['static', 'normalizeName'],
                is_array($rows) ? $rows : func_get_args()
            ));
        }

        $this->rows = $rows;
    }

    /**
     * Set a selecting table.
     *
     * @param string $table
     * @return \Dionchaika\Db\Query\Select
     */
    public function from(string $table): Select
    {
        $this->table = $this->normalizeName($table);
        return $this;
    }

    /**
     * Set a selection condition.
     *
     * @param array|mixed $condition
     * @return \Dionchaika\Db\Query\Select
     * @throws \InvalidArgumentException
     */
    public function where($condition): Select
    {
        $condition = is_array($condition) ? $condition : func_get_args();
        if (3 !== count($condition)) {
            throw new InvalidArgumentException(
                'Invalid selection condition!'
                .'Selection condition must contain three parameters:'
                .'first argument, operator string and a second argument.'
            );
        }

        $firstArgument = $condition[0];
        if (null === $firstArgument || 'null' === strtolower($firstArgument)) {
            $firstArgument = 'NULL';
        } else {
            $firstArgument = is_numeric($firstArgument)
                ? (string)$firstArgument
                : $this->normalizeName($firstArgument);
        }

        $operatorString = $condition[1];
        if (
            '=' !== $operatorString &&
            '>' !== $operatorString &&
            '<' !== $operatorString &&
            '>=' !== $operatorString &&
            '<=' !== $operatorString &&
            '<>' !== $operatorString &&
            'IN' !== $operatorString &&
            'LIKE' !== $operatorString &&
            'BETWEEN' !== $operatorString
        ) {
            throw new InvalidArgumentException(
                'Invalid operator string!'
                .'Valid operator strings: =, >, <, >=, <=, <>, IN, LIKE, BETWEEN.'
            );
        }

        $secondArgument = $condition[2];
        if (null === $secondArgument || 'null' === strtolower($secondArgument)) {
            $firstArgument = 'NULL';
        } else {
            $secondArgument = is_numeric($secondArgument)
                ? (string)$secondArgument
                : $this->normalizeName($secondArgument);
        }

        $this->condition .= $firstArgument.' '.$operatorString.' '.$secondArgument;

        return $this;
    }

    /**
     * Set OR selection condition.
     *
     * @param array|mixed $condition
     * @return \Dionchaika\Db\Query\Select
     * @throws \InvalidArgumentException
     */
    public function or($condition): Select
    {
        if (null !== $this->condition) {
            $this->condition .= ' OR ';
        }

        return $this->where(func_get_args());
    }

    /**
     * Set OR selection condition.
     *
     * An alias method name to or.
     *
     * @param array|mixed $condition
     * @return \Dionchaika\Db\Query\Select
     * @throws \InvalidArgumentException
     */
    public function orWhere($condition): Select
    {
        return $this->or(func_get_args());
    }

    /**
     * Set AND selection condition.
     *
     * @param array|mixed $condition
     * @return \Dionchaika\Db\Query\Select
     * @throws \InvalidArgumentException
     */
    public function and($condition): Select
    {
        if (null !== $this->condition) {
            $this->condition .= ' AND ';
        }

        return $this->where(func_get_args());
    }

    /**
     * Set AND selection condition.
     *
     * An alias method name to and.
     *
     * @param array|mixed $condition
     * @return \Dionchaika\Db\Query\Select
     * @throws \InvalidArgumentException
     */
    public function andWhere($condition): Select
    {
        return $this->and(func_get_args());
    }

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
