<?php

/**
 * The PHP Database Library.
 *
 * @package dionchaika/database
 * @version 1.0.0
 * @license MIT
 * @author Dion Chaika <dionchaika@gmail.com>
 */

namespace Dionchaika\Database\Query\Compilers;

use Dionchaika\Database\Query\CompilerInterface;

class MySqlCompiler implements CompilerInterface
{
    /**
     * The array
     * of SQL parts.
     *
     * @var array
     */
    protected $sqlParts = [
        'select' => [],
        'from' => null,
        'where' => [],
        'orderBy' => [],
        'limit' => null,
        'into' => null,
        'values' => [],
        'update' => null,
        'set' => []
    ];

    /**
     * Compile an SQL name.
     *
     * Grammar:
     *      sql_name_components[ AS `sql_name`].
     *
     * @param mixed $name
     * @return string
     */
    public function compileName($name): string
    {
        $name = (string)$name;

        if (preg_match('/\s+as\s+/i', $name)) {
            return $this->compileAliasedName($name);
        }

        return $this->compileNameComponents($name);
    }

    /**
     * Compile an SQL value.
     *
     * Grammar:
     *      NULL|TRUE|FALSE|number|?|:sql_parameter|'sql_string'.
     *
     * @param mixed $value
     * @return string
     */
    public function compileValue($value): string
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
     * Quote an SQL name.
     *
     * Grammar:
     *      *|`sql_name`.
     *
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
     * Quote an SQL string.
     *
     * Grammar:
     *      'sql_string'.
     *
     * @param string $string
     * @return string
     */
    protected function quoteString(string $string): string
    {
        return '\''.str_replace('\'', '\\\'', $string).'\'';
    }

    /**
     * Compile an aliased SQL name.
     *
     * Grammar:
     *      sql_name_components AS `sql_name`.
     *
     * @param string $aliasedName
     * @return string
     */
    protected function compileAliasedName(string $aliasedName): string
    {
        $aliasedNameParts = preg_split('/\s+as\s+/i', $aliasedName);

        $name = $aliasedNameParts[0];
        $alias = $aliasedNameParts[1];

        return "{$this->compileNameComponents($name)} AS {$this->quoteName($alias)}";
    }

    /**
     * Compile an SQL name components.
     *
     * Grammar:
     *      [`sql_name`.]`sql_name` or
     *      [[`sql_name`.]`sql_name`.]`sql_name`.
     *
     * @param string $nameComponents
     * @return string
     */
    protected function compileNameComponents(string $nameComponents): string
    {
        return implode('.', array_map(['static', 'quoteName'], preg_split('/\s*\.\s*/', $nameComponents, 3)));
    }
}
