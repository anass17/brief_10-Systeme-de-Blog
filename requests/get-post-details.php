<?php

    if ($_SERVER["REQUEST_METHOD"] == "POST") {

        $content = file_get_contents('php://input');

        $data = json_decode($content);

        require '../Controllers/Classes/Database.php';
        require '../Controllers/Classes/User.php';
        require '../Controllers/Classes/Auth.php';
        require '../Controllers/Classes/Post.php';

        $db = new Database();
        $auth = new Auth($db);

        // Check if access token does not exist

        if (!$auth -> isAccessTokenExists()) {
            echo json_encode([]);
            exit;
        }

        $result = $auth -> db -> selectOne("SELECT * FROM posts where post_id = ?", [$data -> id]);

        echo json_encode($result);
    }