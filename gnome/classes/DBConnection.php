<?php

namespace gnome\classes;

use PDO;
use PDOException;

/**
 * Creates and manages the application's PDO database connection.
 */
class DBConnection
{
    /**
     * Database connection configuration.
     *
     * @var array<string, mixed>
     */
    protected $_config;

    /**
     * Redis connection configuration.
     * 
     * @var array<string, mixed>|null
     */
    protected $_redisConfig;

    /**
     * Active PDO connection.
     *
     * @var PDO|null
     */
    public $dbc;

    /**
     * Pagination LIMIT clause.
     *
     * @var string
     */
    public $limit;

    /**
     * Rows displayed per page.
     *
     * @var int
     */
    public $rowsPrePage = 10;


    /**
     * Singleton database connection.
     *
     * Keep this untyped because child classes such as Security
     * redeclare the property.
     *
     * @var DBConnection|null
     */
    protected static $instance = null;

    /**
     * Opens the database connection.
     */
    public function __construct()
    {
        $this->limit = '';

        $environment = $_SERVER['HTTP_ENVIRONMENT']
            ?? getenv('HTTP_ENVIRONMENT')
            ?: null;

        if ($environment === null) {
            throw new \RuntimeException(
                'HTTP_ENVIRONMENT is not configured.'
            );
        }

        $configPath = __DIR__
            . '/../../includes/crystal/settings.config.php';

        if (!is_file($configPath)) {
            throw new \RuntimeException(
                "Database configuration file not found: {$configPath}"
            );
        }

        $config = require $configPath;

        if (!isset($config['connections']['mysql'][$environment])) {
            throw new \RuntimeException(
                "Database configuration not found for environment: {$environment}"
            );
        }

        $this->_config = $config['connections']['mysql'][$environment];
        $this->_redisConfig = $config['redis']['env'][$environment] ?? null;

        $this->getPDOConnection();
    }

    /**
     * Returns the singleton DBConnection instance.
     */
    public static function instance()
    {
        if (self::$instance === null) {
            self::$instance = new DBConnection();
        }

        return self::$instance;
    }

    /**
     * Closes the database connection.
     */
    public function __destruct()
    {
        $this->dbc = null;
    }

    /**
     * Returns a Redis connection.
     */
    public function getRedisConnection() {
        // If the Redis extension is not available, return null to indicate
        // that a Redis connection cannot be created.
        if (!class_exists('Redis')) {
            return null;
        }

        /** @var \Redis $redis */
        $redis = new \Redis();

        try {
            $redis->connect(
                $this->_redisConfig['host'],
                $this->_redisConfig['port']
            );
        } catch (\Exception $exception) {
            $this->msg('Unable to connect to Redis: ' . $exception->getMessage());
            // throw new \RuntimeException(
            //     'Unable to connect to Redis: '
            //     . $exception->getMessage(),
            //     (int) $exception->getCode(),
            //     $exception
            // );
            return null;
        }

        return $redis;
    }

    /**
     * Opens the PDO database connection.
     */
    private function getPDOConnection()
    {
        if ($this->dbc !== null) {
            return;
        }

        $dsn = $this->_config['driver']
            . ':host=' . $this->_config['host']
            . ';dbname=' . $this->_config['database']
            . ';charset=utf8mb4'
            . ';port=' . $this->_config['port'];

        try {
            $this->dbc = new PDO(
                $dsn,
                $this->_config['username'],
                $this->_config['password']
            );

            /*
             * Preserve the application's previous error behavior.
             * This avoids changing every existing model query.
             */
            $this->dbc->setAttribute(
                PDO::ATTR_ERRMODE,
                PDO::ERRMODE_WARNING
            );

            $this->dbc->setAttribute(
                PDO::ATTR_DEFAULT_FETCH_MODE,
                PDO::FETCH_ASSOC
            );
        } catch (PDOException $exception) {
            throw new \RuntimeException(
                'Unable to connect to the database: '
                . $exception->getMessage(),
                (int) $exception->getCode(),
                $exception
            );
        }
    }

    /**
     * Returns the last inserted ID.
     */
    public function lastInsertId()
    {
        return $this->dbc->lastInsertId();
    }

    /**
     * Runs an INSERT, UPDATE, or DELETE query.
     *
     * This retains compatibility with existing calls that pass raw SQL.
     */
    public function runQuery($sql)
    {
        try {
            $count = $this->dbc->exec($sql);

            if ($count === false) {
                print_r($this->dbc->errorInfo());

                return 0;
            }

            return $count;
        } catch (PDOException $exception) {
            echo __LINE__ . $exception->getMessage();

            return 0;
        }
    }

    /**
     * Runs an existing non-parameterized SELECT query.
     *
     * This intentionally retains the original PDO::query() behavior so
     * existing models such as User.php are not changed globally.
     *
     * @return \PDOStatement|false
     */
    public function getQuery($sql)
    {
        $statement = $this->dbc->query($sql);

        if ($statement === false) {
            return false;
        }

        $statement->setFetchMode(PDO::FETCH_ASSOC);

        return $statement;
    }

    /**
     * Runs a parameterized SELECT query.
     *
     * Use this only for queries that contain placeholders.
     *
     * Named parameter example:
     *
     * $sql = 'SELECT * FROM indices WHERE name LIKE :filter_alpha';
     *
     * $params = [
     *     'filter_alpha' => '%value%',
     * ];
     *
     * @param array<int|string, mixed> $params
     * @return \PDOStatement|false
     */
    public function getPreparedQuery($sql, array $params = [])
    {
        $statement = $this->dbc->prepare($sql);

        if ($statement === false) {
            print_r($this->dbc->errorInfo());

            return false;
        }

        /*
         * PDO accepts named parameter keys with or without the colon,
         * but normalizing them avoids inconsistencies between callers.
         */
        $normalizedParams = [];

        foreach ($params as $key => $value) {
            if (is_int($key)) {
                $normalizedParams[$key] = $value;
                continue;
            }

            $normalizedParams[ltrim($key, ':')] = $value;
        }

        $executed = $statement->execute($normalizedParams);

        if (!$executed) {
            print_r($statement->errorInfo());

            return false;
        }

        $statement->setFetchMode(PDO::FETCH_ASSOC);

        return $statement;
    }

    /**
     * Runs a parameterized INSERT, UPDATE, or DELETE query.
     *
     * @param array<int|string, mixed> $params
     */
    public function runPreparedQuery($sql, array $params = [])
    {
        $statement = $this->dbc->prepare($sql);

        if ($statement === false) {
            print_r($this->dbc->errorInfo());

            return 0;
        }

        $normalizedParams = [];

        foreach ($params as $key => $value) {
            if (is_int($key)) {
                $normalizedParams[$key] = $value;
                continue;
            }

            $normalizedParams[ltrim($key, ':')] = $value;
        }

        if (!$statement->execute($normalizedParams)) {
            print_r($statement->errorInfo());

            return 0;
        }

        return $statement->rowCount();
    }

    /**
     * Displays an SQL statement with values substituted for debugging.
     *
     * This method must not be used to execute SQL.
     *
     * @param array<int|string, mixed> $data
     */
    public function showquery($string, $data)
    {
        $indexed = $data === array_values($data);

        foreach ($data as $key => $value) {
            if ($value === null) {
                $formattedValue = 'NULL';
            } elseif (is_bool($value)) {
                $formattedValue = $value ? '1' : '0';
            } elseif (is_string($value)) {
                $formattedValue = $this->dbc->quote($value);
            } else {
                $formattedValue = (string) $value;
            }

            if ($indexed) {
                $string = preg_replace(
                    '/\?/',
                    $formattedValue,
                    $string,
                    1
                );

                continue;
            }

            $placeholder = str_starts_with((string) $key, ':')
                ? (string) $key
                : ':' . $key;

            $string = str_replace(
                $placeholder,
                $formattedValue,
                $string
            );
        }

        return $string;
    }

    /**
     * Returns the number of records from a table.
     *
     * Retained for compatibility with existing model inheritance.
     */
    protected function getCount(
        $tableOveride = null,
        $condition = null
    ) {
        $table = $this->table;

        if ($table === '') {
            throw new \RuntimeException(
                'Table name is not set for this DBConnection instance.'
            );
        }
        
        if ($tableOveride !== null) {
            $table = $tableOveride;
        }

        if ($condition === null) {
            $condition = 1;
        }

        $sql = "
            SELECT COUNT(*) AS count
            FROM {$table}
            WHERE {$condition}
        ";

        $statement = $this->getQuery($sql);

        if ($statement === false) {
            return 0;
        }

        return (int) $statement->fetchColumn();
    }

    /**
     * Returns the number of matching records using equality conditions.
     *
     * Example:
     *
     * $count = $this->getCountPDO(
     *     'your_table',
     *     ['column_name' => 'value']
     * );
     *
     * @param array<string, mixed> $conditions
     */
    public function getCountPDO(
        $tableOverride = null,
        array $conditions = []
    ) {
        $table = $this->table;

        if ($table === '') {
            throw new \RuntimeException(
                'Table name is not set for this DBConnection instance.'
            );
        }

        if ($tableOverride !== null) {
            $table = $tableOverride;
        }

        $whereParts = [];
        $params = [];

        foreach ($conditions as $column => $value) {
            /*
             * Column and table names cannot be bound with PDO.
             * Permit only simple SQL identifiers.
             */
            if (
                !preg_match(
                    '/^[A-Za-z_][A-Za-z0-9_.]*$/',
                    $column
                )
            ) {
                throw new \InvalidArgumentException(
                    "Invalid database column: {$column}"
                );
            }

            $parameterName = str_replace('.', '_', $column);

            $whereParts[] = "{$column} = :{$parameterName}";
            $params[$parameterName] = $value;
        }

        $whereClause = $whereParts
            ? implode(' AND ', $whereParts)
            : '1 = 1';

        $sql = "
            SELECT COUNT(*) AS count
            FROM {$table}
            WHERE {$whereClause}
        ";

        $statement = $this->getPreparedQuery($sql, $params);

        if ($statement === false) {
            return 0;
        }

        return (int) $statement->fetchColumn();
    }

    /**
     * Builds a LIMIT clause for the legacy pagination implementation.
     */
    public function paginate()
    {
        $requestedPage = $_POST['page']
            ?? $_GET['page']
            ?? null;

        if ($requestedPage === null) {
            return '';
        }

        $page = max(1, (int) $requestedPage);
        $offset = ($page - 1) * $this->rowsPrePage;

        return sprintf(
            ' LIMIT %d, %d ',
            $offset,
            $this->rowsPrePage
        );
    }

    /**
     * Stores a response message in the session.
     */
    public function msg($message)
    {
        $_SESSION['actionResponse'] = $message;
    }
}