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

        'drop_table'    => null,
        'if_exists'     => false,
        'create_table'  => null,
        'if_not_exists' => false,
        'primary_key'   => []

    ];

    /**
     * @var mixed[]
     */
    protected $columns = [];

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
     * @param mixed $tableName
     * @return self
     */
    public function createTable($tableName): self
    {
        $this->setType(self::TYPE_CREATE_TABLE);

        $this->parts['create_table']
            = $this->compileName($tableName);

        return $this;
    }

    /**
     * @param string $expression
     * @return self
     */
    public function createTableRaw(string $expression): self
    {
        $this->setType(self::TYPE_CREATE_TABLE);
        $this->parts['create_table'] = $expression;

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

        $this->parts['primary_key']
            = array_map(['static', 'compileName'], $columnNames);

        return $this;
    }

    /**
     * @param string $expression
     * @return self
     */
    public function primaryKeyRaw(string $expression): self
    {
        $this->parts['primary_key'] = $expression;
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
            'null'           => false,
            'not_null'       => false,
            'default'        => null,
            'auto_increment' => false

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
            'null'           => false,
            'not_null'       => false,
            'default'        => null,
            'auto_increment' => false

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
     * @return self
     */
    public function time(): self
    {
        $this->columns[count($this->columns) - 1]['data_type'] = 'TIME()';
        return $this;
    }

    /**
     * @return self
     */
    public function year(): self
    {
        $this->columns[count($this->columns) - 1]['data_type'] = 'YEAR()';
        return $this;
    }

    /**
     * @return self
     */
    public function date(): self
    {
        $this->columns[count($this->columns) - 1]['data_type'] = 'DATE()';
        return $this;
    }

    /**
     * @return self
     */
    public function datetime(): self
    {
        $this->columns[count($this->columns) - 1]['data_type'] = 'DATETIME()';
        return $this;
    }

    /**
     * @return self
     */
    public function timestamp(): self
    {
        $this->columns[count($this->columns) - 1]['data_type'] = 'TIMESTAMP()';
        return $this;
    }

    /**
     * @return self
     */
    public function null(): self
    {
        $this->columns[count($this->columns) - 1]['null'] = true;
        $this->columns[count($this->columns) - 1]['not_null'] = false;

        return $this;
    }

    /**
     * @return self
     */
    public function notNull(): self
    {
        $this->columns[count($this->columns) - 1]['null'] = false;
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
     * @return self
     */
    public function autoIncrement(): self
    {
        if ($this->type === self::TYPE_CREATE_TABLE) {
            $this->columns[count($this->columns) - 1]['auto_increment'] = true;
        }

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
     * @param int $type
     * @return void
     */
    protected function setType(int $type): void
    {
        $this->type = $type;
        $this->columns = [];

        $this->migrationParts['drop_table']    = null;
        $this->migrationParts['if_exists']     = false;
        $this->migrationParts['create_table']  = null;
        $this->migrationParts['if_not_exists'] = false;
        $this->migrationParts['primary_key']   = null;
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
        $integerDataType = $name.'(';
        if (null !== $size) {
            $integerDataType .= $size;
            if ($unsigned) {
                $integerDataType .= ' UNSIGNED';
            }
        }

        return $integerDataType.')';
    }

    /**
     * @param string   $name
     * @param int|null $size
     * @param int|null $digits
     * @return string
     */
    protected function compileFloatDataType(string $name, ?int $size = null, ?int $digits  = null): string
    {
        $floatDataType = $name.'(';
        if (null !== $size) {
            $floatDataType .= $size;
            if (null !== $digits) {
                $floatDataType .= ', '.$digits;
            }
        }

        return $floatDataType.')';
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

            $column = $value['name'].(('' === $value['data_type']) ? '' : ' '.$value['data_type']);

            if ($value['null']) {
                $column .= ' NULL';
            } else if ($value['not_null']) {
                $column .= ' NOT NULL';
            }

            if (null !== $value['default']) {
                $column .= ' DEFAULT '.$value['default'];
            }

            if ($value['auto_increment']) {
                $column .= ' AUTO_INCREMENT';
            }

            $columns[] = $column;
        }

        if (null !== $this->parts['primary_key']) {
            $columns[] = 'PRIMARY KEY ('.implode(', ', $this->parts['primary_key']).')';
        }

        $sql .= ' ('.implode(', ', $columns).')';

        return $sql.';';
    }
}
