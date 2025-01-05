<?php
    session_start();

    require '../Controllers/Classes/Database.php';
    require '../Controllers/Classes/User.php';
    require '../Controllers/Classes/Auth.php';

    $auth = new Auth();

    $auth -> logout();

    header('Location: /pages/auth.php');
?>