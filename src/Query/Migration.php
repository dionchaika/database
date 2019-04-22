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

class Migration extends Query
{
    const TYPE_DROP_TABLE = 0;
    const TYPE_CREATE_TABLE = 1;
    const TYPE_DROP_DATABASE = 2;
    const TYPE_CREATE_DATABASE = 3;

    /**
     * @var mixed[]
     */
    protected $migrationParts = [

        'drop_table'    => null,
        'if_exists'     => false,
        'create_table'  => null,
        'if_not_exists' => false,
        'columns'       => []

    ];

    /**
     * @param mixed $tableName
     * @return self
     */
    public function dropTable($tableName): self
    {
        $this->setType(self::TYPE_DROP_TABLE);

        $this->migrationParts['drop_table']
            = $this->compileName($tableName);

        return $this;
    }

    /**
     * @return self
     */
    public function ifExists(): self
    {
        $this->migrationParts['if_exists'] = true;
        return $this;
    }

    /**
     * @param mixed $tableName
     * @return self
     */
    public function createTable($tableName): self
    {
        $this->setType(self::TYPE_CREATE_TABLE);

        $this->migrationParts['create_table']
            = $this->compileName($tableName);

        return $this;
    }

    /**
     * @return self
     */
    public function ifNotExists(): self
    {
        $this->migrationParts['if_not_exists'] = true;
        return $this;
    }

    /**
     * @param mixed  $columnName
     * @param string $dataType
     * @return self
     */
    public function column($columnName, string $dataType = 'INT()'): self
    {
        $this->migrationParts['columns'][] = [

            'name'      => $this->compileName($columnName),
            'data_type' => $dataType

        ];

        return $this;
    }

    /**
     * @param int|null  $size
     * @param bool      $unsigned
     * @return self
     */
    public function int(?int $size = null, bool $unsigned = false): self
    {
        $dataType = 'INT(';
        if (null !== $size) {
            $dataType .= $size;
            if ($unsigned) {
                $dataType .= ' UNSIGNED';
            }
        }

        $dataType .= ')';

        $this->migrationParts['columns'][count($this->migrationParts['columns']) - 1]['data_type']
            = $dataType;

        return $this;
    }

    /**
     * @param int|null  $size
     * @param bool      $unsigned
     * @return self
     */
    public function bigInt(?int $size = null, bool $unsigned = false): self
    {
        $dataType = 'BIGINT(';
        if (null !== $size) {
            $dataType .= $size;
            if ($unsigned) {
                $dataType .= ' UNSIGNED';
            }
        }

        $dataType .= ')';

        $this->migrationParts['columns'][count($this->migrationParts['columns']) - 1]['data_type']
            = $dataType;

        return $this;
    }

    /**
     * @param int|null  $size
     * @param bool      $unsigned
     * @return self
     */
    public function tinyInt(?int $size = null, bool $unsigned = false): self
    {
        $dataType = 'TINYINT(';
        if (null !== $size) {
            $dataType .= $size;
            if ($unsigned) {
                $dataType .= ' UNSIGNED';
            }
        }

        $dataType .= ')';

        $this->migrationParts['columns'][count($this->migrationParts['columns']) - 1]['data_type']
            = $dataType;

        return $this;
    }

    /**
     * @param int|null  $size
     * @param bool      $unsigned
     * @return self
     */
    public function smallInt(?int $size = null, bool $unsigned = false): self
    {
        $dataType = 'SMALLINT(';
        if (null !== $size) {
            $dataType .= $size;
            if ($unsigned) {
                $dataType .= ' UNSIGNED';
            }
        }

        $dataType .= ')';

        $this->migrationParts['columns'][count($this->migrationParts['columns']) - 1]['data_type']
            = $dataType;

        return $this;
    }

    /**
     * @param int|null  $size
     * @param bool      $unsigned
     * @return self
     */
    public function mediumInt(?int $size = null, bool $unsigned = false): self
    {
        $dataType = 'MEDIUMINT(';
        if (null !== $size) {
            $dataType .= $size;
            if ($unsigned) {
                $dataType .= ' UNSIGNED';
            }
        }

        $dataType .= ')';

        $this->migrationParts['columns'][count($this->migrationParts['columns']) - 1]['data_type']
            = $dataType;

        return $this;
    }

    /**
     * @param int|null $size
     * @param int|null $digits
     * @return self
     */
    public function float(?int $size = null, ?int $digits  = null): self
    {
        $dataType = 'FLOAT(';
        if (null !== $size) {
            $dataType .= $size;
            if (null !== $digits) {
                $dataType .= ', '.$digits;
            }
        }

        $dataType .= ')';

        $this->migrationParts['columns'][count($this->migrationParts['columns']) - 1]['data_type']
            = $dataType;

        return $this;
    }

    /**
     * @param int|null $size
     * @param int|null $digits
     * @return self
     */
    public function double(?int $size = null, ?int $digits  = null): self
    {
        $dataType = 'DOUBLE(';
        if (null !== $size) {
            $dataType .= $size;
            if (null !== $digits) {
                $dataType .= ', '.$digits;
            }
        }

        $dataType .= ')';

        $this->migrationParts['columns'][count($this->migrationParts['columns']) - 1]['data_type']
            = $dataType;

        return $this;
    }

    /**
     * @param int|null $size
     * @param int|null $digits
     * @return self
     */
    public function decimal(?int $size = null, ?int $digits  = null): self
    {
        $dataType = 'DECIMAL(';
        if (null !== $size) {
            $dataType .= $size;
            if (null !== $digits) {
                $dataType .= ', '.$digits;
            }
        }

        $dataType .= ')';

        $this->migrationParts['columns'][count($this->migrationParts['columns']) - 1]['data_type']
            = $dataType;

        return $this;
    }

    /**
     * @return self
     */
    public function text(): self
    {
        $this->migrationParts['columns'][count($this->migrationParts['columns']) - 1]['data_type']
            = 'TEXT';

        return $this;
    }

    /**
     * @return self
     */
    public function tinyText(): self
    {
        $this->migrationParts['columns'][count($this->migrationParts['columns']) - 1]['data_type']
            = 'TINYTEXT';

        return $this;
    }

    /**
     * @param int $size
     * @return self
     */
    public function char(int $size): self
    {
        $this->migrationParts['columns'][count($this->migrationParts['columns']) - 1]['data_type']
            = 'CHAR('.$size.')';

        return $this;
    }

    /**
     * @param int $size
     * @return self
     */
    public function varchar(int $size): self
    {
        $this->migrationParts['columns'][count($this->migrationParts['columns']) - 1]['data_type']
            = 'VARCHAR('.$size.')';

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

        return $this->enum($values);
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

        $this->migrationParts['columns'][count($this->migrationParts['columns']) - 1]['data_type']
            = 'ENUM('.implode(', ', array_map(['static', 'compileValue'], $values)).')';

        return $this;
    }

    /**
     * @return self
     */
    public function time(): self
    {
        $this->migrationParts['columns'][count($this->migrationParts['columns']) - 1]['data_type']
            = 'TIME()';

        return $this;
    }

    /**
     * @return self
     */
    public function year(): self
    {
        $this->migrationParts['columns'][count($this->migrationParts['columns']) - 1]['data_type']
            = 'YEAR()';

        return $this;
    }

    /**
     * @return self
     */
    public function date(): self
    {
        $this->migrationParts['columns'][count($this->migrationParts['columns']) - 1]['data_type']
            = 'DATE()';

        return $this;
    }

    /**
     * @return self
     */
    public function datetime(): self
    {
        $this->migrationParts['columns'][count($this->migrationParts['columns']) - 1]['data_type']
            = 'DATETIME()';

        return $this;
    }

    /**
     * @return self
     */
    public function timestamp(): self
    {
        $this->migrationParts['columns'][count($this->migrationParts['columns']) - 1]['data_type']
            = 'TIMESTAMP()';

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
                return parent::getSql();
        }
    }

    /**
     * @param int $type
     * @return void
     */
    protected function setType(int $type): void
    {
        parent::setType($type);

        $this->migrationParts['drop_table']    = null;
        $this->migrationParts['if_exists']     = false;
        $this->migrationParts['create_table']  = null;
        $this->migrationParts['if_not_exists'] = false;
        $this->migrationParts['columns']       = [];
    }

    /**
     * @return string
     */
    protected function getSqlForDropTable(): string
    {
        $sql = ($this->migrationParts['if_exists'] ? 'DROP TABLE IF EXISTS ' : 'DROP TABLE ')
            .$this->migrationParts['drop_table'];

        return $sql.';';
    }
}
