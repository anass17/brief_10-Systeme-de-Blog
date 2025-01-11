<?php
    session_start();

    require '../Controllers/Classes/Database.php';
    require '../Controllers/Classes/Auth.php';
    require '../Controllers/Classes/User.php';

    $db = new Database();
    $user = new User($db);

    $user -> logout();

    header('Location: /pages/auth.php');
?>