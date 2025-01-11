<?php

    class Tag {
        private int $id = 0;
        private string $name = '';
        private string $error = '';
        private Database $db;

        public function __construct(Database $db, int $id = 0) {
            $this -> db = $db;
            if ($id != 0) {
                $this -> setTagData($id);
            }

        }

        // Setters

        public function setId($id) {
            if (preg_match('/^[1-9][0-9]*$/', $id) == 0) {
                $this -> error = "Invalid ID";
                return false;
            }

            $this -> id = $id;

            return true;
        }

        public function setName(string $name) {
            if (strlen($name) < 2) {
                $this -> error = "Tag name is too short";
                return false;
            }

            $this -> name = $name;

            return true;
        }

        // Getters

        public function getId() : int {
            return $this -> id;   
        }

        public function getName() : string {
            return htmlspecialchars($this -> name);
        }

        public function getError() {
            return $this -> error;
        }

        // Method to get all tags

        public function getAllTags() : array {
            return $this -> db -> select("SELECT * from tags");
        }

        public function setTagData($id) {
            if (!$this -> setId($id)) {
                return false;
            }
            $row = $this -> db -> selectOne("SELECT * from tags WHERE tag_id = ?", [$this -> getId()]);
            $this -> name = $row["tag_name"];
        }

    }