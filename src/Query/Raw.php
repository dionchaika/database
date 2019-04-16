<?php

namespace Dionchaika\Database\Query;

class Raw
{
    /**
     * The raw
     * SQL expression.
     *
     * @var string
     */
    protected $expression;

    /**
     * @param string $expression
     */
    public function __construct(string $expression)
    {
        $this->expression = $expression;
    }

    /**
     * Get the raw
     * SQL expression.
     *
     * @return string
     */
    public function getExpression(): string
    {
        return $this->expression;
    }

    /**
     * __toString magic method.
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->getExpression();
    }
}
