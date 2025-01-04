<?php

    class User {
        private int $id = 0;
        private string $first_name = '';
        private string $last_name = '';
        private string $email = '';
        private string $password = '';
        private string $role = '';
        private string $error = '';

        public function __construct() {
            
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
        public function getPassword() {
            return $this -> password;
        }
        public function getError() {
            return $this -> error;
        }

        // Setters

        public function setId(string $id) {
            if (preg_match('/^[1-9][0-9]*$/', $id) == 0) {
                $this -> error = "Id format is not valid"; 
                return false;
            }
            $this -> id = $id;
        }

        public function setFirstName(string $first_name) {
            if (strlen($first_name) < 2) {
                $this -> error = "First Name is too short"; 
                return false;
            }
            $this -> first_name = $first_name;
        }

        public function setLastName(string $last_name) {
            if (strlen($last_name) < 2) {
                $this -> error = "Last Name is too short"; 
                return false;
            }
            $this -> last_name = $last_name;
        }

        public function setEmail(string $email) {
            if (preg_match('/^[a-z.A-Z-_0-9]{3,}@[a-zA-Z.]{2,}\.[a-zA-Z]{2,}$/', $email) == 0) {
                $this -> error = "Email is not valid"; 
                return false;
            }
            $this -> email = $email;
        }

        public function setPassword(string $password) {
            if (strlen($password) < 8) {
                $this -> error = "Password must contain at least 8 characters"; 
                return false;
            }
            $this -> password = $password;
        }

        public function setRole(string $role) {
            if (!in_array($role, ["user", "admin", "super_admin"])) {
                $this -> error = "Role is not acceptable"; 
                return false;
            }
            $this -> role = $role;
        }


    }