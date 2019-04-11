<?php

namespace Dionchaika\Database\Query;

class Raw
{
    /**
     * @var string
     */
    protected $raw;

    /**
     * @param string $raw
     */
    public function __construct(string $raw)
    {
        $this->raw = $raw;
    }

    /**
     * @param string $raw
     * @return void
     */
    public function append(string $raw): void
    {
        $this->raw .= $raw;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->raw;
    }
}
