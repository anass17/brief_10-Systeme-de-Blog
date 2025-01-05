<?php

    session_start();

    require '../Classes/Database.php';
    require '../Classes/User.php';
    require '../Classes/Auth.php';

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $email = isset($_POST['email']) ? $_POST['email'] : '';
        $password = isset($_POST['password']) ? $_POST['password'] : '';

        $auth = new Auth();

        // Check if access token already exist

        if ($auth -> isAccessTokenExists()) {
            header('Location: /index.php');
            exit;
        }

        $auth -> user -> setEmail($email);
        $auth -> user -> setPassword($password);

        if (!$auth -> login()) {
            $_SESSION['errors'] = $auth -> getErrors();
            header('Location: /pages/auth.php');
            exit;
        }

        header('Location: /pages/blogs.php');
        
    } else {
        header('Location: /pages/auth.php');
    }
