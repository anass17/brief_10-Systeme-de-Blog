<?php

    class Post {
        private int $id;
        private string $title;
        private string $content;
        private int $author_id;
        private string $publish_date;
        private array $image;
        private string $image_url;
        private Database $db;
        private array $errors = [];
        public array $comments = [];
        public array $reactions = [];
        public array $tags = [];


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

        public function setAuthorId(string $id) : bool {
            if (preg_match('/^[1-9][0-9]*$/', $id) == 0) {
                array_push($this -> errors, "Invalid Author ID");
                return false;
            }
            $this -> author_id = (int) $id;

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
                empty($this -> author_id)
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
                'post_author'
            ];

            $data = [
                $this -> title,
                $this -> content,
                $image_path,
                $this -> author_id,
            ];

            $inserted_id = $this -> db -> insert('posts', $columns, $data);

            if (!$inserted_id) {
                array_push($this -> errors, "Could not save your changes");
                return false;
            }

            $this -> id = $inserted_id;

            return true;
        }

        // Method to update post

        public function updatePost() {
            if (!empty($this -> errors)) {
                return false;
            }

            if (
                empty($this -> id) ||
                empty($this -> title) ||
                empty($this -> content)
            ) {
                array_push($this -> errors, "Could not process your request");
                return false;
            }

            $columns = [
                'title',
                'content'
            ];

            $data = [
                $this -> title,
                $this -> content
            ];

            if (!$this -> db -> update('posts', $columns, $data, 'post_id = ?', [$this -> id])) {
                array_push($this -> errors, "Could not save your changes");
                return false;
            }

            $this -> db -> delete('post_tags', 'post_id = ?', [$this -> id]);

            return true;
        }

        // Method to delete a post

        public function deletePost() {
            if (!empty($this -> errors)) {
                return false;
            }

            if (
                empty($this -> id)
            ) {
                array_push($this -> errors, "Could not process your request");
                return false;
            }

            $data = [
                $this -> id,
            ];

            if (!$this -> db -> delete('posts', 'post_id = ?', $data)) {
                array_push($this -> errors, "Could not save your changes");
                return false;
            }

            return true;
        }

        public function getAllPosts() {
            $posts = $this -> db -> select("SELECT * from posts join users on users.user_id = posts.post_author ORDER BY post_id DESC");
            
            // Get tags of each post

            $tags_groups = [];

            foreach($posts as $post) {
                $tags = $this -> db -> select("SELECT * FROM post_tags join tags on tags.tag_id = post_tags.tag_id WHERE post_id = ?", [$post['post_id']]);
            
                array_push($tags_groups, $tags);
            }

            return [$posts, $tags_groups];
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

        // Method to add a new reaction

        public function addReaction(string $type, $user_id) : bool {
            $reaction = new reaction($this -> db);

            $reaction -> setType($type);
            $reaction -> setUserId($user_id);

            if (!empty($reaction -> getErrors())) {
                array_push($this -> errors, "Could not add the reaction");
                return false;
            }

            if (
                empty($this -> id) ||
                empty($reaction -> getType()) ||
                empty($reaction -> getUserId())
            ) {
                array_push($this -> errors, "Could not process your request");
                return false;
            }

            $columns = [
                'user_id',
                'post_id',
                'type'
            ];

            $data = [
                $reaction -> getUserId(),
                $this -> id,
                $reaction -> getType()
            ];

            if ($this -> db -> insert('reactions', $columns, $data) === false) {
                array_push($this -> errors, "Could not save your changes");
                return false;
            }

            array_push($this -> reactions, $reaction);

            return true;
        }

        public function getAllReactions(string $type = '') {

            $query = "";

            if ($type == "Like") {
                $query = "and type = 'Like'";
            } else if ($type == "Dislike") {
                $query = "and type = 'Dislike'";
            }

            return $this -> db -> select("SELECT * from reactions join users on users.user_id = reactions.user_id WHERE post_id = ? $query", [$this -> getId()]);
        }

        public function addTag(Tag $tag) {

            if (empty($tag -> getId())) {
                return false;
            }

            if (!$this -> db -> insert('post_tags', ['post_id', 'tag_id'], [$this -> getId(), $tag -> getId()])) {
                return false;
            }

            array_push($this -> tags, $tag);
        }

        public function filterPost(string $search, array $tags) {

            if (count($tags) > 0) {
    
                $placeholders = implode('|', $tags);
    
                return $this -> db -> select("SELECT p.post_id, title, first_name, last_name, post_image_url as image, GROUP_CONCAT(t.tag_name ORDER BY t.tag_id) AS tags FROM posts p JOIN users u ON u.user_id = p.post_author JOIN post_tags pt ON p.post_id = pt.post_id JOIN tags t ON pt.tag_id = t.tag_id WHERE p.title LIKE CONCAT('%', ?, '%') OR first_name LIKE CONCAT('%', ?, '%') group by p.post_id having GROUP_CONCAT(t.tag_id) REGEXP '({$placeholders})(,|$)'", [$search, $search]);
            
            }

            return $this -> db -> select("SELECT p.post_id, title, first_name, last_name, post_image_url as image, GROUP_CONCAT(t.tag_name ORDER BY t.tag_id) AS tags FROM posts p JOIN users u ON u.user_id = p.post_author JOIN post_tags pt ON p.post_id = pt.post_id JOIN tags t ON pt.tag_id = t.tag_id WHERE p.title LIKE CONCAT('%', ?, '%') OR first_name LIKE CONCAT('%', ?, '%') GROUP BY p.post_id", [$search, $search]);

        }
    }

