<?php

namespace Dionchaika\Database\Query;

trait QuoteTrait
{
    /**
     * @param mixed $name
     * @return string
     */
    protected function quoteName($name): string
    {
        return implode('.', array_map(function ($name) {
            return ('*' === $name) ? $name : '`'.str_replace('`', '\\`', $name).'`';
        }, explode('.', (string)$name)));
    }

    /**
     * @param mixed $value
     * @return string
     */
    protected function quoteValue($value): string
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

        $value = (string)$value;

        if ('?' === $value || 0 === strpos($value, ':') || is_numeric($value)) {
            return $value;
        }

        return '\''.str_replace('\'', '\\\'', $value).'\'';
    }
}
