<?php

    session_start();

    require '../Classes/Database.php';
    require '../Classes/User.php';
    require '../Classes/Auth.php';

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $first_name = isset($_POST['first-name']) ? $_POST['first-name'] : '';
        $last_name = isset($_POST['last-name']) ? $_POST['last-name'] : '';
        $email = isset($_POST['email']) ? $_POST['email'] : '';
        $password = isset($_POST['password']) ? $_POST['password'] : '';
        $confirm_password = isset($_POST['confirm-password']) ? $_POST['confirm-password'] : '';

        $auth = new Auth();

        // Check if access token already exist

        if ($auth -> isAccessTokenExists()) {
            header('Location: /index.php');
            exit;
        }

        $auth -> user -> setFirstName($first_name);
        $auth -> user -> setLastName($last_name);
        $auth -> user -> setEmail($email);
        $auth -> user -> setPassword($password);

        // If the two passwords don't match

        if ($password != $confirm_password) {
            $errors_list = $auth -> getErrors();
            array_push($errors_list, "The two passwords don't match");
            $_SESSION['errors'] = $errors_list;
            header('Location: /pages/auth.php');
            exit;
        }

        // Try to register

        if (!$auth -> register()) {
            $_SESSION['errors'] = $auth -> getErrors();
            header('Location: /pages/auth.php');
            exit;
        }

        header('Location: /pages/profile.php');
        
    } else {
        header('Location: /pages/auth.php');
    }
