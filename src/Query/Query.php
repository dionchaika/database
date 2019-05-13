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

use Dionchaika\Database\QueryException;
use Dionchaika\Database\ConnectionInterface;

class Query
{
    const TYPE_SELECT = 0;
    const TYPE_INSERT = 1;
    const TYPE_UPDATE = 2;
    const TYPE_DELETE = 3;

    const ORDER_ASC = 'ASC';
    const ORDER_DESC = 'DESC';

    const CRITERIA_CONTAINS = 0;
    const CRITERIA_ENDS_WITH = 1;
    const CRITERIA_STARTS_WITH = 2;

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
     * @var \Dionchaika\Database\ConnectionInterface|null
     */
    protected $connection;

    /**
     * @var mixed[]
     */
    protected $parameters = [];

    /**
     * @var int
     */
    protected $parameterNumber = 0;

    /**
     * @param \Dionchaika\Database\ConnectionInterface|null $connection
     */
    public function __construct(?ConnectionInterface $connection = null)
    {
        $this->connection = $connection;
    }

    /**
     * @return \Dionchaika\Database\ConnectionInterface|null
     */
    public function getConnection(): ?ConnectionInterface
    {
        return $this->connection;
    }

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
     * @param mixed      $columnName
     * @param mixed|null $operator
     * @param mixed|null $value
     * @param string     $delimiter
     * @return self
     */
    public function where($columnName, $operator = null, $value = null, string $delimiter = 'AND'): self
    {
        if (
            null === $operator &&
            null === $value
        ) {
            $operator = 'IS NOT';
        } else if (
            null !== $operator &&
            null === $value
        ) {
            $value = $operator;
            $operator = '=';
        } else if (
            null === $operator &&
            null !== $value
        ) {
            $operator = '=';
        }

        $condition = $this->compileName($columnName).' '.$operator.' '.$this->compileValue($value);
        if (!empty($this->parts['where'])) {
            $condition = $delimiter.' '.$condition;
        }

        $this->parts['where'][] = $condition;
        return $this;
    }

    /**
     * @param mixed      $columnName
     * @param mixed|null $operator
     * @param mixed|null $value
     * @return self
     */
    public function orWhere($columnName, $operator = null, $value = null): self
    {
        return $this->where($columnName, $operator, $value, 'OR');
    }

    /**
     * @param mixed      $columnName
     * @param mixed|null $operator
     * @param mixed|null $value
     * @return self
     */
    public function andWhere($columnName, $operator = null, $value = null): self
    {
        return $this->where($columnName, $operator, $value, 'AND');
    }

    /**
     * @param mixed  $columnName
     * @param string $delimiter
     * @return self
     */
    public function whereIsNull($columnName, string $delimiter = 'AND'): self
    {
        $condition = $this->compileName($columnName).' IS NULL';
        if (!empty($this->parts['where'])) {
            $condition = $delimiter.' '.$condition;
        }

        $this->parts['where'][] = $condition;
        return $this;
    }

    /**
     * @param mixed $columnName
     * @return self
     */
    public function orWhereIsNull($columnName): self
    {
        return $this->whereIsNull($columnName, 'OR');
    }

    /**
     * @param mixed $columnName
     * @return self
     */
    public function andWhereIsNull($columnName): self
    {
        return $this->whereIsNull($columnName, 'AND');
    }

    /**
     * @param mixed  $columnName
     * @param string $delimiter
     * @return self
     */
    public function whereIsNotNull($columnName, string $delimiter = 'AND'): self
    {
        $condition = $this->compileName($columnName).' IS NOT NULL';
        if (!empty($this->parts['where'])) {
            $condition = $delimiter.' '.$condition;
        }

        $this->parts['where'][] = $condition;
        return $this;
    }

    /**
     * @param mixed $columnName
     * @return self
     */
    public function orWhereIsNotNull($columnName): self
    {
        return $this->whereIsNotNull($columnName, 'OR');
    }

    /**
     * @param mixed $columnName
     * @return self
     */
    public function andWhereIsNotNull($columnName): self
    {
        return $this->whereIsNotNull($columnName, 'AND');
    }

    /**
     * @param mixed   $columnName
     * @param mixed[] $values
     * @param string  $delimiter
     * @return self
     */
    public function whereIn($columnName, array $values, string $delimiter = 'AND'): self
    {
        $condition = $this->compileName($columnName).' IN '
            .'('.implode(', ', array_map(['static', 'compileValue'], $values)).')';

        if (!empty($this->parts['where'])) {
            $condition = $delimiter.' '.$condition;
        }

        $this->parts['where'][] = $condition;
        return $this;
    }

    /**
     * @param mixed   $columnName
     * @param mixed[] $values
     * @return self
     */
    public function orWhereIn($columnName, array $values): self
    {
        return $this->whereIn($columnName, $values, 'OR');
    }

    /**
     * @param mixed   $columnName
     * @param mixed[] $values
     * @return self
     */
    public function andWhereIn($columnName, array $values): self
    {
        return $this->whereIn($columnName, $values, 'AND');
    }

    /**
     * @param mixed    $columnName
     * @param mixed    $value
     * @param int|null $criteria
     * @param string   $delimiter
     * @return self
     */
    public function whereLike($columnName, $value, ?int $criteria = null, string $delimiter = 'AND'): self
    {
        if (null !== $criteria) {
            switch ($criteria) {
                case self::CRITERIA_CONTAINS:
                    $value = '%'.$value.'%';
                    break;
                case self::CRITERIA_ENDS_WITH:
                    $value = '%'.$value;
                    break;
                case self::CRITERIA_STARTS_WITH:
                    $value = $value.'%';
                    break;
            }
        }

        $condition = $this->compileName($columnName).' LIKE '
            .$this->compileValue($value);

        if (!empty($this->parts['where'])) {
            $condition = $delimiter.' '.$condition;
        }

        $this->parts['where'][] = $condition;
        return $this;
    }

    /**
     * @param mixed    $columnName
     * @param mixed    $value
     * @param int|null $criteria
     * @return self
     */
    public function orWhereLike($columnName, $value, ?int $criteria = null): self
    {
        return $this->whereLike($columnName, $value, $criteria, 'OR');
    }

    /**
     * @param mixed    $columnName
     * @param mixed    $value
     * @param int|null $criteria
     * @return self
     */
    public function andWhereLike($columnName, $value, ?int $criteria = null): self
    {
        return $this->whereLike($columnName, $value, $criteria, 'AND');
    }

    /**
     * @param mixed  $columnName
     * @param mixed  $minValue
     * @param mixed  $maxValue
     * @param string $delimiter
     * @return self
     */
    public function whereBetween($columnName, $minValue, $maxValue, string $delimiter = 'AND'): self
    {
        $condition = $this->compileName($columnName).' BETWEEN '
            .$this->compileValue($minValue).' AND '.$this->compileValue($maxValue);

        if (!empty($this->parts['where'])) {
            $condition = $delimiter.' '.$condition;
        }

        $this->parts['where'][] = $condition;
        return $this;
    }

    /**
     * @param mixed $columnName
     * @param mixed $minValue
     * @param mixed $maxValue
     * @return self
     */
    public function orWhereBetween($columnName, $minValue, $maxValue): self
    {
        return $this->whereBetween($columnName, $minValue, $maxValue, 'OR');
    }

    /**
     * @param mixed $columnName
     * @param mixed $minValue
     * @param mixed $maxValue
     * @return self
     */
    public function andWhereBetween($columnName, $minValue, $maxValue): self
    {
        return $this->whereBetween($columnName, $minValue, $maxValue, 'AND');
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
     * @param string $expression
     * @return self
     */
    public function orWhereRaw(string $expression): self
    {
        return $this->whereRaw($expression, 'OR');
    }

    /**
     * @param string $expression
     * @return self
     */
    public function andWhereRaw(string $expression): self
    {
        return $this->whereRaw($expression, 'AND');
    }

    /**
     * @param mixed[]|mixed $columnNames
     * @param string        $order
     * @return self
     */
    public function orderBy($columnNames, string $order = self::ORDER_ASC): self
    {
        $columnNames = is_array($columnNames)
            ? $columnNames
            : [$columnNames];

        $this->parts['orderBy'][]
            = implode(', ', array_map(['static', 'compileName'], $columnNames)).' '.$order;

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

        return $this->orderBy($columnNames, self::ORDER_ASC);
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

        return $this->orderBy($columnNames, self::ORDER_DESC);
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
     * @return string
     */
    public function __toString(): string
    {
        return $this->getSql();
    }

    /**
     * @param mixed $value
     * @return self
     */
    public function setParameter(&$value): self
    {
        $this->parameters[$this->parameterNumber] = $value;
        ++$this->parameterNumber;

        return $this;
    }

    /**
     * @param int|string $parameter
     * @param mixed      $value
     * @return self
     */
    public function bindParameter($parameter, &$value): self
    {
        if (is_int($parameter)) {
            $this->parameters[$parameter] = $value;
        } else {
            $this->parameters[':'.ltrim($parameter, ':')] = $value;
        }

        return $this;
    }

    /**
     * @return \Dionchaika\Database\ConnectionInterface
     * @throws \Dionchaika\Database\QueryExceptionInterface
     */
    public function execute(): ConnectionInterface
    {
        if (null === $this->connection) {
            throw new QueryException(
                'Missing DB connection!'
            );
        }

        if (empty($this->parameters)) {
            $this->connection->query($this->getSql());
        } else {
            if (!$this->connection->isPrepared()) {
                $this->connection->prepare($this->getSql());
            }

            $this->connection->execute($this->parameters);
        }

        return $this->connection;
    }

    /**
     * @return mixed[]
     * @throws \Dionchaika\Database\QueryExceptionInterface
     * @throws \Dionchaika\Database\FetchExceptionInterface
     */
    public function all(): array
    {
        return $this->execute()->fetchAll();
    }

    /**
     * @return mixed[]|null
     * @throws \Dionchaika\Database\QueryExceptionInterface
     * @throws \Dionchaika\Database\FetchExceptionInterface
     */
    public function last(): ?array
    {
        return $this->execute()->fetchLast();
    }

    /**
     * @return mixed[]|null
     * @throws \Dionchaika\Database\QueryExceptionInterface
     * @throws \Dionchaika\Database\FetchExceptionInterface
     */
    public function first(): ?array
    {
        return $this->execute()->fetchFirst();
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

        if (preg_match('/\s+as\s+/i', $name)) {
            [$name, $alias] = array_filter(preg_split('/\s+as\s+/i', $name, 2));
        } else {
            $alias = null;
        }

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
            return (string)$value;
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

    /**
     * @return string
     */
    protected function getSqlForSelect(): string
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

    /**
     * @return string
     */
    protected function getSqlForInsert(): string
    {
        $sql = 'INSERT INTO '.$this->parts['into']
            .' ('.implode(', ', array_keys($this->parts['values'])).')'
            .' VALUES ('.implode(', ', array_values($this->parts['values'])).')';

        return $sql.';';
    }

    /**
     * @return string
     */
    protected function getSqlForUpdate(): string
    {
        if (empty($this->parts['set'])) {
            return '';
        }

        $set = [];
        foreach ($this->parts['set'] as $key => $value) {
            $set[] = $key.' = '.$value;
        }

        $sql = 'UPDATE '.$this->parts['update'].' SET '.implode(', ', $set);

        if (!empty($this->parts['where'])) {
            $sql .= ' WHERE '.implode(' ', $this->parts['where']);
        }

        return $sql.';';
    }

    /**
     * @return string
     */
    protected function getSqlForDelete(): string
    {
        $sql = 'DELETE FROM '.$this->parts['from'];

        if (!empty($this->parts['where'])) {
            $sql .= ' WHERE '.implode(' ', $this->parts['where']);
        }

        return $sql.';';
    }
}
