<?php

    class Post {
        private int $id;
        private string $title;
        private string $content;
        private int $author_id;
        private int $category_id;
        private string $publish_date;
        private array $image;
        private string $image_url;
        private Database $db;
        private array $errors = [];
        public array $comments = [];


        public function __construct(Database $db) {
            $this -> db = $db;
        }

        // Getters

        public function getId() {
            return htmlspecialchars($this -> id);
        }
        public function getTitle() {
            return htmlspecialchars($this -> title);
        }
        public function getContent() {
            return htmlspecialchars($this -> content);
        }
        public function getAuthorID() {
            return htmlspecialchars($this -> author_id);
        }
        public function getCategoryID() {
            return htmlspecialchars($this -> category_id);
        }
        public function getPublishDate() {
            return htmlspecialchars($this -> publish_date);
        }
        public function getImageUrl() {
            return htmlspecialchars($this -> image_url);
        }
        public function getErrors() {
            return $this -> errors;
        }

        // Setters

        public function setId(string|int $id) : bool {
            if (preg_match('/^[1-9][0-9]*$/', $id) == 0) {
                array_push($this -> errors, "Invalid Title ID");
                return false;
            }
            $this -> id = (int) $id;

            return true;
        }

        public function setAuthorId(string|int $id) : bool {
            if (preg_match('/^[1-9][0-9]*$/', $id) == 0) {
                array_push($this -> errors, "Invalid Author ID");
                return false;
            }
            $this -> author_id = (int) $id;

            return true;
        }

        public function setCategoryId(string|int $id) : bool {
            if (preg_match('/^[1-9][0-9]*$/', $id) == 0) {
                array_push($this -> errors, "Invalid Category ID");
                return false;
            }
            $this -> category_id = (int) $id;

            return true;
        }

        public function setTitle(string $title) : bool {
            if (strlen($title) < 5) {
                array_push($this -> errors, "Title is too short");
                return false;
            }

            $this -> title = ucwords($title);

            return true;
        }

        public function setContent(string $content) : bool {
            if (strlen($content) < 10) {
                array_push($this -> errors, "Content is too short");
                return false;
            }

            $this -> content = $content;

            return true;
        }

        public function setImage(array $file) : bool {

            if ($file['error'] != 0) {      // Image not uploaded
                return false;
            }

            $allowed_types = ["image/jpeg", "image/png", "image/webp"];

            if (!in_array($file["type"], $allowed_types)) {
                array_push($this -> errors, "The type of the file is not allowed");
                return false;
            }

            $this -> image = $file;

            return true;
        }

        public function setImageUrl($url) : bool {
            if ($url != "") {
                $this -> image_url = $url;
                return true;
            }

            $this -> image_url = "/assets/imgs/blogs/placeholder.jpg";
            return true;
        }

        public function setPublishDate(string $date) {
            $this -> publish_date = $date;
        }

        // Methods

        public function createPost() {
            if (!empty($this -> errors)) {
                return false;
            }

            if (
                empty($this -> title) ||
                empty($this -> content) ||
                empty($this -> author_id) ||
                empty($this -> category_id)
            ) {
                array_push($this -> errors, "Could not process your request");
                return false;
            }

            $image_path = "";

            if (!empty($this -> image)) {
                $image_name = uniqid() . $this -> image["name"];
                $new_path = "../../assets/imgs/blogs/" . $image_name;
                $image_path = "/assets/imgs/blogs/" . $image_name;
                
                if (!move_uploaded_file($this -> image['tmp_name'], $new_path)) {
                    array_push($this -> errors, "Could not upload the file.");
                    return false;
                }

            }

            $columns = [
                'title',
                'content',
                'post_image_url',
                'post_author',
                'post_cat'
            ];

            $data = [
                $this -> title,
                $this -> content,
                $image_path,
                $this -> author_id,
                $this -> category_id
            ];

            if (!$this -> db -> insert('posts', $columns, $data)) {
                array_push($this -> errors, "Could not save your changes");
                return false;
            }

            return true;
        }

        public function getAllPosts() {
            return $this -> db -> select("SELECT * from posts join users on users.user_id = posts.post_author");
        }

        public function getPostData(string $id) {
            if (preg_match('/^[0-9][1-9]*$/', $id) == 0) {
                return false;
            }
            $result = $this -> db -> selectOne("SELECT * FROM posts Where post_id = ?", [$id]);

            $this -> setId($result['post_id']);
            $this -> setTitle($result['title']);
            $this -> setContent($result['content']);
            $this -> setImageUrl($result['post_image_url']);
            $this -> setAuthorId($result['post_author']);
            // $this -> setCategoryId($result['post_cat']);
            $this -> setPublishDate($result['publish_date']);

            return true;
        }

        // Method to create a comment

        public function createComment($content, $author_id) {

            $new_comment = new Comment($this -> db);

            $new_comment -> setContent($content);
            $new_comment -> setAuthorId($author_id);

            if (!empty($new_comment -> getErrors())) {
                array_push($this -> errors, "Please fill in the form");
                return false;
            }

            if (
                empty($this -> id)
            ) {
                array_push($this -> errors, "Could not process your request");
                return false;
            }

            $columns = [
                'content',
                'comment_author',
                'comment_post'
            ];

            $data = [
                $content,
                $new_comment -> getAuthorID(),
                $this -> getId()
            ];

            if (!$this -> db -> insert('comments', $columns, $data)) {
                array_push($this -> errors, "Could not save your changes");
                return false;
            }

            array_push($this -> comments, $new_comment);

            return true;
        }

        // Method to get all comments

        public function getAllComments() {
            return $this -> db -> select("SELECT * from comments join users on users.user_id = comments.comment_author WHERE comment_post = ? ORDER BY comment_id", [$this -> getId()]);
        }

        // Method to get all comments

        public function getAllTags() {
            return $this -> db -> select("SELECT * from post_tags join tags on tags.tag_id = post_tags.tag_id WHERE post_id = ?", [$this -> getId()]);
        }
    }