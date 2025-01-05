<?php

    class Auth {
        private Database $db;
        public User $user;
        private array $errors = [];

        public function __construct() {
            $this -> db = new Database();
            $this -> user = new User();
        }

        // Getter

        public function getErrors() {
            return array_merge($this -> errors, $this -> user -> getErrors());
        }

        // Method to log the user into his account

        public function login() {

            // Check if there are any errors while inserting data

            if (!empty($this -> user -> getErrors())) {
                $this -> errors = $this -> user -> getErrors();
                return false;
            }

            // Check if all the necessary data was entered

            if (
                empty($this -> user -> getEmail()) ||
                empty($this -> user -> getPassword())
            ) {
                array_push($this -> errors, "Please fill in the form");
                return false;
            }

            // Get user row, if it exists

            $data = [
                $this -> user -> getEmail()
            ];

            $result = $this -> db -> select("SELECT * from users WHERE email = ?", $data);

            if (!$result) {
                array_push($this -> errors, "Incorrect Email address or password");
                return false;
            }

            // Verify Password

            if (!password_verify($this -> user -> getPassword(), $result[0]['password'])) {
                array_push($this -> errors, "Incorrect Email address or password");
                return false;
            }

            // Create access token

            $this -> user -> setId($result[0]['user_id']);
            if (!$this -> createAccessToken()) {
                return false;
            }

            return true;
        }

        // Method to register new user

        public function register() {

            // Check if there are any errors while inserting data

            if (!empty($this -> user -> getErrors())) {
                $this -> errors = $this -> user -> getErrors();
                return false;
            }

            // Check if all the necessary data was entered

            if (
                empty($this -> user -> getFirstName()) ||
                empty($this -> user -> getLastName()) ||
                empty($this -> user -> getEmail()) ||
                empty($this -> user -> getPassword())
            ) {
                array_push($this -> errors, "Please fill in the form");
                return false;
            }

            // check if email already exists

            if ($this -> isEmailExists()) {
                array_push($this -> errors, "This email already Exists");
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

            $insert_id = $this -> db -> insert('users', $columns, $data);        // The id of inserted row, or False

            if (!$insert_id) {
                array_push($this -> errors, "Could not process your request");
                return false;
            }

            // Create access token

            $this -> user -> setId($insert_id);
            if (!$this -> createAccessToken()) {
                return false;
            }

            return true;
        }

        // Method to the user out

        public function logout() {

            if (!$this -> isAccessTokenExists()) {
                return false;
            }           

            $this -> deleteAccessToken();

            session_destroy();
        }

        // Method to create/update Access Token

        public function createAccessToken() {

            // Id property must not be empty

            if (empty($this -> user -> getId())) {
                array_push($this -> errors, "Invalid user ID");
                return false;
            }

            // Set the token and its expiration time 

            date_default_timezone_set('Etc/GMT-1');         // Set timezone to UTC + 1
            $token = bin2hex(random_bytes(32));
            $token_expiration = time() + 20 * 60;
            $token_expiration_formated = date('Y-m-d H:i:s', $token_expiration);

            // Store token in the database

            if (!$this -> db -> update('users', ['token', 'token_expiration'], [$token, $token_expiration_formated], 'user_id = ' . $this -> user -> getId())) {
                array_push($this -> errors, "Could not assign a token");
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

            $result = $this -> db -> selectOne("SELECT * FROM users WHERE user_id = ? and token = ? and token_expiration > Current_timestamp", [$cookie_params[0], $cookie_params[1]]);

            if (!$result) {
                return false;
            }

            // Set User Data

            $this -> user -> setId($cookie_params[0]);
            $this -> user -> setFirstName($result['first_name']);
            $this -> user -> setLastName($result['last_name']);
            $this -> user -> setEmail($result['email']);
            
            // Update Token

            $this -> createAccessToken();

            return true;
        }

        // Method to delete Access Token
        
        public function deleteAccessToken() {

            // Remove token from database

            if (!$this -> db -> update('users', ['token', 'token_expiration'], ['', null], 'user_id = ' . $this -> user -> getId())) {
                array_push($this -> errors, "Could not delete access token");
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

        // Method to check if a CSRF token is valid

        public function isCSRFTokenValid(string $token) {
            if (isset($_SESSION['CSRF_token']) && $_SESSION["CSRF_token"] == $token) {
                return true;
            }

            return false;
        }

        // Method to check if an email exist

        public function isEmailExists() {
            if (empty($this -> user -> getEmail())) {
                return false;
            }

            $result = $this -> db -> select("SELECT * from users WHERE email = ?", [$this -> user -> getEmail()]);

            // If email exists, return user ID

            if ($result) {
                return $result[0]['user_id'];
            }

            return false;
            
        }

        public function insertVisitor() {

        }

    }

?>