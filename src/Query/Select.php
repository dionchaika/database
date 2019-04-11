<?php

namespace Dionchaika\Database\Query;

use Dionchaika\Database\ConnectorInterface;

class Select
{
    use QuoteTrait;

    /**
     * @var array
     */
    protected $parts = [
        'select' => null,
        'distinct' => false,
        'from' => null,
        'orderBy' => null,
        'limit' => null
    ];

    /**
     * @var Dionchaika\Database\ConnectorInterface
     */
    protected $connector;

    /**
     * @param \Dionchaika\Database\Query\Raw|array|mixed $columns
     */
    public function __construct($columnNames = null)
    {
        if (null !== $columnNames) {
            if ($columnNames instanceof Raw) {
                $this->parts['select'] = $columnNames;
            } else {
                $columnNames = is_array($columnNames)
                    ? $columnNames
                    : func_get_args();

                $this->parts['select'] = [];
                foreach ($columnNames as $key => $value) {
                    if (is_int($key)) {
                        $this->parts['select'][] = $this->quoteName($value);
                    } else {
                        $this->parts['select'][]
                            = $this->quoteName($key).' AS '.$this->quoteName($value);
                    }
                }
            }
        }
    }

    /**
     * @param \Dionchaika\Database\ConnectorInterface $connector
     * @return $this
     */
    public function setConnector(ConnectorInterface $connector): Select
    {
        $this->connector = $connector;
        return $this;
    }

    /**
     * @return $this
     */
    public function distinct(): Select
    {
        $this->parts['distinct'] = true;
        return $this;
    }

    /**
     * @return $this
     */
    public function all(): Select
    {
        $this->parts['select'] = new Raw('*');
        return $this;
    }

    /**
     * @param \Dionchaika\Database\Query\Raw|mixed $columnName
     * @param mixed|null $aliasName
     * @param bool $distinct
     * @return $this
     */
    public function min($columnName = '*', $aliasName = null, $distinct = false): Select
    {
        $min = $this->createAggregate('MIN', $columnName, $aliasName, $distinct);
        if ($this->parts['select'] instanceof Raw) {
            $this->parts['select']->append(', '.$min);
        } else {
            $this->parts['select'][] = $min;
        }

        return $this;
    }

    /**
     * @param \Dionchaika\Database\Query\Raw|mixed $columnName
     * @param mixed|null $aliasName
     * @param bool $distinct
     * @return $this
     */
    public function max($columnName = '*', $aliasName = null, $distinct = false): Select
    {
        $max = $this->createAggregate('MAX', $columnName, $aliasName, $distinct);
        if ($this->parts['select'] instanceof Raw) {
            $this->parts['select']->append(', '.$max);
        } else {
            $this->parts['select'][] = $max;
        }

        return $this;
    }

    /**
     * @param \Dionchaika\Database\Query\Raw|mixed $columnName
     * @param mixed|null $aliasName
     * @param bool $distinct
     * @return $this
     */
    public function avg($columnName = '*', $aliasName = null, $distinct = false): Select
    {
        $avg = $this->createAggregate('AVG', $columnName, $aliasName, $distinct);
        if ($this->parts['select'] instanceof Raw) {
            $this->parts['select']->append(', '.$avg);
        } else {
            $this->parts['select'][] = $avg;
        }

        return $this;
    }

    /**
     * @param \Dionchaika\Database\Query\Raw|mixed $columnName
     * @param mixed|null $aliasName
     * @param bool $distinct
     * @return $this
     */
    public function sum($columnName = '*', $aliasName = null, $distinct = false): Select
    {
        $sum = $this->createAggregate('SUM', $columnName, $aliasName, $distinct);
        if ($this->parts['select'] instanceof Raw) {
            $this->parts['select']->append(', '.$sum);
        } else {
            $this->parts['select'][] = $sum;
        }

        return $this;
    }

    /**
     * @param \Dionchaika\Database\Query\Raw|mixed $columnName
     * @param mixed|null $aliasName
     * @param bool $distinct
     * @return $this
     */
    public function count($columnName = '*', $aliasName = null, $distinct = false): Select
    {
        $count = $this->createAggregate('COUNT', $columnName, $aliasName, $distinct);
        if ($this->parts['select'] instanceof Raw) {
            $this->parts['select']->append(', '.$count);
        } else {
            $this->parts['select'][] = $count;
        }

        return $this;
    }

    /**
     * @param \Dionchaika\Database\Query\Raw|mixed $tableName
     * @param mixed|null $aliasName
     * @return $this
     */
    public function from($tableName, $aliasName = null): Select
    {
        if ($tableName instanceof Raw) {
            $this->parts['from'] = $tableName;
        } else {
            $this->parts['from'] = $this->quoteName($tableName);
            if (null !== $aliasName) {
                $this->parts['from'] .= ' AS '.$this->quoteName($aliasName);
            }
        }

        return $this;
    }

    /**
     * @param string $name
     * @param \Dionchaika\Database\Query\Raw|mixed $columnName
     * @param mixed|null $aliasName
     * @param bool $distinct
     * @return string
     */
    protected function createAggregate(string $name, $columnName, $aliasName = null, $distinct = false): string
    {
        $aggregate = $name.'(';

        if ($columnName instanceof Raw) {
            return $aggregate.(string)$columnName.')';
        }

        if ($distinct) {
            $aggregate .= 'DISTINCT ';
        }

        $aggregate .= $this->quoteName($columnName).')';
        if (null !== $aliasName) {
            $aggregate .= ' AS '.$this->quoteName($aliasName);
        }

        return $aggregate;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        if (null === $this->parts['from']) {
            return '';
        }

        if (null === $this->parts['select']) {
            $this->parts['select'] = new Raw('*');
        }

        $query = 'SELECT';

        if ($this->parts['select'] instanceof Raw) {
            $query .= ' '.(string)$this->parts['select'];
        } else {
            if ($this->parts['distinct']) {
                $query .= ' DISTINCT';
            }

            $query .= ' '.implode(', ', $this->parts['select']);
        }

        $query .= ' FROM ';

        if ($this->parts['from'] instanceof Raw) {
            $query .= (string)$this->parts['from'];
        } else {
            $query .= $this->parts['from'];
        }

        return $query.';';
    }
}
