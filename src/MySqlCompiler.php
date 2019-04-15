<?php

class MySqlCompiler
{
    /**
     * Quote an SQL name.
     *
     * Syntax:
     *      *|`sql_name`.
     *
     * @param string $name
     * @return string
     */
    public function quoteName(string $name): string
    {
        return ('*' === $name)
            ? $name
            : "`{${str_replace('`', '\\`', $name)}}`";
    }

    /**
     * Quote an SQL string.
     *
     * Syntax:
     *      'sql_string'.
     *
     * @param string $string
     * @return string
     */
    public function quoteString(string $string): string
    {
        return "'{${str_replace('\'', '\\\'', $string)}}'";
    }

    /**
     * Compile an SQL name.
     *
     * Syntax:
     *      sql_name_components[ AS sql_name].
     *
     * @param mixed $name
     * @return string
     */
    public function compileName($name): string
    {
        $name = (string)$name;

        if (preg_match('/ as /i', $name)) {
            return $this->compileAliasedName($name);
        }

        return $this->compileNameComponents($name);
    }

    /**
     * Compile an aliased SQL name.
     *
     * Syntax:
     *      sql_name_components AS sql_name.
     *
     * @param string $aliasedName
     * @return string
     */
    public function compileAliasedName(string $aliasedName): string
    {
        $aliasedNameParts = preg_split('/ as /i', $aliasedName);

        $name = $aliasedNameParts[0];
        $alias = $aliasedNameParts[1];

        return "{$this->compileNameComponents($name)} AS {$this->quoteName($alias)}";
    }

    /**
     * Compile an SQL name components.
     *
     * Syntax:
     *      [sql_name.]sql_name or
     *      [[sql_name.]sql_name.]sql_name
     *
     * @param string $nameComponents
     * @return string
     */
    public function compileNameComponents(string $nameComponents): string
    {
        return implode('.', array_map(['static', 'quoteName'], explode('.', $nameComponents)));
    }

    /**
     * Compile an SQL value.
     *
     * Syntax:
     *      NULL|TRUE|FALSE|int|float|?|:patameter|'sql_name'.
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
            return $value;
        }

        $value = (string)$value;

        if ('?' === $value || 0 === strpos($value, ':')) {
            return $value;
        }

        return $this->quoteString($value);
    }
}
