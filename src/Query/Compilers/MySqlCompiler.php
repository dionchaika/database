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

use InvalidArgumentException;
use Dionchaika\Database\Query\CompilerInterface;

class MySqlCompiler implements CompilerInterface
{
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
     * Compile an SQL ORDER BY clause.
     *
     * Grammar:
     *      `sql_name`([, `sql_name`])* ASC|DESC.
     *
     * @param array  $columnNames
     * @param string $direction
     * @return string
     * @throws \InvalidArgumentException
     */
    public function compileOrderBy(array $columnNames, string $direction): string
    {
        $direction = strtoupper($direction);
        if ('ASC' !== $direction && 'DESC' !== $direction) {
            throw new InvalidArgumentException(
                'SQL "ORDER BY" clause direction must be "ASC" or "DESC"!'
            );
        }

        return implode(', ', array_map(['static', 'quoteName'], $columnNames)).' '.$direction;
    }

    /**
     * Compile an SQL LIMIT clause.
     *
     * Grammar:
     *      number[, number].
     *
     * @param int      $count
     * @param int|null $offset
     * @return string
     */
    public function compileLimit(int $count, ?int $offset = null): string
    {
        return (null === $offset) ? (string)$count : $offset.', '.$count;
    }

    /**
     * Compile an SQL SELECT statement.
     *
     * Grammar:
     *      SELECT[ DISTINCT] sql_name_components[ AS `sql_name`]|raw
     *          ([, sql_name_components[ AS `sql_name`]|raw])*
     *      FROM sql_name_components[ AS `sql_name`]|raw
     *      ORDER BY `sql_name`([, `sql_name`])* ASC|DESC
     *          ([, `sql_name`([, `sql_name`])* ASC|DESC])*
     *      LIMIT number[, number].
     *
     * @param array $parts
     * @return string
     */
    public function compileSelect(array $parts): string
    {
        if (null === $parts['from']) {
            return '';
        }

        if (empty($parts['select'])) {
            $parts['select'][] = '*';
        }

        $sql = $parts['distinct'] ? 'SELECT DISTINCT' : 'SELECT';
        $sql .= ' '.implode(', ', $parts['select']).' FROM '.$parts['from'];

        if (!empty($parts['orderBy'])) {
            $sql .= ' ORDER BY '.implode(', ', $parts['orderBy']);
        }

        if (null !== $parts['limit']) {
            $sql .= ' LIMIT '.$parts['limit'];
        }

        return $sql.';';
    }

    /**
     * Compile an SQL INSERT statement.
     *
     * Grammar:
     *      INSERT INTO sql_name_components[ AS `sql_name`]|raw
     *      ([sql_name_components[ AS `sql_name`]|raw
     *          ([, sql_name_components[ AS `sql_name`]|raw])*])
     *      VALUES ([NULL|TRUE|FALSE|number|?|:sql_parameter|'sql_string'|raw
     *          ([, NULL|TRUE|FALSE|number|?|:sql_parameter|'sql_string'|raw])]).
     *
     * @param array $parts
     * @return string
     */
    public function compileInsert(array $parts): string
    {
        if (null === $parts['into']) {
            return '';
        }

        $sql = 'INSERT INTO '.$parts['into'];
        $sql .= ' ('.implode(', ', array_keys($parts['values'])).')';
        $sql .= ' VALUES ('.implode(', ', array_values($parts['values'])).')';

        return $sql.';';
    }

    /**
     * Compile an SQL UPDATE statement.
     *
     * Grammar:
     *      UPDATE sql_name_components[ AS `sql_name`]|raw
     *      SET `sql_name` = NULL|TRUE|FALSE|number|?|:sql_parameter|'sql_string'|raw
     *          ([, `sql_name` = NULL|TRUE|FALSE|number|?|:sql_parameter|'sql_string'|raw])*.
     *
     * @param array $parts
     * @return string
     */
    public function compileUpdate(array $parts): string
    {
        if (null === $parts['update'] || empty($parts['set'])) {
            return '';
        }

        $set = [];
        foreach ($parts['set'] as $key => $value) {
            $set[] = $key.' = '.$value;
        }

        $sql = 'UPDATE '.$parts['update'].' SET '.implode(', ', $set);

        return $sql.';';
    }

    /**
     * Compile an SQL DELETE statement.
     *
     * Grammar:
     *      DELETE FROM sql_name_components[ AS `sql_name`]|raw.
     *
     * @param array $parts
     * @return string
     */
    public function compileDelete(array $parts): string
    {
        if ('' === $parts['from']) {
            return '';
        }

        $sql = 'DELETE FROM '.$parts['from'];

        return $sql.';';
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

        return $this->compileNameComponents($name).' AS '.$this->quoteName($alias);
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
