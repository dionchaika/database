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
     * Make the select statement distinct.
     *
     * @return self
     */
    public function distinct(): self
    {
        $this->parts['distinct'] = true;
        return $this;
    }

    /**
     * Set the SELECT statement table.
     *
     * @param mixed $tableName
     * @return self
     */
    public function from($tableName): self
    {
        $this->parts['from'] = $this->compileName($tableName);
        return $this;
    }
}
