<?php

    session_start();

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        require '../Classes/Database.php';
        require '../Classes/User.php';
        require '../Classes/Auth.php';
        require '../Classes/Post.php';

        $db = new Database();

        $auth = new Auth($db);

        // Check if access token does not exist

        if (!$auth -> isAccessTokenExists()) {
            header('Location: /pages/auth.php');
            exit;
        }

        $post = new Post($db);

        $CSRF_token = isset($_POST['CSRF_token']) ? $_POST['CSRF_token'] : '';
        $author_id = isset($_POST['author_id']) ? $_POST['author_id'] : '';
        $post_title = isset($_POST['post-title']) ? $_POST['post-title'] : '';
        $post_content = isset($_POST['post-content']) ? $_POST['post-content'] : '';
        $post_category = isset($_POST['post-category']) ? $_POST['post-category'] : '';
        $post_background = isset($_POST['post-background']) ? $_POST['post-background'] : '';

        // If CSRF Token is Invalid

        if (!$auth -> isCSRFTokenValid($CSRF_token)) {
            $_SESSION['errors'] = ['Invalid CSRF Token'];
            header('Location: /pages/blogs.php');
            exit;
        }

        $post -> setTitle($post_title);
        $post -> setContent($post_content);
        $post -> setAuthorId($author_id);
        $post -> setCategoryId($post_category);
        $post -> setImage($_FILES['post-background']);

        if (!$post -> createPost()) {
            $_SESSION['errors'] = $post -> getErrors();
            header('Location: /pages/blogs.php');
            exit;
        }
    }
        
    header('Location: /pages/blogs.php');
    