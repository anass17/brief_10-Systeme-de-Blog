<?php
    session_start();

    require '../Controllers/Classes/Database.php';
    require '../Controllers/Classes/User.php';
    require '../Controllers/Classes/Auth.php';
    require '../Controllers/Classes/Post.php';

    $db = new Database();
    $auth = new Auth($db);
    $post = new Post($db);

    $auth -> createCSRFToken();

    if ($auth -> isAccessTokenExists()) {
        $is_registred = true;
    } else {
        $is_registred = false;
    }

    $results = $post -> getAllPosts();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blogs</title>
    <?php
        include '../inc/links.php';
    ?>
</head>
<body>
    
    <?php
        include '../inc/header.php';
    ?>

    <div class="flex">
        <div class="min-h-screen w-[25%] bg-gray-700 border-r border-gray-200 shadow-md text-white px-5 py-10">
            <h3 class="font-semibold text-lg">Search</h3>
            <h3 class="font-semibold text-lg">Filter</h3>
        </div>
        <div class="w-[75%] px-6 py-10">
            <h1 class="text-center mb-12 text-gray-700 text-2xl font-semibold">Discover these topics that may interst you</h1>

            <?php if (isset($_SESSION['errors'])): ?>
                <div class="px-5 py-4 rounded border border-red-500 bg-red-100 mb-7 -mt-5 text-center">
                    <ul>
                        <?php
                            foreach($_SESSION["errors"] as $error) {
                                echo "<li>$error</li>";
                            }
                            session_destroy();
                        ?>
                    </ul>
                </div>
            <?php endif; ?>

            <!-- Add Blog Button -->

            <button type="button" class="write-blog-btn w-60 h-12 rounded bg-gray-700 text-orange-200 font-semibold flex items-center justify-center gap-4 mx-auto mb-7">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 fill-orange-200" viewBox="0 0 512 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.--><path d="M410.3 231l11.3-11.3-33.9-33.9-62.1-62.1L291.7 89.8l-11.3 11.3-22.6 22.6L58.6 322.9c-10.4 10.4-18 23.3-22.2 37.4L1 480.7c-2.5 8.4-.2 17.5 6.1 23.7s15.3 8.5 23.7 6.1l120.3-35.4c14.1-4.2 27-11.8 37.4-22.2L387.7 253.7 410.3 231zM160 399.4l-9.1 22.7c-4 3.1-8.5 5.4-13.3 6.9L59.4 452l23-78.1c1.4-4.9 3.8-9.4 6.9-13.3l22.7-9.1 0 32c0 8.8 7.2 16 16 16l32 0zM362.7 18.7L348.3 33.2 325.7 55.8 314.3 67.1l33.9 33.9 62.1 62.1 33.9 33.9 11.3-11.3 22.6-22.6 14.5-14.5c25-25 25-65.5 0-90.5L453.3 18.7c-25-25-65.5-25-90.5 0zm-47.4 168l-144 144c-6.2 6.2-16.4 6.2-22.6 0s-6.2-16.4 0-22.6l144-144c6.2-6.2 16.4-6.2 22.6 0s6.2 16.4 0 22.6z"/></svg>
                <span>Write a new Blog</span>
            </button>

            <div class="max-w-lg h-px bg-gray-200 mx-auto mb-7"></div>

            <div class="grid grid-cols-3 gap-4">

                <!-- Blog Item -->

                <?php foreach($results as $row): ?>

                    <a class="shadow-md rounded overflow-hidden block post-item" href="/pages/post.php?id=<?php echo $row["post_id"]; ?>">
                        <div class="h-44 bg-gray-200 bg-cover bg-center" style="background-image: url('<?php if ($row["post_image_url"] == "") {echo "/assets/imgs/blogs/placeholder.jpg";} else {echo $row["post_image_url"];} ?>')">

                        </div>
                        <div class="px-5 py-4">
                            <span class="text-gray-400 font-medium"><?php echo "{$row['cat_name']}"; ?></span>
                            <h2 class="text-blue-500 font-semibold text-lg mt-4 mb-1"><?php echo $row['title']; ?></h2>
                            <h3 class="text-sm text-gray-500"><?php echo "{$row['first_name']} {$row['last_name']}"; ?> <?php if ($row["post_author"] == $auth -> user -> getId()) {echo "<span class='px-3 py-0.5 text-center ml-3 rounded bg-gray-600 text-white inline-block'>You</span>";} ?></h3>
                        </div>
                    </a>

                <?php endforeach; ?>
            </div>
        </div>
    </div>
    
    <script>
        <?php
            echo "let userID = {$auth -> user -> getId()};";
            echo "let CSRFToken = '{$_SESSION["CSRF_token"]}';";
        ?>
    </script>

    <script src="/assets/js/blogs.js"></script>

</body>
</html>