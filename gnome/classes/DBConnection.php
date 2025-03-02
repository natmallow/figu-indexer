<?php

namespace gnome\classes;



/*
 * Class DBConnection
 * Create a database connection using PDO
 * @author jonahlyn@unm.edu
 *
 * Instructions for use:
 *
 * require_once('settings.config.php');          // Define db configuration arrays here
 * require_once('DBConnection.php');             // Include this file
 *
 * $database = new DBConnection($dbconfig);      // Create new connection by passing in your configuration array
 *
 * $sqlSelect = "select * from .....";           // Select Statements:
 * $rows = $database->getQuery($sqlSelect);      // Use this method to run select statements
 *
 * foreach($rows as $row){
 * 		echo $row["column"] . "<br/>";
 * }
 *
 * $sqlInsert = "insert into ....";              // Insert/Update/Delete Statements:
 * $count = $database->runQuery($sqlInsert);     // Use this method to run inserts/updates/deletes
 * echo "number of records inserted: " . $count;
 *
 * $name = "jonahlyn";                          // Prepared Statements:
 * $stmt = $database->dbc->prepare("insert into test (name) values (?)");
 * $stmt->execute(array($name));
 *
 */

use PDO;
use PDOException;

class DBConnection
{

    // Database Connection Configuration Parameters
    // array('driver' => 'mysql','host' => '','dbname' => '','username' => '','password' => '')
    protected $_config;

    // Database Connection
    public $dbc;

    // pagination
    public $limit;

    // rows per page
    public $rowsPrePage = 10;

    // Singleton Database Connection
    protected static $instance = null;

    /* function __construct
     * Opens the database connection
     * @param $config is an array of database connection parameters
     */
    public function __construct()
    {
        $this->limit = "";
        $env = $_SERVER['HTTP_ENVIRONMENT'];
        $config = require(__DIR__ . '/../../includes/crystal/settings.config.php');
        $this->_config = $config['connections']['mysql'][$env];
        $this->getPDOConnection();
    }

    public static function instance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new DBConnection();
        }
        return self::$instance;
    }

    /* Function __destruct
     * Closes the database connection
     */
    public function __destruct()
    {
        $this->dbc = NULL;
    }

    /* Function getPDOConnection
     * Get a connection to the database using PDO.
     */
    private function getPDOConnection()
    {
        // Check if the connection is already established
        if ($this->dbc == NULL) {
            // Create the connection
            $dsn = "" .
                $this->_config['driver'] .
                ":host=" . $this->_config['host'] .
                ";dbname=" . $this->_config['database'] .
                ';charset=utf8mb4' .
                ";port=" . $this->_config['port'];

            try {
                $this->dbc = new \PDO($dsn, $this->_config['username'], $this->_config['password']);
            } catch (PDOException $e) {
                echo __LINE__ . $e->getMessage();
            }

            $this->dbc->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_WARNING);
        }
    }


    public function lastInsertId()
    {
        return $this->dbc->lastInsertId();
    }

    /* Function runQuery
     * Runs a insert, update or delete query
     * @param string sql insert update or delete statement
     * @return int count of records affected by running the sql statement.
     */
    public function runQuery($sql)
    {
        try {
            $count = $this->dbc->exec($sql) or print_r($this->dbc->errorInfo());
        } catch (\PDOException $e) {
            echo __LINE__ . $e->getMessage();
        }
        return $count;
    }

    /* Function getQuery
     * Runs a select query
     * @param string sql insert update or delete statement
     * @returns associative array
     */
    public function getQuery($sql)
    {

        $stmt = $this->dbc->query($sql);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);

        return $stmt;
    }

    /**
     * Show the sql statement
     */
    public function showquery($string, $data)
    {
        $indexed = $data == array_values($data);
        foreach ($data as $k => $v) {
            if (is_string($v)) $v = "'$v'";
            if ($indexed) $string = preg_replace('/\?/', $v, $string, 1);
            else $string = str_replace("$k", $v, $string);
            // var_dump()
        }
        return $string;
    }

    protected function getCount($tableOveride = null, $condition = null)
    {

        // this is not a problem gets inherited
        $table = $this->table;

        if (!is_null($tableOveride)) {
            $table = $tableOveride;
        }

        if (is_null($condition)) {
            $condition = 1;
        }

        $sql = "SELECT COUNT(*) as count
                FROM {$table} 
                WHERE $condition";

        return $this->getQuery($sql)->fetch()["count"];
    }

    /**
     * USage:
     * $count = $this->getCount('your_table', ['column_name' => 'value']);
     */
    public function getCountPDO($tableOverride = null, array $conditions = [])
    {

        $table = $this->table;

        if (!is_null($tableOverride)) {
            $table = $tableOverride;
        }

        // Constructing the WHERE clause with bound parameters
        $whereClause = '1'; // Default condition (always true)
        $params = [];
        if (!empty($conditions)) {
            $whereParts = [];
            foreach ($conditions as $column => $value) {
                $whereParts[] = "$column = :$column";
                $params[":$column"] = $value;
            }
            $whereClause = implode(' AND ', $whereParts);
        }

        $sql = "SELECT COUNT(*) as count FROM {$table} WHERE {$whereClause}";

        // Prepare and execute the query with bound parameters
        $stmt = $this->dbc->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchColumn();
    }



    /**
     * attaches pagination to call
     */
    public function paginate()
    {

        $limit = "";
        $page = 1;

        if (!empty($_POST["page"]) || !empty($_GET["page"])) {
            $page = !empty($_POST["page"]) ? filter_input(INPUT_POST, 'page') : filter_input(INPUT_GET, 'page');
            $start = ($page - 1) * $this->rowsPrePage;
            $limit = " LIMIT $page, $start ";
        }

        return $limit;
    }

    /**
     * response msg
     */
    public function msg($message)
    {
        $_SESSION['actionResponse'] = $message;
    }
}
