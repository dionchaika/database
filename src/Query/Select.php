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

class Select extends Query
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
     */
    public function where($condition): Select
    {
        
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
