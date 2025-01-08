<?php
    session_start();

    require '../Controllers/Classes/Database.php';
    require '../Controllers/Classes/User.php';
    require '../Controllers/Classes/Auth.php';

    $db = new Database();
    $auth = new Auth($db);

    // Check if access token does not exist

    if ($auth -> isAccessTokenExists()) {
        header('Location: /index.php');
        exit;
    }

    $auth -> createCSRFToken();

    $page = 'login';
    if (isset($_GET['to']) && $_GET['to'] == 'register') {
        $page = 'register';
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="/assets/css/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap" rel="stylesheet">
</head>
<body class="h-screen overflow-hidden">

    <!-- <header class="bg-orange-600 text-white shadow-md">
        <div class="w-[1250px] mx-auto px-3 h-[70px] flex justify-between items-center">
            <a href="#" class="text-lg font-semibold">Blog</a>
            <nav>
                <ul class="flex *:ml-5 font-semibold">
                    <li><a href="#">Home</a></li>
                    <li><a href="#">Blogs</a></li>
                    <li><a href="#">Log in</a></li>
                    <li><a href="#">Register</a></li>
                </ul>
            </nav>
        </div>
    </header> -->

    <div class="flex gap-10 h-screen overflow-hidden">
        <div class="h-screen w-[70%] flex justify-center items-center form-container absolute top-0 left-0 transition-all delay-100 duration-300 <?php if($page == 'register') { echo " left-[30%]"; } ?>">
            
            <!-- Log in Form -->

            <form action="/Controllers/Validation/login.php" method="POST" class="py-7 px-9 max-w-xl w-full rounded-xl login-form transition-all delay-100 duration-200 absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 <?php if($page != 'login') { echo " invisible opacity-0 scale-75"; } ?>">
                <h1 class="text-center mb-14 text-gray-700 font-extrabold text-4xl form-title">Login to Your Account</h1>
                <?php if (isset($_SESSION['errors']) && $page == 'login'): ?>
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
                <input type="hidden" name="CSRF_token" value="<?php echo $_SESSION["CSRF_token"]; ?>">
                <div class="mb-7">
                    <label for="email" class="mb-2 block sr-only">Email</label>
                    <input type="text" name="email" placeholder="Email" id="email" class="outline-none border border-orange-300 px-4 py-2 w-full rounded-lg bg-orange-500 bg-opacity-10 placeholder:text-gray-500">
                </div>
                <div class="mb-7">
                    <label for="password" class="mb-2 block sr-only">Password</label>
                    <input type="password" name="password" placeholder="Password" id="password" class="outline-none border border-orange-300 px-4 py-2 w-full rounded-lg bg-orange-500 bg-opacity-10 placeholder:text-gray-500">
                </div>
                <button type="submit" class="w-44 h-12 block mx-auto rounded-full font-semibold text-white bg-orange-500 hover:bg-orange-600 transition-colors">Log In</button>
            </form>

            <!-- Register Form -->

            <form action="/Controllers/Validation/register.php" method="POST" class="py-7 px-9 max-w-xl w-full rounded-xl register-form transition-all duration-300 delay-100 absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 <?php if($page != 'register') { echo " invisible opacity-0 scale-75"; } ?>">
                <h1 class="text-center mb-14 text-gray-700 font-extrabold text-4xl form-title">Create a new Account</h1>
                <?php if (isset($_SESSION['errors']) && $page == 'register'): ?>
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
                <input type="text" name="CSRF_token" value="<?php echo $_SESSION["CSRF_token"]; ?>">
                <div class="flex gap-4 mb-7">
                    <div class="w-full">
                        <label for="first-name" class="mb-2 block sr-only">First Name</label>
                        <input type="text" name="first-name" placeholder="First Name" id="first-name" class="outline-none border border-orange-300 px-4 py-2 w-full rounded-lg bg-orange-500 bg-opacity-10 placeholder:text-gray-500">
                    </div>
                    <div class="w-full">
                        <label for="last-name" class="mb-2 block sr-only">Last Name</label>
                        <input type="text" name="last-name" placeholder="Last Name" id="last-name" class="outline-none border border-orange-300 px-4 py-2 w-full rounded-lg bg-orange-500 bg-opacity-10 placeholder:text-gray-500">
                    </div>
                </div>
                <div class="mb-7">
                    <label for="email" class="mb-2 block sr-only">Email</label>
                    <input type="text" name="email" placeholder="Email" id="email" class="outline-none border border-orange-300 px-4 py-2 w-full rounded-lg bg-orange-500 bg-opacity-10 placeholder:text-gray-500">
                </div>
                <div class="mb-7">
                    <label for="password" class="mb-2 block sr-only">Password</label>
                    <input type="password" name="password" placeholder="Password" id="password" class="outline-none border border-orange-300 px-4 py-2 w-full rounded-lg bg-orange-500 bg-opacity-10 placeholder:text-gray-500">
                </div>
                <div class="mb-7">
                    <label for="confirm-password" class="mb-2 block sr-only">Confirm Password</label>
                    <input type="password" name="confirm-password" placeholder="Confirm Password" id="confirm-password" class="outline-none border border-orange-300 px-4 py-2 w-full rounded-lg bg-orange-500 bg-opacity-10 placeholder:text-gray-500">
                </div>
                <button type="submit" class="w-44 h-12 block mx-auto rounded-full font-semibold text-white bg-orange-500 hover:bg-orange-600 transition-colors">Register</button>
            </form>
        </div>


        <div class="w-[30%] bg-gradient-to-tr to-orange-400 from-orange-700 h-screen register-bar flex flex-col justify-center items-center text-white px-7 text-center transition duration-300 absolute top-0 left-[70%] <?php if($page == 'register') { echo " translate-y-full"; } ?>">
            <h2 class="mb-10 text-3xl font-bold">New Here?</h2>
            <p class="text-md">Register and discover plenty of blogs that would interest you.</p>
            <button type="button" class="bg-white w-44 h-12 rounded-full mt-7 text-orange-500 font-semibold register-btn">Register</button>
        </div>

        <div class="w-[30%] bg-gradient-to-tr to-orange-400 from-orange-700 h-screen login-bar flex flex-col justify-center items-center text-white px-7 text-center transition duration-300 delay-300 absolute top-0 left-0  <?php if($page == 'login') { echo " translate-y-full"; } ?>">
            <h2 class="mb-10 text-3xl font-bold">Already a member?</h2>
            <p class="text-md">Log into your account and continue the journey. Don't miss it</p>
            <button type="button" class="bg-white w-44 h-12 rounded-full mt-7 text-orange-500 font-semibold login-btn">Login</button>
        </div>
    </div>

    <script>
        let registerBar = document.querySelector('.register-bar');
        let loginBar = document.querySelector('.login-bar');
        let formContainer = document.querySelector('.form-container');
        let formTitle = document.querySelector('.form-title');
        let loginForm = formContainer.querySelector('.login-form');
        let registerForm = formContainer.querySelector('.register-form');

        registerBar.lastElementChild.addEventListener('click', function () {
            
            // Register Bar

            registerBar.style.transform = 'translateY(100%)';
            registerBar.classList.remove('delay-300');

            // Login Bar

            loginBar.style.transform = 'translateY(0)';
            loginBar.classList.add('delay-300');

            // Login Form

            loginForm.style.opacity = '0';
            loginForm.style.visibility = 'hidden';
            loginForm.classList.add('delay-100', 'scale-75');
            loginForm.classList.remove('delay-300');

            // Register Form

            registerForm.style.opacity = '1';
            registerForm.style.visibility = 'visible';
            registerForm.classList.remove('scale-75');
            registerForm.classList.add('delay-300');

            
            formContainer.style.left = '30%';
        });
        loginBar.lastElementChild.addEventListener('click', function () {

            // Register Bar
            
            registerBar.style.transform = 'translateY(0)';
            registerBar.classList.add('delay-300');

            // Login Bar

            loginBar.style.transform = 'translateY(100%)';
            loginBar.classList.remove('delay-300');

            // Login Form

            loginForm.style.opacity = '1';
            loginForm.style.visibility = 'visible';
            loginForm.classList.remove('delay-100', 'scale-75');
            loginForm.classList.add('delay-300');

            // Register Form

            registerForm.style.opacity = '0';
            registerForm.style.visibility = 'hidden';
            registerForm.classList.add('scale-75', 'delay-100');
            registerForm.classList.remove('delay-300');

                        
            formContainer.style.left = '0';
        });
    </script>
</body>
</html>