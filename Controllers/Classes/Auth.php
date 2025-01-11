<?php

    abstract class Auth {
        protected int $id = 0;
        protected string $first_name = '';
        protected string $last_name = '';
        protected string $email = '';
        protected string $password = '';
        protected string $image = '';
        protected string $role = '';
        protected string $register_date = '';
        protected Database $db;
        protected array $errors = [];

        public function __construct(Database $db) {
            $this -> db = $db;
        }

        // Getters

        public function getErrors() {
            return $this -> errors;
        }

        // Getters

        public function getId() {
            return htmlspecialchars($this -> id);
        }
        public function getFirstName() {
            return htmlspecialchars($this -> first_name);
        }
        public function getLastName() {
            return htmlspecialchars($this -> last_name);
        }
        public function getEmail() {
            return htmlspecialchars($this -> email);
        }
        public function getRegisterDate() {
            return htmlspecialchars($this -> register_date);
        }
        public function getPassword() {
            return $this -> password;
        }
        public function getRole() {
            return $this -> role;
        }

        public function getImageUrl() {
            if ($this -> image == "") {
                return "/assets/imgs/users/default.webp";
            }

            return $this -> image;
        }

        // Setters

        public function setId(string $id) {
            if (preg_match('/^[1-9][0-9]*$/', $id) == 0) {
                array_push($this -> errors, "Id format is not valid");
                return false;
            }
            $this -> id = $id;
        }

        public function setFirstName(string $first_name) {
            if (strlen($first_name) < 2) {
                array_push($this -> errors, "First Name is too short");
                return false;
            }
            $this -> first_name = $first_name;
        }

        public function setLastName(string $last_name) {
            if (strlen($last_name) < 2) {
                array_push($this -> errors, "Last Name is too short");
                return false;
            }
            $this -> last_name = $last_name;
        }

        public function setEmail(string $email) {
            if (preg_match('/^[a-z.A-Z-_0-9]{3,}@[a-zA-Z.]{2,}\.[a-zA-Z]{2,}$/', $email) == 0) {
                array_push($this -> errors, "Email is not valid");
                return false;
            }
            $this -> email = $email;
        }

        public function setPassword(string $password) {
            if (strlen($password) < 8) {
                array_push($this -> errors, "Password must contain at least 8 characters");
                return false;
            }
            $this -> password = $password;
        }

        public function setRegisterDate(string $date) {
            $this -> register_date = $date;
        }

        // Method to log the user into his account

        public function login() {

            // Check if there are any errors while inserting data

            if (!empty($this -> errors)) {
                $this -> errors = $this -> errors;
                return false;
            }

            // Check if all the necessary data was entered

            if (
                empty($this -> email) ||
                empty($this -> password)
            ) {
                array_push($this -> errors, "Please fill in the form");
                return false;
            }

            // Get user row, if it exists

            $data = [
                $this -> email
            ];

            $result = $this -> db -> selectOne("SELECT * from users WHERE email = ?", $data);

            if (!$result) {
                array_push($this -> errors, "Incorrect Email address or password");
                return false;
            }

            // Verify Password

            if (!password_verify($this -> password, $result['password'])) {
                array_push($this -> errors, "Incorrect Email address or password");
                return false;
            }

            // Create access token

            $this -> id = $result['user_id'];

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

            if (empty($this -> id)) {
                array_push($this -> errors, "Invalid user ID");
                return false;
            }

            // Set the token and its expiration time 

            date_default_timezone_set('Etc/GMT-1');         // Set timezone to UTC + 1
            $token = bin2hex(random_bytes(32));
            $token_expiration = time() + 20 * 60;
            $token_expiration_formated = date('Y-m-d H:i:s', $token_expiration);

            // Store token in the database

            if (!$this -> db -> update('users', ['token', 'token_expiration'], [$token, $token_expiration_formated], 'user_id = ' . $this -> id)) {
                array_push($this -> errors, "Could not assign a token");
                return false;
            }

            // Store token in a cookie

            $cookie_value = $this -> id . '.' . $token;            // In the format "ID.token"
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

            $this -> id = $cookie_params[0];
            $this -> first_name = $result['first_name'];
            $this -> last_name = $result['last_name'];
            $this -> email = $result['email'];
            $this -> image = $result['image_url'];
            $this -> role = $result['role'];
            
            // Update Token

            $this -> createAccessToken();

            return true;
        }

        // Method to delete Access Token
        
        public function deleteAccessToken() {

            // Remove token from database

            if (!$this -> db -> update('users', ['token', 'token_expiration'], ['', null], 'user_id = ' . $this -> id)) {
                array_push($this -> errors, "Could not delete access token");
                return false;
            }

            // Remove token cookie

            setcookie('token', '', time() - 0, '/', 'localhost', true, true);

            return true;
        }

        // Method to create CSRF Token

        public function createCSRFToken() {
            if(!isset($_SESSION['CSRF_token'])){
                $csrf_token = bin2hex(random_bytes(32));
                $_SESSION['CSRF_token'] = $csrf_token;
            }
        }

        // Method to delete CSRF Token

        public function deleteCSRFToken() {
            if(isset($_SESSION['CSRF_token'])){
                unset( $_SESSION['CSRF_token']);
            }
        }

        // Method to check if a CSRF token is valid

        public function isCSRFTokenValid(string $token) {
            if (isset($_SESSION['CSRF_token']) && trim($_SESSION["CSRF_token"]) == trim($token)) {
                return true;
            }

            return false;
        }

        // Method to check if an email exist

        public function isEmailExists() {
            if (empty($this -> email)) {
                return false;
            }

            $result = $this -> db -> select("SELECT * from users WHERE email = ?", [$this -> email]);

            // If email exists, return user ID

            if ($result) {
                return $result[0]['user_id'];
            }

            return false;
            
        }

        // Method to set User Data

        public function setUserData() {
            $result = $this -> db -> selectOne('SELECT * FROM users WHERE user_id = ?', [$this -> getId()]);

            if ($result) {
                $this -> first_name = $result["first_name"];
                $this -> last_name = $result["last_name"];
                $this -> email = $result["email"];
                $this -> register_date = $result["register_date"];
                $this -> image = $result["image_url"];
                $this -> role = $result["role"];
            }
        }

    }

?>