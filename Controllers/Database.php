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

        // Method to execute a delete statement 

        public function delete(string $table, string $condition) {

            $sql = "DELETE FROM $table WHERE $condition";

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

        // Method to execute an update statement

        public function update(string $table, array $columns, array $data, string $condition) {
            
            if (count($columns) == 0 || count($columns) != count($data)) {
                return false;
            }

            // combine columns with placeholders

            $columns_string = ''; 

            foreach($columns as $col) {
                $columns_string .= $col . ' = ?,';
            }

            $columns_string = rtrim($columns_string, ',');

            // Combine all parts to form an insert statement

            $sql = "UPDATE $table SET $columns_string WHERE $condition";

            $stmt = $this -> conn -> prepare($sql);

            try {
                return $stmt -> execute($data);
            } catch (Exception) {
                return false;
            }

        }
    }

    $db = new Database();

    $db -> connect();

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
    if ($db -> update('users', ['first_name', 'age'], ['Ahmed', 61], 'user_id = 1')) {
        echo 'Yes';
    } else {
        echo 'No';
    }