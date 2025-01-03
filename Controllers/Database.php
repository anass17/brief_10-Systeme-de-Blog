<?php

    class Database {
        private string $host;
        private string $dbname;
        private string $username;
        private string $password;
        private string $charset = 'utf8mb4';
        private $conn;

        public function __construct(string $host = 'localhost', string $dbname = 'blogs-db', string $username = 'root', string $password = 'root123') {
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

        // public function query() {

        // }
    }