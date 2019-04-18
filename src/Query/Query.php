<?php

class Query
{
    const TYPE_SELECT = 0;
    const TYPE_INSERT = 1;
    const TYPE_UPDATE = 2;
    const TYPE_DELETE = 3;

    /**
     * @var int
     */
    protected $type = self::TYPE_SELECT;

    /**
     * @var mixed[]
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
     * @param mixed $columnNames
     * @return self
     */
    public function select($columnNames = '*'): self
    {
        $this->setType(self::TYPE_SELECT);

        $columnNames = is_array($columnNames)
            ? $columnNames
            : func_get_args();

        $this->parts['select']
            = array_map(['static', 'compileName'], $columnNames);

        return $this;
    }

    /**
     * @param string $expression
     * @return self
     */
    public function selectRaw(string $expression): self
    {
        $this->setType(self::TYPE_SELECT);
        $this->parts['select'] = $expression;

        return $this;
    }

    /**
     * @return self
     */
    public function distinct(): self
    {
        $this->parts['distinct'] = true;
        return $this;
    }

    /**
     * @param mixed      $columnName
     * @param mixed|null $aliasName
     * @return self
     */
    public function min($columnName = '*', $aliasName = null): self
    {
        $this->parts['select'][] = $this->compileAggregate('MIN', $columnName, $aliasName);
        return $this;
    }

    /**
     * @param mixed      $columnName
     * @param mixed|null $aliasName
     * @return self
     */
    public function max($columnName = '*', $aliasName = null): self
    {
        $this->parts['select'][] = $this->compileAggregate('MAX', $columnName, $aliasName);
        return $this;
    }

    /**
     * @param mixed      $columnName
     * @param mixed|null $aliasName
     * @return self
     */
    public function avg($columnName = '*', $aliasName = null): self
    {
        $this->parts['select'][] = $this->compileAggregate('AVG', $columnName, $aliasName);
        return $this;
    }

    /**
     * @param mixed      $columnName
     * @param mixed|null $aliasName
     * @return self
     */
    public function sum($columnName = '*', $aliasName = null): self
    {
        $this->parts['select'][] = $this->compileAggregate('SUM', $columnName, $aliasName);
        return $this;
    }

    /**
     * @param mixed      $columnName
     * @param mixed|null $aliasName
     * @return self
     */
    public function count($columnName = '*', $aliasName = null): self
    {
        $this->parts['select'][] = $this->compileAggregate('COUNT', $columnName, $aliasName);
        return $this;
    }

    /**
     * @param mixed $tableName
     * @return self
     */
    public function from($tableName): self
    {
        $this->parts['from'] = $this->compileName($tableName);
        return $this;
    }

    /**
     * @param string $expression
     * @return self
     */
    public function fromRaw(string $expression): self
    {
        $this->parts['from'] = $expression;
        return $this;
    }

    /**
     * @param string $expression
     * @param string $delimiter
     * @return self
     */
    public function whereRaw(string $expression, string $delimiter = 'AND'): self
    {
        if (!empty($this->parts['where'])) {
            $expression = $delimiter.' '.$expression;
        }

        $this->parts['where'][] = $expression;
        return $this;
    }

    /**
     * @param mixed[]|mixed $columnNames
     * @param string        $order
     * @return self
     */
    public function orderBy($columnNames, string $order = 'ASC'): self
    {
        $columnNames = is_array($columnNames)
            ? $columnNames
            : [$columnNames];

        $this->parts['orderBy'][]
            = implode(', ', array_map(['static', 'compileName'], $columnNames)).' '.$order;

        return $this;
    }

    /**
     * @param string $expression
     * @return self
     */
    public function orderByRaw(string $expression): self
    {
        $this->parts['orderBy'][] = $expression;
        return $this;
    }

    /**
     * @param mixed $columnNames
     * @return self
     */
    public function orderByAsc($columnNames): self
    {
        $columnNames = is_array($columnNames)
            ? $columnNames
            : func_get_args();

        return $this->orderBy($columnNames, 'ASC');
    }

    /**
     * @param mixed $columnNames
     * @return self
     */
    public function orderByDesc($columnNames): self
    {
        $columnNames = is_array($columnNames)
            ? $columnNames
            : func_get_args();

        return $this->orderBy($columnNames, 'DESC');
    }

    /**
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
     * @param string $expression
     * @return self
     */
    public function limitRaw(string $expression): self
    {
        $this->parts['limit'] = $expression;
        return $this;
    }

    /**
     * @param mixed $tableName
     * @return self
     */
    public function insert($tableName): self
    {
        $this->setType(self::TYPE_INSERT);
        $this->parts['into'] = $this->compileName($tableName);

        return $this;
    }

    /**
     * @param string $expression
     * @return self
     */
    public function insertRaw(string $expression): self
    {
        $this->setType(self::TYPE_INSERT);
        $this->parts['into'] = $expression;

        return $this;
    }

    /**
     * @param mixed[] $values
     * @return self
     */
    public function values(array $values): self
    {
        $this->parts['values'] = array_combine(
            array_map(['static', 'compileName'], array_keys($values)),
            array_map(['static', 'compileValue'], array_values($values))
        );

        return $this;
    }

    /**
     * @param string $expression
     * @return self
     */
    public function valuesRaw(string $expression): self
    {
        $this->parts['values'][] = $expression;
        return $this;
    }

    /**
     * @param mixed $tableName
     * @return self
     */
    public function update($tableName): self
    {
        $this->setType(self::TYPE_UPDATE);
        $this->parts['update'] = $this->compileName($tableName);

        return $this;
    }

    /**
     * @param string $expression
     * @return self
     */
    public function updateRaw(string $expression): self
    {
        $this->setType(self::TYPE_UPDATE);
        $this->parts['update'] = $expression;

        return $this;
    }

    /**
     * @param mixed[] $values
     * @return self
     */
    public function set(array $values): self
    {
        $this->parts['set'] = array_combine(
            array_map(['static', 'compileName'], array_keys($values)),
            array_map(['static', 'compileValue'], array_values($values))
        );

        return $this;
    }

    /**
     * @param string $expression
     * @return self
     */
    public function setRaw(string $expression): self
    {
        $this->parts['set'][] = $expression;
        return $this;
    }

    /**
     * @param mixed $tableName
     * @return self
     */
    public function delete($tableName): self
    {
        $this->setType(self::TYPE_DELETE);
        $this->parts['from'] = $this->compileName($tableName);

        return $this;
    }

    /**
     * @param string $expression
     * @return self
     */
    public function deleteRaw(string $expression): self
    {
        $this->setType(self::TYPE_UPDATE);
        $this->parts['from'] = $expression;

        return $this;
    }

    /**
     * @return string
     */
    public function getSql(): string
    {
        switch ($this->type) {
            case self::TYPE_SELECT:
                return $this->getSqlForSelect();
            case self::TYPE_INSERT:
                return $this->getSqlForInsert();
            case self::TYPE_UPDATE:
                return $this->getSqlForUpdate();
            case self::TYPE_DELETE:
                return $this->getSqlForDelete();
        }
    }

    /**
     * @param int $type
     * @return void
     */
    protected function setType(int $type): void
    {
        $this->type = $type;

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

    /**
     * @param string $name
     * @return string
     */
    protected function quoteName(string $name): string
    {
        return ('*' === $name)
            ? $name
            : '`'.str_replace('`', '\\`', $name).'`';
    }

    /**
     * @param string $string
     * @return string
     */
    protected function quoteString(string $string): string
    {
        return '\''.str_replace('\'', '\\\'', $string).'\'';
    }

    /**
     * @param mixed $name
     * @return string
     */
    protected function compileName($name): string
    {
        $name = (string)$name;

        [$name, $alias] = preg_split('/\s+as\s+/i', $name, 2);
        $name = implode('.', array_map(['static', 'quoteName'], preg_split('/\s*\.\s*/', $name, 3)));

        if (!empty($alias)) {
            $name .= ' AS '.$this->quoteName($alias);
        }

        return $name;
    }

    /**
     * @param mixed $value
     * @return string
     */
    protected function compileValue($value): string
    {
        if (null === $value) {
            return 'NULL';
        }

        if (true === $value) {
            return 'TRUE';
        }

        if (false === $value) {
            return 'FALSE';
        }

        if (is_int($value) || is_float($value)) {
            return $value;
        }

        $value = (string)$value;

        if ('?' === $value || 0 === strpos($value, ':')) {
            return $value;
        }

        return $this->quoteString($value);
    }

    /**
     * @param string     $name
     * @param mixed      $columnName
     * @param mixed|null $aliasName
     * @return string
     */
    protected function compileAggregate(string $name, $columnName, $aliasName = null): string
    {
        $aggregate = $name.'('.$this->compileName($columnName).')';
        if (null !== $aliasName) {
            $aggregate .= ' AS '.$this->compileName($aliasName);
        }

        return $aggregate;
    }
}
