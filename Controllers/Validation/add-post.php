<?php

    session_start();

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        require '../Classes/Database.php';
        require '../Classes/Auth.php';
        require '../Classes/User.php';
        require '../Classes/Post.php';
        require '../Classes/Tag.php';

        $db = new Database();

        $user = new User($db);

        // Check if access token does not exist

        if (!$user -> isAccessTokenExists()) {
            header('Location: /pages/auth.php');
            exit;
        }

        $post = new Post($db);

        $CSRF_token = isset($_POST['CSRF_token']) ? $_POST['CSRF_token'] : '';
        $author_id = isset($_POST['author_id']) ? $_POST['author_id'] : '';
        $post_title = isset($_POST['post-title']) ? $_POST['post-title'] : '';
        $post_content = isset($_POST['post-content']) ? $_POST['post-content'] : '';
        $post_tags = isset($_POST['post-tags']) ? $_POST['post-tags'] : '';
        $post_background = isset($_POST['post-background']) ? $_POST['post-background'] : '';

        // If CSRF Token is Invalid

        if (!$user -> isCSRFTokenValid($CSRF_token)) {
            $_SESSION['errors'] = ['Invalid CSRF Token'];
            header('Location: /pages/blogs.php');
            exit;
        }

        $user -> deleteCSRFToken();

        // Split tags

        $tags_list = explode(',', rtrim($post_tags, ','));

        $post -> setTitle($post_title);
        $post -> setContent($post_content);
        $post -> setAuthorId($author_id);
        $post -> setImage($_FILES['post-background']);

        // If post wasn't created, exit
    
        if (!$post -> createPost()) {
            $_SESSION['errors'] = $post -> getErrors();
            header('Location: /pages/blogs.php');
            exit;
        }

        // Insert tags

        foreach($tags_list as $tag_item) {
            $tag = new Tag($db, $tag_item);
            $post -> addTag($tag);
        }

        header('Location: /pages/post.php?id=' . $post -> getId());
        exit;
    }
    
    header('Location: /pages/blogs.php');
    