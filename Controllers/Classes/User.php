<?php

    class User extends Auth {

        // Method to register new user

        public function register() {

            // Check if there are any errors while inserting data

            if (!empty($this -> errors)) {
                $this -> errors = $this -> errors;
                return false;
            }

            // Check if all the necessary data was entered

            if (
                empty($this -> first_name) ||
                empty($this -> last_name) ||
                empty($this -> email) ||
                empty($this -> password)
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
                $this -> first_name,
                $this -> last_name,
                $this -> email,
                password_hash($this -> password, PASSWORD_BCRYPT)
            ];

            $insert_id = $this -> db -> insert('users', $columns, $data);        // The id of inserted row, or False

            if (!$insert_id) {
                array_push($this -> errors, "Could not process your request");
                return false;
            }

            // Create access token

            $this -> id = $insert_id;

            if (!$this -> createAccessToken()) {
                return false;
            }

            return true;
        }


    }