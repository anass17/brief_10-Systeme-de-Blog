<?php

    class Auth {
        private $db;
        public $user;

        public function __construct() {
            $this -> db = new Database();
            $this -> user = new User();
        }

        // Method to log the user into his account

        public function login() {

            // Check if all the necessary data was entered

            if (
                empty($this -> user -> getEmail()) ||
                empty($this -> user -> getPassword())
            ) {
                echo 'Please fill in the form';
                return false;
            }

            $data = [
                $this -> user -> getEmail()
            ];

            $result = $this -> db -> select("SELECT * from users WHERE email = ?", $data);

            if (!$result) {
                echo 'Incorrect Email address or password';
                return false;
            }

            if (!password_verify($this -> user -> getPassword(), $result[0]['password'])) {
                echo 'Incorrect Email address or password';
                return false;
            }

            echo 'Logged in';

        }

        // Method to register new user

        public function register() {

            // Check if there are any errors while inserting data

            if (!empty($this -> user -> getError())) {
                echo $this -> user -> getError();
                return false;
            }

            // Check if all the necessary data was entered

            if (
                empty($this -> user -> getFirstName()) ||
                empty($this -> user -> getLastName()) ||
                empty($this -> user -> getEmail()) ||
                empty($this -> user -> getPassword())
            ) {
                echo 'Please fill in the form';
                return false;
            }

            // check if email already exists

            $result = $this -> db -> select("SELECT * from users WHERE email = ?", [$this -> user -> getEmail()]);

            if ($result) {
                echo 'This email already Exists';
                return false;
            }

            // If not, insert new row in users table

            $columns = [
                'first_name',
                'last_name',
                'email',
                'password'
            ];

            $data = [
                $this -> user -> getFirstName(),
                $this -> user -> getLastName(),
                $this -> user -> getEmail(),
                password_hash($this -> user -> getPassword(), PASSWORD_BCRYPT)
            ];

            if (!$this -> db -> insert('users', $columns, $data)) {
                echo 'Could not process your request'; 
            } else {
                echo 'added successfully';
            }
        }

        // Method to the user out

        public function logout() {

        }

    }

?>