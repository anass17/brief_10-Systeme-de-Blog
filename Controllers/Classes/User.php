<?php

    class User {
        private int $id = 0;
        private string $first_name = '';
        private string $last_name = '';
        private string $email = '';
        private string $password = '';
        private string $register_date = '';
        private string $role = '';
        private array $errors = [];
        private Database|null $db;

        public function __construct(Database|null $db = null) {
            $this -> db = $db;
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
        public function getRole() {
            return htmlspecialchars($this -> role);
        }
        public function getRegisterDate() {
            return htmlspecialchars($this -> register_date);
        }
        public function getPassword() {
            return $this -> password;
        }
        public function getErrors() {
            return $this -> errors;
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

        public function setRole(string $role) {
            if (!in_array($role, ["user", "admin", "super_admin"])) {
                array_push($this -> errors, "Role is not acceptable");
                return false;
            }
            $this -> role = $role;
        }

        public function setRegisterDate(string $date) {
            $this -> register_date = $date;
        }

        // Methods

        public function getUserData() {
            $result = $this -> db -> selectOne('SELECT * FROM users WHERE user_id = ?', [$this -> getId()]);

            if ($result) {
                $this -> setFirstName($result["first_name"]);
                $this -> setLastName($result["last_name"]);
                $this -> setEmail($result["email"]);
                $this -> setRegisterDate($result["register_date"]);
            }

        }


    }