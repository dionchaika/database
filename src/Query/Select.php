<?php

/**
 * The PHP Database Library.
 *
 * @package dionchaika/db
 * @version 1.0.0
 * @license MIT
 * @author Dion Chaika <dionchaika@gmail.com>
 */

namespace Dionchaika\Db\Query;

class Select extends Query
{
    /**
     * The selected rows string.
     *
     * @var string
     */
    protected $rows;

    /**
     * @param array|mixed $rows
     */
    public function __construct($rows)
    {
        if ('*' !== $rows) {
            $rows = implode(', ', array_map(
                ['static', 'normalizeName'],
                is_array($rows) ? $rows : func_get_args()
            ));
        }

        $this->rows = $rows;
    }
}
