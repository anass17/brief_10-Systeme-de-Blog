<?php
    session_start();

    require '../Controllers/Classes/Database.php';
    require '../Controllers/Classes/User.php';
    require '../Controllers/Classes/Auth.php';

    $db = new Database();
    $auth = new Auth($db);

    $auth -> logout();

    header('Location: /pages/auth.php');
?>