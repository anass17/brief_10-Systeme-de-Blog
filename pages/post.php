<?php

    session_start();

    require '../Controllers/Classes/Database.php';
    require '../Controllers/Classes/User.php';
    require '../Controllers/Classes/Auth.php';
    require '../Controllers/Classes/Post.php';
    require '../Controllers/Classes/Helpers.php';

    $db = new Database();
    $helpers = new Helpers();
    $auth = new Auth($db);
    $post = new Post($db);

    if ($auth -> isAccessTokenExists()) {
        $is_registred = true;
    } else {
        $is_registred = false;
    }

    $post_id = isset($_GET['id']) ? $_GET['id'] : '';

    if (!$post -> getPostData($post_id)) {
        header('Location: /pages/blogs.php');
        exit;
    }

    $author = new User($db);

    $author -> setId($post -> getAuthorID());
    $author -> getUserData();

    
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home Page</title>
    <?php
        include '../inc/links.php';
    ?>
</head>
<body>
    
    <?php
        include '../inc/header.php';
    ?>

    <div class="flex">

        <!-- Side Menu -->

        <div class="min-h-screen w-[25%] bg-gray-700 border-r border-gray-200 shadow-md text-white px-5 py-10">
            <div class="text-center">
                <a href="#" class="rounded-full w-14 h-14 overflow-hidden block mx-auto mb-3 border-2 border-white">
                    <img src="/assets/imgs/users/default.webp">
                </a>
                <a href="#" class="block text-center font-medium text-lg"><?php echo "{$author -> getFirstName()} {$author -> getLastName()}" ?></a>
                <span class="text-sm text-gray-300">Member Since: <?php echo $helpers -> format_date($author -> getRegisterDate()); ?></span>
            </div>
        </div>

        <!-- Post Content -->

        <div class="w-[75%] px-10 py-10">
            
            <div class="bg-cover bg-center h-80 rounded mb-7 relative group" style="background-image: url('<?php echo $post -> getImageUrl(); ?>')">
                <button type="button" class="text-gray-500 absolute border border-transparent top-3 right-3 tracking-widest w-8 h-8 rounded transition-all group-hover:bg-white group-hover:border-gray-300">
                    •••
                </button>
            </div>

            <h1 class="text-center mb-4 text-gray-700 text-2xl font-semibold"><?php echo $post -> getTitle(); ?></h1>

            <div class="flex gap-4 justify-center mb-6">
                <div class="bg-gray-200 rounded flex gap-3 items-center px-5">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 fill-gray-800" viewBox="0 0 448 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.--><path d="M152 24c0-13.3-10.7-24-24-24s-24 10.7-24 24l0 40L64 64C28.7 64 0 92.7 0 128l0 16 0 48L0 448c0 35.3 28.7 64 64 64l320 0c35.3 0 64-28.7 64-64l0-256 0-48 0-16c0-35.3-28.7-64-64-64l-40 0 0-40c0-13.3-10.7-24-24-24s-24 10.7-24 24l0 40L152 64l0-40zM48 192l352 0 0 256c0 8.8-7.2 16-16 16L64 464c-8.8 0-16-7.2-16-16l0-256z"/></svg>
                    <span><?php echo $helpers -> format_date($post -> getPublishDate()); ?></span>
                </div>
                <span class="bg-gray-200 rounded px-5 py-2">Web Development</span>
            </div>

            <div class="max-w-lg h-px bg-gray-200 mx-auto mb-7"></div>

            <pre class="text-gray-800 whitespace-pre-line">
                <?php echo $post -> getContent(); ?>
            </pre>


            <!-- More Posts -->

            <h3 class="mb-5 mt-8 text-lg font-medium text-gray-800">More Posts By <?php echo $author -> getFirstName(); ?></h3>

            <div class="grid grid-cols-3 gap-5">

                <!-- Blog Item -->

                <a class="shadow-md rounded overflow-hidden block post-item" href="/pages/post.php?id=5">
                    <div class="h-44 bg-gray-200 bg-cover bg-center" style="background-image: url('/assets/imgs/blogs/placeholder.jpg')">

                    </div>
                    <div class="px-5 py-4">
                        <span class="text-gray-400 font-medium">SEO</span>
                        <h2 class="text-blue-500 font-semibold text-lg mt-2 mb-1">Learn What is SEO</h2>
                        <!-- <h3 class="text-sm text-gray-500">Anass Boutaib</h3> -->
                    </div>
                </a>

                <a class="shadow-md rounded overflow-hidden block post-item" href="/pages/post.php?id=5">
                    <div class="h-44 bg-gray-200 bg-cover bg-center" style="background-image: url('/assets/imgs/blogs/placeholder.jpg')">

                    </div>
                    <div class="px-5 py-4">
                        <span class="text-gray-400 font-medium">SEO</span>
                        <h2 class="text-blue-500 font-semibold text-lg mt-2 mb-1">Learn What is SEO</h2>
                        <!-- <h3 class="text-sm text-gray-500">Anass Boutaib</h3> -->
                    </div>
                </a>

                <a class="shadow-md rounded overflow-hidden block post-item" href="/pages/post.php?id=5">
                    <div class="h-44 bg-gray-200 bg-cover bg-center" style="background-image: url('/assets/imgs/blogs/placeholder.jpg')">

                    </div>
                    <div class="px-5 py-4">
                        <span class="text-gray-400 font-medium">SEO</span>
                        <h2 class="text-blue-500 font-semibold text-lg mt-2 mb-1">Learn What is SEO</h2>
                        <!-- <h3 class="text-sm text-gray-500">Anass Boutaib</h3> -->
                    </div>
                </a>

            </div>

        </div>

    </div>

</body>
</html>