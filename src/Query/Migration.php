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

class Migration
{
    const TYPE_DROP_TABLE = 0;
    const TYPE_CREATE_TABLE = 1;
    const TYPE_DROP_DATABASE = 2;
    const TYPE_CREATE_DATABASE = 3;

    /**
     * @var int
     */
    protected $type = self::TYPE_DROP_TABLE;

    /**
     * @var mixed[]
     */
    protected $parts = [

        'drop_table'      => null,
        'if_exists'       => false,
        'create_table'    => null,
        'comment'         => null,
        'if_not_exists'   => false,
        'primary_key'     => [],
        'unique'          => [],
        'check'           => [],
        'engine'          => null,
        'drop_database'   => null,
        'create_database' => null,
        'character_set'   => null,
        'collate'         => null

    ];

    /**
     * @var mixed[]
     */
    protected $columns = [];

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
     * @param mixed $tableName
     * @return self
     */
    public function dropTable($tableName): self
    {
        $this->setType(self::TYPE_DROP_TABLE);

        $this->parts['drop_table']
            = $this->compileName($tableName);

        return $this;
    }

    /**
     * @param string $expression
     * @return self
     */
    public function dropTableRaw(string $expression): self
    {
        $this->setType(self::TYPE_DROP_TABLE);
        $this->parts['drop_table'] = $expression;

        return $this;
    }

    /**
     * @return self
     */
    public function ifExists(): self
    {
        $this->parts['if_exists'] = true;
        return $this;
    }

    /**
     * @param mixed      $tableName
     * @param mixed|null $comment
     * @return self
     */
    public function createTable($tableName, $comment = null): self
    {
        $this->setType(self::TYPE_CREATE_TABLE);

        $this->parts['create_table']
            = $this->compileName($tableName);

        if (null !== $comment) {
            $this->parts['comment']
                = $this->compileValue($comment);
        }

        return $this;
    }

    /**
     * @param string     $expression
     * @param mixed|null $comment
     * @return self
     */
    public function createTableRaw(string $expression, $comment = null): self
    {
        $this->setType(self::TYPE_CREATE_TABLE);
        $this->parts['create_table'] = $expression;

        if (null !== $comment) {
            $this->parts['comment']
                = $this->compileValue($comment);
        }

        return $this;
    }

    /**
     * @return self
     */
    public function ifNotExists(): self
    {
        $this->parts['if_not_exists'] = true;
        return $this;
    }

    /**
     * @param mixed $columnNames
     * @return self
     */
    public function primaryKey($columnNames): self
    {
        $columnNames = is_array($columnNames)
            ? $columnNames
            : func_get_args();

        $this->parts['primary_key'][]
            = implode(', ', array_map(['static', 'compileName'], $columnNames));

        return $this;
    }

    /**
     * @param string $expression
     * @return self
     */
    public function primaryKeyRaw(string $expression): self
    {
        $this->parts['primary_key'][] = $expression;
        return $this;
    }

    /**
     * @param mixed $columnNames
     * @return self
     */
    public function unique($columnNames): self
    {
        $columnNames = is_array($columnNames)
            ? $columnNames
            : func_get_args();

        $this->parts['unique'][]
            = implode(', ', array_map(['static', 'compileName'], $columnNames));

        return $this;
    }

    /**
     * @param string $expression
     * @return self
     */
    public function uniqueRaw(string $expression): self
    {
        $this->parts['unique'][] = $expression;
        return $this;
    }

    /**
     * @param string $expression
     * @return self
     */
    public function check(string $expression): self
    {
        $this->parts['check'][] = $expression;
        return $this;
    }

    /**
     * @param mixed $engine
     * @return self
     */
    public function engine($engine): self
    {
        $this->parts['engine'] = $this->compileValue($engine);
        return $this;
    }

    /**
     * @param mixed $columnName
     * @return self
     */
    public function column($columnName): self
    {
        $this->columns[] = [

            'name'           => $this->compileName($columnName),
            'data_type'      => null,
            'not_null'       => false,
            'default'        => null,
            'auto_increment' => false,
            'comment'        => null

        ];

        return $this;
    }

    /**
     * @param string $expression
     * @return self
     */
    public function columnRaw(string $expression): self
    {
        $this->columns[] = [

            'name'           => $expression,
            'data_type'      => '',
            'not_null'       => false,
            'default'        => null,
            'auto_increment' => false,
            'comment'        => null

        ];

        return $this;
    }

    /**
     * @param int|null $size
     * @param bool     $unsigned
     * @return self
     */
    public function int(?int $size = null, bool $unsigned = false): self
    {
        $this->columns[count($this->columns) - 1]['data_type'] = $this->compileIntegerDataType('INT', $size, $unsigned);
        return $this;
    }

    /**
     * @param int|null $size
     * @param bool     $unsigned
     * @return self
     */
    public function bigInt(?int $size = null, bool $unsigned = false): self
    {
        $this->columns[count($this->columns) - 1]['data_type'] = $this->compileIntegerDataType('BIGINT', $size, $unsigned);
        return $this;
    }

    /**
     * @param int|null $size
     * @param bool     $unsigned
     * @return self
     */
    public function tinyInt(?int $size = null, bool $unsigned = false): self
    {
        $this->columns[count($this->columns) - 1]['data_type'] = $this->compileIntegerDataType('TINYINT', $size, $unsigned);
        return $this;
    }

    /**
     * @param int|null $size
     * @param bool     $unsigned
     * @return self
     */
    public function smallInt(?int $size = null, bool $unsigned = false): self
    {
        $this->columns[count($this->columns) - 1]['data_type'] = $this->compileIntegerDataType('SMALLINT', $size, $unsigned);
        return $this;
    }

    /**
     * @param int|null $size
     * @param bool     $unsigned
     * @return self
     */
    public function mediumInt(?int $size = null, bool $unsigned = false): self
    {
        $this->columns[count($this->columns) - 1]['data_type'] = $this->compileIntegerDataType('MEDIUMINT', $size, $unsigned);
        return $this;
    }

    /**
     * @param int|null $size
     * @param int|null $digits
     * @return self
     */
    public function float(?int $size = null, ?int $digits  = null): self
    {
        $this->columns[count($this->columns) - 1]['data_type'] = $this->compileFloatDataType('FLOAT', $size, $digits);
        return $this;
    }

    /**
     * @param int|null $size
     * @param int|null $digits
     * @return self
     */
    public function double(?int $size = null, ?int $digits  = null): self
    {
        $this->columns[count($this->columns) - 1]['data_type'] = $this->compileFloatDataType('DOUBLE', $size, $digits);
        return $this;
    }

    /**
     * @param int|null $size
     * @param int|null $digits
     * @return self
     */
    public function decimal(?int $size = null, ?int $digits  = null): self
    {
        $this->columns[count($this->columns) - 1]['data_type'] = $this->compileFloatDataType('DECIMAL', $size, $digits);
        return $this;
    }

    /**
     * @return self
     */
    public function text(): self
    {
        $this->columns[count($this->columns) - 1]['data_type'] = 'TEXT';
        return $this;
    }

    /**
     * @return self
     */
    public function longText(): self
    {
        $this->columns[count($this->columns) - 1]['data_type'] = 'LONGTEXT';
        return $this;
    }

    /**
     * @return self
     */
    public function tinyText(): self
    {
        $this->columns[count($this->columns) - 1]['data_type'] = 'TINYTEXT';
        return $this;
    }

    /**
     * @return self
     */
    public function mediumText(): self
    {
        $this->columns[count($this->columns) - 1]['data_type'] = 'MEDIUMTEXT';
        return $this;
    }

    /**
     * @return self
     */
    public function blob(): self
    {
        $this->columns[count($this->columns) - 1]['data_type'] = 'BLOB';
        return $this;
    }

    /**
     * @return self
     */
    public function longBlob(): self
    {
        $this->columns[count($this->columns) - 1]['data_type'] = 'LONGBLOB';
        return $this;
    }

    /**
     * @return self
     */
    public function mediumBlob(): self
    {
        $this->columns[count($this->columns) - 1]['data_type'] = 'MEDIUMBLOB';
        return $this;
    }

    /**
     * @param int $size
     * @return self
     */
    public function char(int $size): self
    {
        $this->columns[count($this->columns) - 1]['data_type'] = 'CHAR('.$size.')';
        return $this;
    }

    /**
     * @param int $size
     * @return self
     */
    public function varchar(int $size): self
    {
        $this->columns[count($this->columns) - 1]['data_type'] = 'VARCHAR('.$size.')';
        return $this;
    }

    /**
     * @param mixed $values
     * @return self
     */
    public function set($values): self
    {
        $values = is_array($values)
            ? $values
            : func_get_args();

        $this->columns[count($this->columns) - 1]['data_type'] = $this->compileEnumerationDataType('SET', $values);
        return $this;
    }

    /**
     * @param mixed $values
     * @return self
     */
    public function enum($values): self
    {
        $values = is_array($values)
            ? $values
            : func_get_args();

        $this->columns[count($this->columns) - 1]['data_type'] = $this->compileEnumerationDataType('ENUM', $values);
        return $this;
    }

    /**
     * @param int|null $size
     * @return self
     */
    public function time(?int $size = null): self
    {
        $this->columns[count($this->columns) - 1]['data_type'] = $this->compileTimeDataType('TIME', $size);
        return $this;
    }

    /**
     * @param int|null $size
     * @return self
     */
    public function year(?int $size = null): self
    {
        $this->columns[count($this->columns) - 1]['data_type'] = $this->compileTimeDataType('YEAR', $size);
        return $this;
    }

    /**
     * @param int|null $size
     * @return self
     */
    public function date(?int $size = null): self
    {
        $this->columns[count($this->columns) - 1]['data_type'] = $this->compileTimeDataType('DATE', $size);
        return $this;
    }

    /**
     * @param int|null $size
     * @return self
     */
    public function datetime(?int $size = null): self
    {
        $this->columns[count($this->columns) - 1]['data_type'] = $this->compileTimeDataType('DATETIME', $size);
        return $this;
    }

    /**
     * @param int|null $size
     * @return self
     */
    public function timestamp(?int $size = null): self
    {
        $this->columns[count($this->columns) - 1]['data_type'] = $this->compileTimeDataType('TIMESTAMP', $size);
        return $this;
    }

    /**
     * @return self
     */
    public function notNull(): self
    {
        $this->columns[count($this->columns) - 1]['not_null'] = true;
        return $this;
    }

    /**
     * @param mixed $value
     * @return self
     */
    public function default($value): self
    {
        $this->columns[count($this->columns) - 1]['default'] = $this->compileValue($value);
        return $this;
    }

    /**
     * @param string $expression
     * @return self
     */
    public function defaultRaw(string $expression): self
    {
        $this->columns[count($this->columns) - 1]['default'] = $expression;
        return $this;
    }

    /**
     * @param int $startsWith
     * @return self
     */
    public function autoIncrement(int $startsWith = 1): self
    {
        if ($this->type === self::TYPE_CREATE_TABLE) {
            $this->columns[count($this->columns) - 1]['auto_increment'] = true;
        }

        return $this;
    }

    /**
     * @param mixed $comment
     * @return self
     */
    public function comment($comment): self
    {
        $this->columns[count($this->columns) - 1]['comment'] = $this->compileValue($comment);
        return $this;
    }

    /**
     * @param mixed $databaseName
     * @return self
     */
    public function dropDatabase($databaseName): self
    {
        $this->setType(self::TYPE_DROP_DATABASE);

        $this->parts['drop_database']
            = $this->compileName($databaseName);

        return $this;
    }

    /**
     * @param string $expression
     * @return self
     */
    public function dropDatabaseRaw(string $expression): self
    {
        $this->setType(self::TYPE_DROP_DATABASE);
        $this->parts['drop_database'] = $expression;

        return $this;
    }

    /**
     * @param mixed $databaseName
     * @return self
     */
    public function createDatabase($databaseName): self
    {
        $this->setType(self::TYPE_CREATE_DATABASE);

        $this->parts['create_database']
            = $this->compileName($databaseName);

        return $this;
    }

    /**
     * @param string $expression
     * @return self
     */
    public function createDatabaseRaw(string $expression): self
    {
        $this->setType(self::TYPE_CREATE_DATABASE);
        $this->parts['create_database'] = $expression;

        return $this;
    }

    /**
     * @param mixed $characterSet
     * @return self
     */
    public function characterSet($characterSet): self
    {
        $this->parts['character_set'] = $this->compileValue($characterSet);
        return $this;
    }

    /**
     * @param mixed $collate
     * @return self
     */
    public function collate($collate): self
    {
        $this->parts['collate'] = $this->compileValue($collate);
        return $this;
    }

    /**
     * @return string
     */
    public function getSql(): string
    {
        switch ($this->type) {
            case self::TYPE_DROP_TABLE:
                return $this->getSqlForDropTable();
            case self::TYPE_CREATE_TABLE:
                return $this->getSqlForCreateTable();
            case self::TYPE_DROP_DATABASE:
                return $this->getSqlForDropDatabase();
            case self::TYPE_CREATE_DATABASE:
                return $this->getSqlForCreateDatabase();
            default:
                return '';
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
     * @throws \Dionchaika\Database\QueryException
     */
    public function migrate(): ConnectionInterface
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
     * @param int $type
     * @return void
     */
    protected function setType(int $type): void
    {
        $this->type = $type;
        $this->columns = [];

        $this->parts['drop_table']      = null;
        $this->parts['if_exists']       = false;
        $this->parts['create_table']    = null;
        $this->parts['comment']         = null;
        $this->parts['if_not_exists']   = false;
        $this->parts['primary_key']     = [];
        $this->parts['unique']          = [];
        $this->parts['check']           = [];
        $this->parts['engine']          = null;
        $this->parts['drop_database']   = null;
        $this->parts['create_database'] = null;
        $this->parts['character_set']   = null;
        $this->parts['collate']         = null;
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
     * @param string   $name
     * @param int|null $size
     * @param bool     $unsigned
     * @return string
     */
    protected function compileIntegerDataType(string $name, ?int $size = null, bool $unsigned = false): string
    {
        $integerDataType = $name;
        if (null !== $size) {
            $integerDataType .= '('.$size.')';
        }

        if ($unsigned) {
            $integerDataType .= ' UNSIGNED';
        }

        return $integerDataType;
    }

    /**
     * @param string   $name
     * @param int|null $size
     * @param int|null $digits
     * @return string
     */
    protected function compileFloatDataType(string $name, ?int $size = null, ?int $digits  = null): string
    {
        $floatDataType = $name;
        if (null !== $size) {
            $floatDataType .= '('.$size;
            if (null !== $digits) {
                $floatDataType .= ', '.$digits;
            }

            $floatDataType .= ')';
        }

        return $floatDataType;
    }

    /**
     * @param string  $name
     * @param mixed[] $values
     * @return string
     */
    protected function compileEnumerationDataType(string $name, array $values): string
    {
        return $name.'('.implode(', ', array_map(['static', 'compileValue'], $values)).')';
    }

    /**
     * @param string   $name
     * @param int|null $size
     * @return string
     */
    protected function compileTimeDataType(string $name, ?int $size = null): string
    {
        $timeDataType = $name;
        if (null !== $size) {
            $timeDataType .= '('.$size.')';
        }

        return $timeDataType;
    }

    /**
     * @return string
     */
    protected function getSqlForDropTable(): string
    {
        $sql = ($this->parts['if_exists'] ? 'DROP TABLE IF EXISTS ' : 'DROP TABLE ')
            .$this->parts['drop_table'];

        return $sql.';';
    }

    /**
     * @return string
     */
    protected function getSqlForCreateTable(): string
    {
        if (empty($this->columns)) {
            return '';
        }

        $sql = ($this->parts['if_not_exists'] ? 'CREATE TABLE IF NOT EXISTS ' : 'CREATE TABLE ')
            .$this->parts['create_table'];

        $columns = [];

        foreach ($this->columns as $value) {
            if (null === $value['data_type']) {
                return '';
            }

            $column = $value['name'];
            if ('' !== $value['data_type']) {
                $column .= ' '.$value['data_type'];
            }

            if ($value['not_null']) {
                $column .= ' NOT NULL';
            }

            if ($value['auto_increment']) {
                $column .= ' AUTO_INCREMENT';
            } else if (null !== $value['default']) {
                $column .= ' DEFAULT '.$value['default'];
            }

            if (null !== $value['comment']) {
                $column .= ' COMMENT '.$value['comment'];
            }

            $columns[] = $column;
        }

        if (!empty($this->parts['primary_key'])) {
            foreach ($this->parts['primary_key'] as $primaryKey) {
                $columns[] = 'PRIMARY KEY ('.$primaryKey.')';
            }
        }

        if (!empty($this->parts['unique'])) {
            foreach ($this->parts['unique'] as $unique) {
                $columns[] = 'UNIQUE ('.$unique.')';
            }
        }

        if (!empty($this->parts['check'])) {
            foreach ($this->parts['check'] as $check) {
                $columns[] = 'CHECK ('.$check.')';
            }
        }

        $sql .= ' ('.implode(', ', $columns).')';

        if (null !== $this->parts['engine']) {
            $sql .= ' ENGINE = '.$this->parts['engine'];
        }

        if (null !== $this->parts['comment']) {
            $sql .= ' COMMENT = '.$this->parts['comment'];
        }

        return $sql.';';
    }

    /**
     * @return string
     */
    protected function getSqlForDropDatabase(): string
    {
        $sql = ($this->parts['if_exists'] ? 'DROP DATABASE IF EXISTS ' : 'DROP DATABASE ')
            .$this->parts['drop_database'];

        return $sql.';';
    }

    /**
     * @return string
     */
    protected function getSqlForCreateDatabase(): string
    {
        $sql = ($this->parts['if_not_exists'] ? 'CREATE DATABASE IF NOT EXISTS ' : 'CREATE DATABASE ')
            .$this->parts['create_database'];

        if (null !== $this->parts['character_set']) {
            $sql .= ' CHARACTER SET = '.$this->parts['character_set'];
        }

        if (null !== $this->parts['collate']) {
            $sql .= ' COLLATE = '.$this->parts['collate'];
        }

        return $sql.';';
    }
}
