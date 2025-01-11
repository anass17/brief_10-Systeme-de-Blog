<?php

    session_start();

    require '../Classes/Database.php';
    require '../Classes/Auth.php';
    require '../Classes/User.php';

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $CSRF_token = isset($_POST['CSRF_token']) ? $_POST['CSRF_token'] : '';
        $first_name = isset($_POST['first-name']) ? $_POST['first-name'] : '';
        $last_name = isset($_POST['last-name']) ? $_POST['last-name'] : '';
        $email = isset($_POST['email']) ? $_POST['email'] : '';
        $password = isset($_POST['password']) ? $_POST['password'] : '';
        $confirm_password = isset($_POST['confirm-password']) ? $_POST['confirm-password'] : '';

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
            header('Location: /pages/auth.php?to=register');
            exit;
        }

        $user -> setFirstName($first_name);
        $user -> setLastName($last_name);
        $user -> setEmail($email);
        $user -> setPassword($password);

        // If the two passwords don't match

        if ($password != $confirm_password) {
            $errors_list = $user -> getErrors();
            array_push($errors_list, "The two passwords are different");
            $_SESSION['errors'] = $errors_list;
            header('Location: /pages/auth.php?to=register');
            exit;
        }

        // Try to register

        if (!$user -> register()) {
            $_SESSION['errors'] = $user -> getErrors();
            header('Location: /pages/auth.php?to=register');
            exit;
        }

        header('Location: /pages/blogs.php');
        
    } else {
        header('Location: /pages/auth.php?to=register');
    }
