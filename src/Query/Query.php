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

class Query
{
    /**
     * The query
     * SELECT type.
     */
    const TYPE_SELECT = 0;

    /**
     * The query
     * INSERT type.
     */
    const TYPE_INSERT = 1;

    /**
     * The query
     * UPDATE type.
     */
    const TYPE_UPDATE = 2;

    /**
     * The query
     * DELETE type.
     */
    const TYPE_DELETE = 3;

    /**
     * The query type.
     *
     * @var int
     */
    protected $type = self::TYPE_SELECT;

    /**
     * The array
     * of query parts.
     *
     * @var array
     */
    protected $parts = [
        'select' => [],
        'distinct' => false,
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
     * The query compiler.
     *
     * @var \Dionchaika\Database\CompilerInterface
     */
    protected $compiler;

    /**
     * @param \Dionchaika\Database\CompilerInterface $compiler
     */
    public function __construct(CompilerInterface $compiler)
    {
        $this->compiler = $compiler;
    }
}
