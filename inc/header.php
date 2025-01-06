<div class="bg-gradient-to-b from-orange-500 to-orange-600">
    <div class="max-w-[1250px] mx-auto px-3 h-16 flex justify-between items-center">
        
        <?php if ($is_registred == false): ?>
            <div>
                <a href="/index.php" class="font-semibold text-lg no-underline text-white">WisNest</a>
            </div>
            
            <div class="*:ml-6 *:font-semibold text-white">
                <a href="/index.php">Home</a>
                <a href="/pages/blogs.php">Blogs</a>
                <a href="/pages/auth.php">Login</a>
                <a href="/pages/auth.php?to=register">Register</a>
            </div>

        <?php else: ?>

            <div>
                <a href="/pages/blogs.php" class="font-semibold text-lg no-underline text-white">WisNest</a>
            </div>

            <div class="*:ml-7 *:font-semibold text-white flex items-center">
                <a href="/pages/blogs.php" class="flex gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 fill-gray-700" viewBox="0 0 576 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.--><path d="M575.8 255.5c0 18-15 32.1-32 32.1l-32 0 .7 160.2c0 2.7-.2 5.4-.5 8.1l0 16.2c0 22.1-17.9 40-40 40l-16 0c-1.1 0-2.2 0-3.3-.1c-1.4 .1-2.8 .1-4.2 .1L416 512l-24 0c-22.1 0-40-17.9-40-40l0-24 0-64c0-17.7-14.3-32-32-32l-64 0c-17.7 0-32 14.3-32 32l0 64 0 24c0 22.1-17.9 40-40 40l-24 0-31.9 0c-1.5 0-3-.1-4.5-.2c-1.2 .1-2.4 .2-3.6 .2l-16 0c-22.1 0-40-17.9-40-40l0-112c0-.9 0-1.9 .1-2.8l0-69.7-32 0c-18 0-32-14-32-32.1c0-9 3-17 10-24L266.4 8c7-7 15-8 22-8s15 2 21 7L564.8 231.5c8 7 12 15 11 24z"/></svg>
                    <span>Home</span>
                </a>
                <a href="/pages/blogs.php" class="flex gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 fill-gray-700" viewBox="0 0 448 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.--><path d="M224 0c-17.7 0-32 14.3-32 32l0 19.2C119 66 64 130.6 64 208l0 18.8c0 47-17.3 92.4-48.5 127.6l-7.4 8.3c-8.4 9.4-10.4 22.9-5.3 34.4S19.4 416 32 416l384 0c12.6 0 24-7.4 29.2-18.9s3.1-25-5.3-34.4l-7.4-8.3C401.3 319.2 384 273.9 384 226.8l0-18.8c0-77.4-55-142-128-156.8L256 32c0-17.7-14.3-32-32-32zm45.3 493.3c12-12 18.7-28.3 18.7-45.3l-64 0-64 0c0 17 6.7 33.3 18.7 45.3s28.3 18.7 45.3 18.7s33.3-6.7 45.3-18.7z"/></svg>
                    <span>Notifications</span>
                </a>
                <button class="block w-9 h-9 rounded-full overflow-hidden border-2 border-white">
                    <img src="/assets/imgs/users/default.webp" alt>
                </button>
            </div>

        <?php endif; ?>

    </div>
</div>