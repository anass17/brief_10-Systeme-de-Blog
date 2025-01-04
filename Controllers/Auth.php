<?php

    session_start();

    include 'Database.php';
    include 'User.php';

    class Auth {
        public $db;
        public $user;
        private $error;

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

        // Method to create/update Access Token

        public function createAccessToken() {

            // Id property must not be empty

            if (empty($this -> user -> getId())) {
                $this -> error = "Invalid user ID";
                return false;
            }

            // Set the token and its expiration time 

            date_default_timezone_set('Etc/GMT-1');         // Set timezone to UTC + 1
            $token = bin2hex(random_bytes(32));
            $token_expiration = time() + 15 * 60;
            $token_expiration_formated = date('Y-m-d H:i:s', $token_expiration);

            // Store token in the database

            if (!$this -> db -> update('users', ['token', 'token_expiration'], [$token, $token_expiration_formated], 'user_id = ' . $this -> user -> getId())) {
                $this -> error = "Could not assign a token";
                return false;
            }

            // Store token in a cookie

            $cookie_value = $this -> user -> getId() . '.' . $token;            // In the format "ID.token"
            setcookie('token', $cookie_value, $token_expiration, '/', 'localhost', true, true);

            return true;
        }

        // Method to check if Access Token Exists

        public function isAccessTokenExists() {
            if (!isset($_COOKIE['token'])) {
                return false;
            }

            // Return cookie parts

            $cookie_params = explode('.', $_COOKIE['token']);

            // A token cookie must contain two parts: User ID + token

            if (count($cookie_params) != 2) {
                return false;
            }

            // Check if the token is valid

            $result = $this -> db -> select("SELECT * FROM users WHERE user_id = ? and token = ? and token_expiration > Current_timestamp", [$cookie_params[0], $cookie_params[1]]);

            if (!$result) {
                return false;
            }

            // Set session parameters
            
            $_SESSION['id'] = $cookie_params[0];
            $_SESSION['token'] = $cookie_params[1];

            return true;
        }

        // Method to delete Access Token
        
        public function deleteAccessToken() {

            // Remove token from database

            if (!$this -> db -> update('users', ['token', 'token_expiration'], ['', null], 'user_id = ' . $this -> user -> getId())) {
                $this -> error = "Could not delete acess token";
                return false;
            }

            // Remove token cookie

            setcookie('token', '', time() - 0, '/', 'localhost', true, true);

            return true;
        }

        // Method to create CSRF Token

        public function createCSRFToken() {
            $csrf_token = bin2hex(random_bytes(32));
            $_SESSION['CSRF_token'] = $csrf_token;
        }

        public function isCSRFTokenValid(string $token) {
            if (isset($_SESSION['CSRF_token']) && $_SESSION["CSRF_token"] == $token) {
                return true;
            }

            return false;
        }

        public function isEmailExists() {

        }

        public function insertVisitor() {

        }

    }

    $auth = new Auth();

    // $auth -> user -> setId(1);
    // $auth -> createCSRFToken();
    // $auth -> deleteAccessToken();


?>