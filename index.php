<?php

    session_start();

    require './Controllers/Classes/Database.php';
    require './Controllers/Classes/Auth.php';
    require './Controllers/Classes/User.php';

    $db = new Database();
    $user = new User($db);

    if ($user -> isAccessTokenExists()) {
        $is_registred = true;
        header('Location: /pages/blogs.php');
        exit;
    } else {
        $is_registred = false;
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home Page</title>
    <?php
        include 'inc/links.php';
    ?>
</head>
<body>
    
    <?php
        include 'inc/header.php';
    ?>

</body>
</html>