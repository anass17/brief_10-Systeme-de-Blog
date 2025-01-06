let writeBlogBtn = document.querySelector('.write-blog-btn');

writeBlogBtn.addEventListener('click', function () {
    let modal = document.createElement('div');

    modal.className = "bg-black bg-opacity-70 backdrop-blur-sm fixed top-0 left-0 z-50 flex justify-center items-center w-full h-screen";

    modal.innerHTML = 
    `<div class="bg-white w-full max-w-xl rounded max-h-[650px] overflow-auto shadow-lg">
            
        <div class="px-7 py-4 flex justify-between items-center border-b border-gray-300">
            <h2 class="text-lg font-bold text-gray-700">Write a New Blog</h2>
            <button type="button" class="font-semibold text-red-500 text-xl close-btn">X</button>
        </div>

        <div class="py-5 px-7">
            <form action="/Controllers/Validation/add-post.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" value="${CSRFToken}" name="CSRF_token">
                <input type="hidden" value="${userID}" name="author_id">
                <div class="mb-4">
                    <label for="form-post-title" class="block mb-2">Title</label>
                    <input type="text" placeholder="Post Title" id="form-post-title" name="post-title" class="w-full px-4 py-2 rounded border outline-none border-orange-200 bg-gray-100 placeholder:text-gray-500">
                    <small class="text-red-400 font-semibold"></small>
                </div>

                <div class="mb-4">
                    <label for="form-post-content" class="block mb-2">Content</label>
                    <textarea type="text" placeholder="Post Body" id="form-post-content" name="post-content" class="w-full px-4 py-2 rounded border outline-none h-36 resize-y border-orange-200 bg-gray-100 placeholder:text-gray-500"></textarea>
                    <small class="text-red-400 font-semibold"></small>
                </div>

                <div class="mb-4">
                    <label for="form-post-category" class="block mb-2">category</label>
                    <select id="form-post-category" name="post-category" class="w-full px-3 py-2 rounded border outline-none border-orange-200 bg-gray-100 placeholder:text-gray-500">
                        <option value="">SELECT</option>
                        <option value="1">Web Development</option>
                        <option value="2">Artificial Inteligence</option>
                        <option value="3">Problem Solving</option>
                    </select>
                    <small class="text-red-400 font-semibold"></small>
                </div>
                
                <div class="mb-4">
                    <p class="mb-2">Background</p>
                    <label for="form-post-background" class="mb-2 h-16 bg-gray-500 text-white rounded flex gap-6 text-lg justify-center items-center cursor-pointer">
                        <svg xmlns="http://www.w3.org/2000/svg" class="fill-white w-6 h-6" viewBox="0 0 640 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.--><path d="M144 480C64.5 480 0 415.5 0 336c0-62.8 40.2-116.2 96.2-135.9c-.1-2.7-.2-5.4-.2-8.1c0-88.4 71.6-160 160-160c59.3 0 111 32.2 138.7 80.2C409.9 102 428.3 96 448 96c53 0 96 43 96 96c0 12.2-2.3 23.8-6.4 34.6C596 238.4 640 290.1 640 352c0 70.7-57.3 128-128 128l-368 0zm79-217c-9.4 9.4-9.4 24.6 0 33.9s24.6 9.4 33.9 0l39-39L296 392c0 13.3 10.7 24 24 24s24-10.7 24-24l0-134.1 39 39c9.4 9.4 24.6 9.4 33.9 0s9.4-24.6 0-33.9l-80-80c-9.4-9.4-24.6-9.4-33.9 0l-80 80z"/></svg>    
                        Upload an Image
                    </label>
                    <input type="file" id="form-post-background" name="post-background" class="hidden"></textarea>
                    <small class="text-red-400 font-semibold"></small>
                </div>

                <button type="submit" class="h-10 w-32 bg-orange-500 text-white block rounded submit-btn">POST</button>
            </form>
        </div>
    </div>`;

    document.body.append(modal);

    modal.querySelector('.close-btn').addEventListener('click', function () {
        modal.remove();
    });

    modal.querySelector('.submit-btn').addEventListener('click', function (e) {
        let postTitle = modal.querySelector('#form-post-title');
        let postContent = modal.querySelector('#form-post-content');
        let postCategory = modal.querySelector('#form-post-category');
        let hasErrors = false;

        if (postTitle.value.trim().length < 5) {
            postTitle.nextElementSibling.textContent = "Post title is too short";
            hasErrors = true;
        } else {
            postTitle.nextElementSibling.textContent = "";
        }

        if (postContent.value.trim().length < 10) {
            postContent.nextElementSibling.textContent = "Post content is too short";
            hasErrors = true;
        } else {
            postContent.nextElementSibling.textContent = "";
        }

        if (postCategory.value == "") {
            postCategory.nextElementSibling.textContent = "Please select a category";
            hasErrors = true;
        } else {
            postCategory.nextElementSibling.textContent = "";
        }

        if (hasErrors == true) {
            e.preventDefault();
        }
        
    });

});