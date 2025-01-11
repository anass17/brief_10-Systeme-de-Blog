<?php

    session_start();

    require '../../Controllers/Classes/Database.php';
    require '../../Controllers/Classes/Auth.php';
    require '../../Controllers/Classes/Admin.php';
    require '../../Controllers/Classes/Post.php';
    require '../../Controllers/Classes/Tag.php';
    require '../../Controllers/Classes/helpers.php';

    $db = new Database();
    $user = new Admin($db);
    $post = new Post($db);
    $tag = new Tag($db);
    $helper = new Helpers();

    $is_registred = true;

    $general_statistics = $user -> getStatistics();

    $full_statistics = $user -> get30DaysStatistics();
    
    $loaded_users = $user -> loadUsers();
    $loaded_tags = $user -> loadTags();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <?php
        include '../../inc/links.php';
    ?>
</head>
<body class="pb-10">

    <?php
        include '../../inc/header.php';
    ?>

    <h1 class="text-4xl mt-10 text-gray-800 font-semibold text-center">Dashboard</h1>

    <div class="flex gap-4 justify-center items-center *:block *:w-32 *:py-1 *:rounded mt-10 *:font-medium *:border *:border-gray-300">
        <button type="button" data-target="statistics" class="bg-gray-800 text-white dashboard-btn">Statistics</button>
        <button type="button" data-target="users" class="bg-gray-200 text-gray-800 dashboard-btn">Users</button>
        <button type="button" data-target="posts" class="bg-gray-200 text-gray-800 dashboard-btn">Posts</button>
        <button type="button" data-target="tags" class="bg-gray-200 text-gray-800 dashboard-btn">Tags</button>
    </div>

    <div class="mx-auto max-w-lg bg-gray-300 h-px mb-10 mt-10">

    </div>

    <div class="" data-section="statistics">
        <div class="mb-10">
            <h3 class="text-center mb-5 font-semibold text-2xl">General Statistics</h3>
            <div class="grid grid-cols-3 gap-5 max-w-[1000px] mx-auto px-3 text-center *:py-3 *:shadow-lg">
                <div class="rounded bg-gray-800 text-white">
                    <h4 class="font-semibold mb-2 text-orange-500">Users</h4>
                    <span class="text-lg"><?php echo $general_statistics['users_count']; ?></span>
                </div>
                <div class="rounded bg-gray-800 text-white">
                    <h4 class="font-semibold mb-2 text-orange-500">Posts</h4>
                    <span class="text-lg"><?php echo $general_statistics['posts_count']; ?></span>
                </div>
                <div class="rounded bg-gray-800 text-white">
                    <h4 class="font-semibold mb-2 text-orange-500">Comments</h4>
                    <span class="text-lg"><?php echo $general_statistics['comments_count']; ?></span>
                </div>
                <div class="rounded bg-gray-800 text-white">
                    <h4 class="font-semibold mb-2 text-orange-500">Tags</h4>
                    <span class="text-lg"><?php echo $general_statistics['tags_count']; ?></span>
                </div>
                <div class="rounded bg-gray-800 text-white">
                    <h4 class="font-semibold mb-2 text-orange-500">Likes</h4>
                    <span class="text-lg"><?php echo $general_statistics['likes_count']; ?></span>
                </div>
            </div>
        </div>

        <div class="">
            <h3 class="text-center mb-5 font-semibold text-2xl">For the previous 30 days</h3>
            <div class="max-w-2xl mx-auto">
                <canvas id="chart" width="400" height="400"></canvas>
            </div>
        </div>
    </div>

    <div class="hidden" data-section="users">
        <div class="max-w-[1250px] mx-auto px-3 text-center">
            <h3 class="text-center mb-5 font-semibold text-2xl">List of users</h3>
            <div class="grid grid-cols-4 gap-4 bg-orange-500 text-white py-2">
                <span>First Name</span>
                <span>Last Name</span>
                <span>Email</span>
                <span>Register Date</span>
            </div>
            <?php foreach($loaded_users as $user): ?>
                <div class="grid grid-cols-4 gap-4 border py-2 even:bg-gray-100">
                    <span><?php echo $user['first_name'] ?></span>
                    <span><?php echo $user['last_name'] ?></span>
                    <span><?php echo $user['email'] ?></span>
                    <span><?php echo $helper -> format_date($user['register_date']); ?></span>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="hidden" data-section="posts">
        <h3 class="text-center mb-5 font-semibold text-2xl">List of posts</h3>
    </div>

    <div class="hidden" data-section="tags">
        <h3 class="text-center mb-5 font-semibold text-2xl">List of tags</h3>
        <div class="max-w-[1250px] mx-auto px-3 grid grid-cols-5 gap-5 text-center">
            <?php foreach($loaded_tags as $tag): ?>
                <div class="bg-gray-500 text-white rounded py-2 flex justify-between px-5">
                    <span><?php echo $tag["tag_name"]; ?></span>
                    <span><?php echo $tag["count"]; ?></span>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    

    <script>

        <?php 

            $labels = [];
            for($i = 0; $i < 30; $i++) {
                $labels[] = $i + 1;
            }

            $usersStatistics = json_encode($full_statistics['users']);
            $postsStatistics = json_encode($full_statistics['posts']);
            $commentsStatistics = json_encode($full_statistics['comments']);
            $labels = json_encode($labels);
            echo "let usersStatistics = $usersStatistics;
            let postsStatistics = $postsStatistics;
            let commentsStatistics = $commentsStatistics;
            let labelsList = $labels;";
        ?>
    
    </script>

    <script src="/assets/js/helpers.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="/assets/js/dashboard.js"></script>

</body>
</html>