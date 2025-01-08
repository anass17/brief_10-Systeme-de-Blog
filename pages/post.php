<?php

    session_start();

    require '../Controllers/Classes/Database.php';
    require '../Controllers/Classes/User.php';
    require '../Controllers/Classes/Auth.php';
    require '../Controllers/Classes/Post.php';
    require '../Controllers/Classes/Comment.php';
    require '../Controllers/Classes/Helpers.php';

    $db = new Database();
    $auth = new Auth($db);
    $post = new Post($db);
    $helpers = new Helpers();

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

    $comments = $post -> getAllComments();
    $tags = $post -> getAllTags();

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

            <?php if (isset($_SESSION['errors'])): ?>
                <div class="px-5 py-4 rounded border border-red-500 bg-red-100 mb-7 -mt-5 text-center relative">
                    <ul>
                        <?php
                            foreach($_SESSION["errors"] as $error) {
                                echo "<li>$error</li>";
                            }
                            // session_destroy();
                        ?>
                    </ul>
                    <button type="button" onclick="this.parentElement.remove();" class="top-3.5 right-4 absolute font-semibold text-lg">X</button>
                </div>
            <?php endif; ?>
            
            <div class="bg-cover bg-center h-80 rounded mb-7 relative group" style="background-image: url('<?php echo $post -> getImageUrl(); ?>')">
                <button type="button" class="text-gray-500 absolute border border-transparent top-3 right-3 tracking-widest w-8 h-8 rounded transition-all group-hover:bg-white group-hover:border-gray-300">
                    •••
                </button>
            </div>

            <h1 class="text-center mb-4 text-gray-700 text-2xl font-semibold"><?php echo $post -> getTitle(); ?></h1>

            <div class="flex gap-4 justify-center mb-6">
                <div class="bg-gray-200 rounded flex gap-3 items-center px-5 py-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 fill-gray-800" viewBox="0 0 448 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.--><path d="M152 24c0-13.3-10.7-24-24-24s-24 10.7-24 24l0 40L64 64C28.7 64 0 92.7 0 128l0 16 0 48L0 448c0 35.3 28.7 64 64 64l320 0c35.3 0 64-28.7 64-64l0-256 0-48 0-16c0-35.3-28.7-64-64-64l-40 0 0-40c0-13.3-10.7-24-24-24s-24 10.7-24 24l0 40L152 64l0-40zM48 192l352 0 0 256c0 8.8-7.2 16-16 16L64 464c-8.8 0-16-7.2-16-16l0-256z"/></svg>
                    <span><?php echo $helpers -> format_date($post -> getPublishDate()); ?></span>
                </div>
                <!-- <span class="bg-gray-200 rounded px-5 py-2">Web Development</span> -->
            </div>

            <div class="max-w-lg h-px bg-gray-200 mx-auto mb-7"></div>

            <pre class="text-gray-800 whitespace-pre-line">
                <?php echo $post -> getContent(); ?>
            </pre>
            <div class="mt-7">
                <?php foreach($tags as $tag): ?>
                    <a href="#" class="text-blue-500 font-semibold mr-5"># <?php echo $tag["tag_name"]; ?></a>
                <?php endforeach; ?>
            </div>

            <!-- Comments -->

            <div>
                <div class="py-3 mt-7 flex items-center justify-between border-t border-b px-7">
                    <div class="flex items-center gap-7">
                        <div class="flex items-center gap-2">
                            <button>
                                <!-- Uploaded to: SVG Repo, www.svgrepo.com, Generator: SVG Repo Mixer Tools -->
                                <svg class="w-8 h-8 fill-gray-800" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M15.0501 7.04419C15.4673 5.79254 14.5357 4.5 13.2163 4.5C12.5921 4.5 12.0062 4.80147 11.6434 5.30944L8.47155 9.75H5.85748L5.10748 10.5V18L5.85748 18.75H16.8211L19.1247 14.1428C19.8088 12.7747 19.5406 11.1224 18.4591 10.0408C17.7926 9.37439 16.8888 9 15.9463 9H14.3981L15.0501 7.04419ZM9.60751 10.7404L12.864 6.1813C12.9453 6.06753 13.0765 6 13.2163 6C13.5118 6 13.7205 6.28951 13.627 6.56984L12.317 10.5H15.9463C16.491 10.5 17.0133 10.7164 17.3984 11.1015C18.0235 11.7265 18.1784 12.6814 17.7831 13.472L15.8941 17.25H9.60751V10.7404ZM8.10751 17.25H6.60748V11.25H8.10751V17.25Z" fill="#080341"/>
                                </svg>
                            </button>
                            <span>0</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <button>
                                <!-- Uploaded to: SVG Repo, www.svgrepo.com, Generator: SVG Repo Mixer Tools -->
                                <svg class="w-8 h-8 fill-gray-800" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M15.0501 16.9558C15.4673 18.2075 14.5357 19.5 13.2164 19.5C12.5921 19.5 12.0063 19.1985 11.6435 18.6906L8.47164 14.25L5.85761 14.25L5.10761 13.5L5.10761 6L5.85761 5.25L16.8211 5.25L19.1247 9.85722C19.8088 11.2253 19.5407 12.8776 18.4591 13.9592C17.7927 14.6256 16.8888 15 15.9463 15L14.3982 15L15.0501 16.9558ZM9.60761 13.2596L12.8641 17.8187C12.9453 17.9325 13.0765 18 13.2164 18C13.5119 18 13.7205 17.7105 13.6271 17.4302L12.317 13.5L15.9463 13.5C16.491 13.5 17.0133 13.2836 17.3984 12.8985C18.0235 12.2735 18.1784 11.3186 17.7831 10.528L15.8941 6.75L9.60761 6.75L9.60761 13.2596ZM8.10761 6.75L6.60761 6.75L6.60761 12.75L8.10761 12.75L8.10761 6.75Z" fill="#080341"/>
                                </svg>
                            </button>
                            <span>0</span>
                        </div>

                    </div>
                    <span class="">
                        <span><?php echo count($comments); ?></span> Comments
                    </span>
                </div>

                <div class="px-7 comments-section">

                    <?php if (count($comments) > 0): ?>
                        <?php foreach($comments as $comment): ?>
                            <div class="py-5 comment-item">
                                <div class="flex items-start justify-between">
                                    <div class="flex gap-5">
                                        <div class="w-12 h-12 rounded-full overflow-hidden border-2 border-orange-500">
                                            <img src="/assets/imgs/users/default.webp" alt="">
                                        </div>
                                        <div>
                                            <h3 class="flex items-center gap-3"><?php echo "{$comment['first_name']} {$comment['last_name']}"; ?> <?php if ($comment['comment_author'] == $auth -> user -> getId()) {echo '<span>•</span><span class="">You</span>';} else if ($comment['comment_author'] == $post -> getAuthorID()) {echo '<span class="px-3 py-1 text-xs rounded bg-gray-700 text-white inline-block">Author</span>';} ?></h3>
                                            <span class="text-gray-500 text-sm"><?php echo $helpers -> format_date($comment['publish_date']); ?></span>
                                        </div>
                                    </div>

                                    <?php if ($comment['comment_author'] == $auth -> user -> getId()): ?>
                                        <div class="relative">
                                            <button type="button" class="text-gray-500 tracking-widest w-8 h-8 comment-menu-btns">
                                                •••
                                            </button>
                                            <div class="absolute hidden top-7 right-0 bg-white border shadow-md py-2 rounded min-w-36" data-id="<?php echo $comment['comment_id']; ?>">
                                                <button type="button" class="px-7 py-1 flex gap-2 w-full items-center edit-comment-btn">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 block fill-gray-800" viewBox="0 0 512 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.--><path d="M410.3 231l11.3-11.3-33.9-33.9-62.1-62.1L291.7 89.8l-11.3 11.3-22.6 22.6L58.6 322.9c-10.4 10.4-18 23.3-22.2 37.4L1 480.7c-2.5 8.4-.2 17.5 6.1 23.7s15.3 8.5 23.7 6.1l120.3-35.4c14.1-4.2 27-11.8 37.4-22.2L387.7 253.7 410.3 231zM160 399.4l-9.1 22.7c-4 3.1-8.5 5.4-13.3 6.9L59.4 452l23-78.1c1.4-4.9 3.8-9.4 6.9-13.3l22.7-9.1 0 32c0 8.8 7.2 16 16 16l32 0zM362.7 18.7L348.3 33.2 325.7 55.8 314.3 67.1l33.9 33.9 62.1 62.1 33.9 33.9 11.3-11.3 22.6-22.6 14.5-14.5c25-25 25-65.5 0-90.5L453.3 18.7c-25-25-65.5-25-90.5 0zm-47.4 168l-144 144c-6.2 6.2-16.4 6.2-22.6 0s-6.2-16.4 0-22.6l144-144c6.2-6.2 16.4-6.2 22.6 0s6.2 16.4 0 22.6z"/></svg>
                                                    <span class="text-gray-700">Edit</span>
                                                </button>
                                                <button type="button" class="px-7 py-1 flex gap-2 w-full items-center delete-comment-btn">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 block fill-red-500" viewBox="0 0 448 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.--><path d="M135.2 17.7C140.6 6.8 151.7 0 163.8 0L284.2 0c12.1 0 23.2 6.8 28.6 17.7L320 32l96 0c17.7 0 32 14.3 32 32s-14.3 32-32 32L32 96C14.3 96 0 81.7 0 64S14.3 32 32 32l96 0 7.2-14.3zM32 128l384 0 0 320c0 35.3-28.7 64-64 64L96 512c-35.3 0-64-28.7-64-64l0-320zm96 64c-8.8 0-16 7.2-16 16l0 224c0 8.8 7.2 16 16 16s16-7.2 16-16l0-224c0-8.8-7.2-16-16-16zm96 0c-8.8 0-16 7.2-16 16l0 224c0 8.8 7.2 16 16 16s16-7.2 16-16l0-224c0-8.8-7.2-16-16-16zm96 0c-8.8 0-16 7.2-16 16l0 224c0 8.8 7.2 16 16 16s16-7.2 16-16l0-224c0-8.8-7.2-16-16-16z"/></svg>
                                                    <span class="text-red-500">Delete</span>
                                                </button>
                                            </div>
                                        </div>
                                    <?php endif; ?>

                                </div>
                                <div class="whitespace-pre-line mt-4 text-gray-700 border-l-2 py-2 ml-6 border-gray-500 pl-4 comment-content"><?php echo $helpers -> format_text(htmlentities($comment['content'])); ?></div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="mt-7 text-center text-gray-600">This post has no comments. Be the first to place one</p>
                    <?php endif; ?>
                </div>

                <div class="mt-7 px-7">
                    <h3 class="mb-3 text-lg">Add Comment</h3>
                    <form class="w-full rounded comment-form" method="POST" action="/Controllers/Validation/add-comment.php">
                        <input type="hidden" name="post-id" value="<?php echo $post_id; ?>">
                        <textarea type="text" name="post-content" placeholder="Write a Comment [**Bold Text**] [# Headline]" class="resize-y border shadow h-32 bg-[#ff760017] w-full px-3 py-2 rounded border-orange-100 placeholder:text-gray-500 outline-none"></textarea>
                        <small class="text-sm text-red-500 block mb-3"></small>
                        <button type="submit" class="comment-submit-btn w-24 h-11 bg-orange-500 text-white rounded">Send</button>
                    </form>
                </div>
            </div>


            <!-- More Posts -->

            <h3 class="mb-5 mt-20 text-lg font-medium text-gray-800">More Posts By <?php echo $author -> getFirstName(); ?></h3>

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

    <script src="/assets/js/helpers.js"></script>
    <script src="/assets/js/post.js"></script>
</body>
</html>