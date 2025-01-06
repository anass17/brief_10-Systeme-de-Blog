<?php

    session_start();

    require './Controllers/Classes/Database.php';
    require './Controllers/Classes/User.php';
    require './Controllers/Classes/Auth.php';

    $db = new Database();
    $auth = new Auth($db);

    if ($auth -> isAccessTokenExists()) {
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