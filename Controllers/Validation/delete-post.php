<?php

    session_start();

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        require '../Classes/Database.php';
        require '../Classes/Auth.php';
        require '../Classes/User.php';
        require '../Classes/Post.php';

        $db = new Database();

        $user = new User($db);

        // Check if access token does not exist

        if (!$user -> isAccessTokenExists()) {
            header('Location: /pages/auth.php');
            exit;
        }

        $post = new Post($db);

        $post_id = isset($_POST['post_id']) ? $_POST['post_id'] : '';

        $post -> getPostData($post_id);

        if ($post -> getAuthorID() != $user -> getId()) {
            $_SESSION['errors'] = ["You are not authorized to perform this action"];
            header('Location: /pages/post.php?id=' . $post_id);
            exit;
        }


        if (!$post -> deletePost()) {
            $_SESSION['errors'] = $post -> getErrors();
            header('Location: /pages/post.php?id=' . $post_id);
            exit;
        }
    }
        
    header('Location: /pages/blogs.php');
    