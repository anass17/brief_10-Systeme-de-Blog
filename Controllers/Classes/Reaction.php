<?php

    class Reaction {
        private int $user_id;
        private string $type;
        private array $errors = [];
        private Database $db;

        public function __construct(Database $db) {
            $this -> db = $db;
        }

        public function setUserId(string $id) : bool {
            if (preg_match('/^[1-9][0-9]*$/', $id) == 0) {
                array_push($this -> errors, "Invalid ID");
                return false;
            }

            $this -> user_id = $id - 0;
            return true;
        }

        public function setType(string $type) : bool {
            if ($type != "Like" && $type != "Dislike") {
                array_push($this -> errors, "Invalid Reaction");
                return false;
            }

            $this -> type = $type;
            return true;
        }
        
        public function getUserId() {
            return $this -> user_id;
        }

        public function getType() {
            return $this -> type;
        }

        public function getErrors() {
            return $this -> errors;
        }

        // Method to add a new reaction

        public function removeReaction($user_id, $post_id) : bool {

            $data = [
                $user_id,
                $post_id
            ];

            if (!$this -> db -> delete('reactions', "user_id = ? and post_id = ?", $data)) {
                array_push($this -> errors, "Could not save your changes");
                return false;
            }
            return true;
        }

        public function updateReaction($type, $user_id, $post_id) : bool {

            $columns = [
                'type'
            ];

            if (!$this -> db -> update('reactions', $columns, [$type], "user_id = ? and post_id = ?", [$user_id, $post_id])) {
                array_push($this -> errors, "Could not save your changes");
                return false;
            }
            return true;
        }
    }