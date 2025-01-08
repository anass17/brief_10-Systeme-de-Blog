<?php

    class Comment {
        private int $id;
        private string $content = '';
        private string $publish_date;
        private int $author_id;
        private array $errors = [];
        private Database $db;

        public function __construct(Database $db) {
            $this -> db = $db;
        }

        // Getters

        public function getId() {
            return $this -> id;
        }
        public function getContent() {
            return htmlspecialchars($this -> content);
        }
        public function getPublish() {
            return htmlspecialchars($this -> publish_date);
        }
        public function getAuthorId() {
            return $this -> author_id;
        }
        public function getErrors() {
            return $this -> errors;
        }

        // Setters

        public function setId(string $id) : bool {
            if (preg_match('/^[1-9][0-9]*$/', $id) == 0) {
                array_push($this -> errors, "Invalid Comment ID");
                return false;
            }

            $this -> id = $id;
            return true;
        }
        public function setContent(string $content) : bool {
            if (strlen($content) < 5) {
                array_push($this -> errors, "Content is too short");
                return false;
            }

            $this -> content = $content;
            return true;
        }
        public function setPublishDate(string $publish_date) : bool {

            $this -> publish_date = $publish_date;
            return true;
        }
        public function setAuthorId(string $author_id) : bool {
            if (preg_match('/^[1-9][0-9]*$/', $author_id) == 0) {
                array_push($this -> errors, "Invalid Comment Author ID");
                return false;
            }

            $this -> author_id = $author_id;
            return true;
        }

        // Methods

        public function deleteComment() {

            if (!empty($this -> getErrors())) {
                array_push($this -> errors, "Please fill in the form");
                return false;
            }

            if (
                empty($this -> id)
            ) {
                array_push($this -> errors, "Could not process your request");
                return false;
            }

            if (!$this -> db -> delete('comments', 'comment_id = ?', [$this -> id])) {
                array_push($this -> errors, "Could not save your changes");
                return false;
            }

            return true;
        }

        public function updateComment() {

            if (!empty($this -> getErrors())) {
                array_push($this -> errors, "Please fill in the form");
                return false;
            }

            if (
                empty($this -> id) ||
                empty($this -> content)
            ) {
                array_push($this -> errors, "Could not process your request");
                return false;
            }

            if (!$this -> db -> update('comments', ['content'], [$this -> content], 'comment_id = ?', [$this -> id])) {
                array_push($this -> errors, "Could not save your changes");
                return false;
            }

            return true;
        }

    }