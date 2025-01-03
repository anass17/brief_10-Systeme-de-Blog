<?php

    class Database {
        private string $host;
        private string $dbname;
        private string $username;
        private string $password;
        private string $charset = 'utf8mb4';
        private $conn;

        public function __construct(string $host = 'localhost', string $dbname = 'bibliotheque', string $username = 'root', string $password = 'root123') {
            $this -> host = $host;
            $this -> dbname = $dbname;
            $this -> username = $username;
            $this -> password = $password;
        }

        // Method to connect to database using PDO Class

        public function connect() {
            $dsn = "mysql:host={$this -> host};dbname={$this -> dbname};charset={$this -> charset}";
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_CASE => PDO::CASE_LOWER
            ];

            try {
                $this -> conn = new PDO($dsn, $this -> username, $this -> password, $options);

                return true;
            } catch (PDOException $e) {
                return false;
            }
        }

        // Method to execute an insert statement

        public function insert(string $table, array $columns, array $data) {

            if (count($columns) == 0 || count($columns) != count($data)) {
                return false;
            }

            // repeat the placeholder, to match the number of provided columns

            $placeholders = '';            
            for($i = 0; $i < count($columns); $i++) {
                $placeholders .= '?,';
            }
            $placeholders = trim($placeholders, ',');


            // Join columns into a string

            $columns_string = implode(', ', $columns);

            // Combine all parts to form an insert statement

            $sql = "INSERT INTO $table ($columns_string) VALUES ($placeholders)";

            $stmt = $this -> conn -> prepare($sql);

            try {
                return $stmt -> execute($data);
            } catch (Exception) {
                return false;
            }
        }

        // Method to execute a delete statment 

        public function delete(string $table, array $conditions) {

            $conditions_string = implode(' AND ', $conditions);

            $sql = "DELETE FROM $table WHERE $conditions_string";

            // Execute Statment

            try {
                $stmt = $this -> conn -> query($sql);

                if ($stmt -> rowCount() > 0) {
                    return true;
                }

                return false;
            } catch (PDOException) {
                return false;
            }
        }

        // public function update(string $table, array $columns, array $data, $condition) {

        // }
    }

    // $db = new Database();

    // $db -> connect();

    // if ($db -> delete('users', ['first_name = "Ali"'])) {
    //     echo 'Yes';
    // } else {
    //     echo 'No';
    // }
    // if ($db -> insert('users', ['first_name', 'last_name', 'age'], ['Ali', 'Gabri', 36])) {
    //     echo 'Yes';
    // } else {
    //     echo 'No';
    // }