<?php

    session_start();

    require '../Classes/Database.php';
    require '../Classes/Auth.php';
    require '../Classes/User.php';

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $CSRF_token = isset($_POST['CSRF_token']) ? $_POST['CSRF_token'] : '';
        $email = isset($_POST['email']) ? $_POST['email'] : '';
        $password = isset($_POST['password']) ? $_POST['password'] : '';

        $db = new Database();
        $user = new User($db);

        // Check if access token already exist

        if ($user -> isAccessTokenExists()) {
            header('Location: /index.php');
            exit;
        }

        // If CSRF Token is Invalid

        if (!$user -> isCSRFTokenValid($CSRF_token)) {
            $_SESSION['errors'] = ['Invalid CSRF Token'];
            header('Location: /pages/auth.php');
            exit;
        }

        $user -> setEmail($email);
        $user -> setPassword($password);

        if (!$user -> login()) {
            $_SESSION['errors'] = $user -> getErrors();
            header('Location: /pages/auth.php');
            exit;
        }

        header('Location: /pages/blogs.php');
        
    } else {
        header('Location: /pages/auth.php');
    }
