<?php

    class Admin extends Auth {

        public function __construct($db) {
            parent::__construct($db);

            $this -> isAccessTokenExists();

            if ($this -> role != "admin") {
                header('Location: /pages/blogs.php');
                exit;
            }
        }

        // Load Users
        
        public function loadUsers() {
            return $this -> db -> select("SELECT first_name, last_name, email, register_date FROM users order by register_date DESC");
        }

        // Load Tags with how many posts they were used in

        public function loadTags() {
            return $this -> db -> select("SELECT tag_name, count(pt.tag_id) as count FROM tags t left join post_tags pt on t.tag_id = pt.tag_id group by tag_name;");
        }

        // Get the number of: users, posts, comments and likes

        public function getStatistics() {
            return $this -> db -> selectOne("select (select count(first_name) from users) as users_count, 
            (select count(tag_name) from tags) as tags_count,
            (select count(comment_id) from comments) as comments_count,
            (select count(type) from reactions where type = 'Like') as likes_count,
            count(title) as posts_count 
            from posts;");
        }

        // Get the number of {users, comments and posts} for each day during the last 30 days

        public function get30DaysStatistics() {

            $users = $this -> db -> select("SELECT DATE(register_date) AS date,
                    DATEDIFF(CURDATE(), DATE(register_date)) AS diff,
                    COUNT(*) AS count
                    FROM users
                    WHERE register_date >= CURDATE() - INTERVAL 30 DAY
                    GROUP BY DATE(register_date), diff
                    ORDER BY DATE(register_date) DESC");

            $posts = $this -> db -> select("SELECT DATE(publish_date) AS date,
                    DATEDIFF(CURDATE(), DATE(publish_date)) AS diff,
                    COUNT(*) AS count
                    FROM posts
                    WHERE publish_date >= CURDATE() - INTERVAL 30 DAY
                    GROUP BY DATE(publish_date), diff
                    ORDER BY DATE(publish_date) DESC");

            $comments = $this -> db -> select("SELECT DATE(publish_date) AS date,
                    DATEDIFF(CURDATE(), DATE(publish_date)) AS diff,
                    COUNT(*) AS count
                    FROM comments
                    WHERE publish_date >= CURDATE() - INTERVAL 30 DAY
                    GROUP BY DATE(publish_date), diff
                    ORDER BY DATE(publish_date) DESC");

            $users_30days = array_fill(0, 30, 0);
            $posts_30days = array_fill(0, 30, 0);
            $comments_30days = array_fill(0, 30, 0);

            foreach($users as $user) {
                $users_30days[$user['diff']] = $user['count'];
            }
            foreach($posts as $post) {
                $posts_30days[$post['diff']] = $post['count'];
            }
            foreach($comments as $comment) {
                $comments_30days[$comment['diff']] = $comment['count'];
            }

            return [
                'users' => $users_30days,
                'posts' => $posts_30days,
                'comments' => $comments_30days
            ];
            
        }
    }