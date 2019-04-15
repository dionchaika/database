<?php

class MySqlCompiler
{
    public function quoteName(string $name): string
    {
        return "`{${str_replace('`', '\\`', $name)}}`";
    }

    public function quoteString(string $string): string
    {
        return "'{${str_replace('\'', '\\\'', $string)}}'";
    }

    public function compileName($name): string
    {
        $name = (string)$name;

        if (preg_match('/ as /i', $name)) {
            return $this->compileAliasedName($name);
        }

        return $this->compileNameComponents($name);
    }

    public function compileAliasedName(string $aliasedName): string
    {
        $aliasedNameParts = preg_split('/ as /i', $aliasedName);

        $name = $aliasedNameParts[0];
        $alias = $aliasedNameParts[1];

        return "{$this->compileNameComponents($name)} AS {$this->quoteName($alias)}";
    }

    public function compileNameComponents(string $nameComponents): string
    {
        return implode('.', array_map(['static', 'quoteName'], explode('.', $nameComponents)));
    }

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
