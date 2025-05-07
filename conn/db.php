<?php
date_default_timezone_set('Asia/Manila');
require_once 'config.php';
session_start();

class DatabaseHandler
{

    public $pdo;

    public function __construct()
    {
        $dbHost = DB_SERVER;
        $dbPort = DB_PORT;
        $dbName = DB_DATABASE;
        $dbUser = DB_USERNAME;
        $dbPassword = DB_PASSWORD;

        try {
            $dsn = "mysql:host=$dbHost;port=$dbPort;dbname=$dbName;charset=utf8mb4";
            $this->pdo = new PDO($dsn, $dbUser, $dbPassword, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
            ]);
        } catch (PDOException $e) {
            die("Database connection failed: " . $e->getMessage());
        }
    }

    // Basic Queries
    public function getAllRows($tableName)
    {
        return $this->fetchAll("SELECT * FROM `$tableName` WHERE status = 1");
    }

    public function getRowsWithLimit($tableName, $limit, $offset)
    {
        return $this->fetchAll(
            "SELECT * FROM `$tableName` LIMIT :limit OFFSET :offset",
            ['limit' => (int)$limit, 'offset' => (int)$offset]
        );
    }


    public function getGroupedRows($tableName, array $conditions = [], $groupBy = '')
    {
        $where = '1';
        $params = [];

        foreach ($conditions as $column => $value) {
            $where .= " AND `$column` = :$column";
            $params[$column] = $value;
        }

        $sql = "SELECT * FROM `$tableName` WHERE $where";
        if ($groupBy) $sql .= " GROUP BY `$groupBy`";

        return $this->fetchAll($sql, $params);
    }


    // Authentication
    public function loginUser($email, $password)
    {
        $user = $this->fetchOne("SELECT * FROM `users` WHERE email = :email AND status = 1", ['email' => $email]);

        if ($user && $user['password'] === $password) {
            $_SESSION['user'] = $user['position'];
            $_SESSION['id'] = $user['id'];
            $_SESSION['name'] = $user['name'];
            $_SESSION['last_activity'] = time();
            return true;
        }
        return false;
    }

    public function loginUserSecure($email, $password)
    {
        $user = $this->fetchOne("SELECT * FROM `users` WHERE email = :email AND status = 1 ", ['email' => $email]);

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user'] = $user['position'];
            $_SESSION['id'] = $user['id'];
            $_SESSION['name'] = $user['name'];
            $_SESSION['last_activity'] = time();

            return true;
        } else {
            return false;
        }
    }

    // CRUD Operations
    public function insert($tableName, array $data)
    {
        $columns = implode(", ", array_map(fn($col) => "`$col`", array_keys($data)));
        $placeholders = implode(", ", array_map(fn($col) => ":$col", array_keys($data)));

        $sql = "INSERT INTO `$tableName` ($columns) VALUES ($placeholders)";
        $this->execute($sql, $data);

        return $this->pdo->lastInsertId();
    }

    public function update($tableName, array $data, array $conditions)
    {
        $set = implode(", ", array_map(fn($col) => "`$col` = :$col", array_keys($data)));
        $where = implode(" AND ", array_map(fn($col) => "`$col` = :where_$col", array_keys($conditions)));

        $params = array_merge($data, array_combine(
            array_map(fn($col) => "where_$col", array_keys($conditions)),
            array_values($conditions)
        ));

        $sql = "UPDATE `$tableName` SET $set WHERE $where";
        $this->execute($sql, $params);

        return true;
    }

    public function delete($tableName, array $conditions)
    {
        $where = implode(" AND ", array_map(fn($col) => "`$col` = :$col", array_keys($conditions)));
        $sql = "DELETE FROM `$tableName` WHERE $where";

        $this->execute($sql, $conditions);
        return true;
    }

    // Helpers
    public function fetchAll($sql, array $params = [])
    {
        $stmt = $this->pdo->prepare($sql);
        foreach ($params as $key => $value) $stmt->bindValue(":$key", $value);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function fetchOne($sql, array $params = [])
    {
        $stmt = $this->pdo->prepare($sql);
        foreach ($params as $key => $value) $stmt->bindValue(":$key", $value);
        $stmt->execute();
        return $stmt->fetch();
    }

    public function execute($sql, array $params)
    {
        $stmt = $this->pdo->prepare($sql);
        foreach ($params as $key => $value) $stmt->bindValue(":$key", $value);
        $stmt->execute();
    }

    public function getLastInsertId()
    {
        return $this->pdo->lastInsertId();
    }
    // Session Management
    public function isLoggedIn()
    {
        return !empty($_SESSION['id']);
    }


    public function logOut()
    {
        session_unset();
        session_destroy();
    }
    public function getUser()
    {
        if (isset($_SESSION['id'])) {
            return [
                'id' => $_SESSION['id'],
                'name' => $_SESSION['name'],
                'position' => $_SESSION['user'] ?? null,
            ];
        }
        return null;
    }
    public function getRowsWhere($tableName, array $conditions = [], $orderBy = '', $limit = '', $likeColumns = [])
    {
        $where = 'status = 1';
        $params = [];

        foreach ($conditions as $column => $value) {
            // Check if column should be a LIKE search
            if (in_array($column, $likeColumns)) {
                $where .= " AND `$column` LIKE :$column";
                $params[$column] = '%' . $value . '%';
            } else {
                $where .= " AND `$column` = :$column";
                $params[$column] = $value;
            }
        }

        $sql = "SELECT * FROM `$tableName` WHERE $where";
        if ($orderBy) $sql .= " ORDER BY $orderBy";
        if ($limit)   $sql .= " LIMIT $limit";

        return $this->fetchAll($sql, $params);
    }
}
