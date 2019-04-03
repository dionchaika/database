<?php

/**
 * The PHP MySQL Library.
 *
 * @package dionchaika/db
 * @version 1.0.0
 * @license MIT
 * @author Dion Chaika <dionchaika@gmail.com>
 */

namespace Dionchaika\Db;

use Exception;

class Connection
{
    /**
     * The database user.
     *
     * @var string
     */
    protected $user;

    /**
     * The database password.
     *
     * @var string
     */
    protected $password;

    /**
     * The database host.
     *
     * @var string
     */
    protected $host;

    /**
     * The database name.
     *
     * @var string
     */
    protected $name;

    /**
     * The database charset.
     *
     * @var string
     */
    protected $charset;

    /**
     * The database connection.
     *
     * @var mixed|null
     */
    protected $conn;

    /**
     * The SQL query string.
     *
     * @var string
     */
    protected $queryString = '';

    /**
     * The SQL query result.
     *
     * @var mixed
     */
    protected $queryResult = false;

    /**
     * @param string $user
     * @param string $password
     * @param string $host
     * @param string $name
     * @param string $charset
     */
    public function __construct(
        string $user = 'root',
        string $password = '',
        string $host = 'localhost',
        string $name = '',
        string $charset = 'utf8'
    ) {
        $this->user = $user;
        $this->password = $password;
        $this->host = $host;
        $this->name = $name;
        $this->charset = $charset;
    }

    /**
     * @throws \Exception
     */
    public function __destruct()
    {
        $this->disconnect();
    }

    /**
     * Set the database user.
     *
     * @param string $user
     * @return \Dionchaika\Db\Connection
     */
    public function setUser(string $user): Connection
    {
        $this->user = $user;
        return $this;
    }

    /**
     * Set the database password.
     *
     * @param string $user
     * @return \Dionchaika\Db\Connection
     */
    public function setPassword(string $password): Connection
    {
        $this->password = $password;
        return $this;
    }

    /**
     * Set the database host.
     *
     * @param string $user
     * @return \Dionchaika\Db\Connection
     */
    public function setHost(string $host): Connection
    {
        $this->host = $host;
        return $this;
    }

    /**
     * Set the database name.
     *
     * @param string $user
     * @return \Dionchaika\Db\Connection
     */
    public function setName(string $name): Connection
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Set the database charset.
     *
     * @param string $user
     * @return \Dionchaika\Db\Connection
     */
    public function setCharset(string $charset): Connection
    {
        $this->charset = $charset;
        return $this;
    }

    /**
     * Set a database connection.
     *
     * @param mixed $connection
     * @return \Dionchaika\Db\Connection
     */
    public function setConnection($connection): Connection
    {
        $this->conn = $connection;
        return $this;
    }

    /**
     * Get the database connection.
     *
     * @return mixed|null
     */
    public function getConnection()
    {
        return $this->conn;
    }

    /**
     * Connect to the database.
     *
     * @return mixed
     * @throws \Exception
     */
    public function connect()
    {
        $conn = mysqli_connect($this->host, $this->user, $this->password, $this->name);
        if (false === $conn) {
            $errorCode = mysqli_connect_errno();
            $errorMessage = mysqli_connect_error();

            throw new Exception(
                'Database connection error #'.$errorCode.': '.$errorMessage.'!'
            );
        }

        if (false === mysqli_set_charset($conn, $this->charset)) {
            $errorCode = mysqli_errno($conn);
            $errorMessage = mysqli_error($conn);

            throw new Exception(
                'Database set charset error #'.$errorCode.': '.$errorMessage.'!'
            );
        }

        return $this->conn = $conn;
    }

    /**
     * Disconnect from the database.
     *
     * @return void
     * @throws \Exception
     */
    public function disconnect()
    {
        if (false === mysqli_close($this->conn)) {
            $errorCode = mysqli_errno($this->conn);
            $errorMessage = mysqli_error($this->conn);

            throw new Exception(
                'Database disconnection error #'.$errorCode.': '.$errorMessage.'!'
            );
        };

        unset($this->conn);
    }

    /**
     * Get the SQL query string.
     *
     * @return string
     */
    public function getQueryString(): string
    {
        return $this->queryString;
    }

    /**
     * Perform an SQL query.
     *
     * @param string $query
     * @return \Dionchaika\Db\Connection
     * @throws \Exception
     */
    public function query(string $query): Connection
    {
        if (null === $this->conn) {
            $this->connect();
        }

        $this->queryString = $query;

        $queryResult = mysqli_query($this->conn, $this->queryString);
        if (false === $queryResult) {
            $errorCode = mysqli_errno($this->conn);
            $errorMessage = mysqli_error($this->conn);

            throw new Exception(
                'Database query error #'.$errorCode.': '.$errorMessage.'!'
            );
        }

        $this->queryResult = $queryResult;

        return $this;
    }

    /**
     * Fetch all of
     * the SQL query result rows.
     *
     * @return array
     */
    public function fetchAll(): array
    {
        if (is_bool($this->queryResult)) {
            return [];
        }

        $rows = [];
        while ($row = mysqli_fetch_assoc($this->queryResult)) {
            $rows[] = $row;
        }

        return $rows;
    }

    /**
     * Fetch all of
     * the SQL query result rows.
     *
     * An alias method name to fetchAll.
     *
     * @return array
     */
    public function fetch(): array
    {
        return $this->fetchAll();
    }

    /**
     * Fetch the first
     * SQL query result row.
     *
     * @return mixed|null
     */
    public function fetchFirst()
    {
        if (is_bool($this->queryResult)) {
            return null;
        }

        return mysqli_fetch_assoc($this->queryResult);
    }

    /**
     * Fetch the last
     * SQL query result row.
     *
     * @return mixed|null
     */
    public function fetchLast()
    {
        $rows = $this->fetchAll();

        if (0 === count($rows)) {
            return null;
        }

        return $rows[count($rows) - 1];
    }
}
