<?php namespace App\Libraries;

use PDO;

class Database
{
    /**
     * @var PDO
     */
    protected $pdo;


    /**
     * @param PDO $pdo
     */
    public function __construct(PDO $pdo)
    {
        // Configure PDO to throw exceptions on error
        // and to use ASSOC as default fetch mode
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        $this->pdo = $pdo;
    }


    /**
     * Insert a record
     *
     * @param  string $query
     * @param  array  $params
     * @return int    The id of the last inserted record
     */
    public function insert($query, array $params = [])
    {
        $stmt = $this->query($query, $params);

        return $this->pdo->lastInsertId();
    }


    /**
     * Update a record
     *
     * @param  string $query
     * @param  array  $params
     * @return int    Number of affected rows
     */
    public function update($query, array $params = [])
    {
        $stmt = $this->query($query, $params);

        return $stmt->rowCount();
    }


    /**
     * Get records
     *
     * @param  string  $query
     * @param  array   $params
     * @param  boolean $single If true, only the first record will be returned
     * @return array
     */
    public function select($query, array $params = [], $single = false)
    {
        $stmt = $this->query($query, $params);

        return $single
            ? $stmt->fetch()
            : $stmt->fetchAll();
    }


    /**
     * Make a generic query and return the statement
     *
     * @param  string $query
     * @param  array  $params
     * @return PDOStatement
     */
    public function query($query, array $params = [])
    {
        $stmt = $this->pdo->prepare($query);
        $stmt->execute($params);

        return $stmt;
    }
}
