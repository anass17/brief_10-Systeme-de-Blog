let writeBlogBtn = document.querySelector('.write-blog-btn');

let tagsString = "";

tagsList.forEach(tag => {
    tagsString += `<button type="button" data-tag="${tag["tag_id"]}" class="bg-gray-100 tag-btn">${tag["tag_name"]}</button>`;
})

writeBlogBtn.addEventListener('click', function () {
    let modal = document.createElement('div');

    modal.className = "bg-black bg-opacity-70 backdrop-blur-sm fixed top-0 left-0 z-50 flex justify-center items-center w-full h-screen";

    modal.innerHTML = 
    `<div class="bg-white w-full max-w-xl rounded max-h-[650px] overflow-auto shadow-lg no-scrolling">
            
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
                    <textarea type="text" placeholder="Post Body [# Headline] [**Bold Text**]" id="form-post-content" name="post-content" class="w-full px-4 py-2 rounded border outline-none h-36 resize-y border-orange-200 bg-gray-100 placeholder:text-gray-500"></textarea>
                    <small class="text-red-400 font-semibold"></small>
                </div>

                <div class="mb-4">
                    <div class="flex items-center justify-between mb-2">
                        <label for="form-post-tags" class="block mb-2">Tags <span class="ml-5 text-gray-500">[<span class="tags-count">0</span>]</span></label>
                        <button type="button" class="tags-toggle transition-all">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 320 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.--><path d="M310.6 233.4c12.5 12.5 12.5 32.8 0 45.3l-192 192c-12.5 12.5-32.8 12.5-45.3 0s-12.5-32.8 0-45.3L242.7 256 73.4 86.6c-12.5-12.5-12.5-32.8 0-45.3s32.8-12.5 45.3 0l192 192z"/></svg>
                        </button>
                    </div>
                    <div class="">
                        <div class="*:text-sm grid grid-cols-3 gap-3 *:rounded *:border *:shadow *:py-1.5 overflow-auto no-scrolling tags-container transition-all" style="height: 0px;">
                            ${tagsString}
                        </div>
                    </div>
                    <input type="hidden" id="form-post-tags" name="post-tags" class="w-full px-3 py-2 rounded border outline-none border-orange-200 bg-gray-100 placeholder:text-gray-500">
                    <small class="text-red-400 font-semibold"></small>
                </div>
                
                <div class="mb-4">
                    <p class="mb-2">Background</p>
                    <label for="form-post-background" class="file-label mb-2 h-16 bg-gray-500 text-white rounded flex gap-6 text-lg justify-center items-center cursor-pointer">
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

    let postTitle = modal.querySelector('#form-post-title');
    let postContent = modal.querySelector('#form-post-content');
    let postTags = modal.querySelector('#form-post-tags');

    modal.querySelector('.submit-btn').addEventListener('click', function (e) {
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

        if (postTags.value == "") {
            postTags.nextElementSibling.textContent = "Please select at least a tag";
            hasErrors = true;
        } else {
            postTags.nextElementSibling.textContent = "";
        }

        if (hasErrors == true) {
            e.preventDefault();
        }
        
    });

    document.querySelector('#form-post-background').addEventListener('input', function () {
        this.previousElementSibling.innerHTML = "Successfully Uploaded";
    });

    modal.querySelector('.tags-toggle').addEventListener('click', function () {
        let tagsContainer = modal.querySelector('.tags-container');
        if (tagsContainer.style.height == "0px") {
            tagsContainer.style.height = "100px";
            this.style.transform = "rotate(90deg)";
        } else {
            tagsContainer.style.height = "0px";
            this.style.transform = "rotate(0deg)";
        }
    });

    let tagsCount = modal.querySelector('.tags-count');

    modal.querySelectorAll('.tag-btn').forEach((btn) => {
        btn.addEventListener('click', function () {
            if (this.classList.contains('selected')) {
                this.classList.remove('selected');
                tagsCount.textContent = tagsCount.textContent - 1;
                postTags.value = postTags.value.replace(this.dataset.tag + ',', '');
            } else {
                this.classList.add('selected');
                tagsCount.textContent = +tagsCount.textContent + 1;
                postTags.value += this.dataset.tag + ',';
            }
        });
    });

});

//-----------------------------
// Filter
//-----------------------------

let filterTagButton = document.querySelectorAll('.filter-tag-btn');
let blogsContainer = document.querySelector('.blogs-container');

filterTagButton.forEach(item => {
    item.addEventListener('click', function () {
        if (this.classList.contains('filter-selected')) {
            this.classList.remove('filter-selected');
        } else {
            this.classList.add('filter-selected');
        }

        searchFilterRequest();
        
    });
});

function displayBlog(details) {
    let element = document.createElement('a');

    element.className = "shadow-md rounded overflow-hidden block post-item";
    element.href = "/pages/post.php?id=" + details["post_id"];

    let tagsList = details.tags.split(',');

    let tagsString = "";

    for(let i = 0; i < tagsList.length && i < 3; i++) {
        tagsString += `<span class='mr-4'>#${tagsList[i]}</span>`;
    }

    if (tagsList.length > 3) {
        tagsString += "<span class='text-gray-800'>+ " + (tagsList.length - 3) + "</span>";
    }

    let image = "/assets/imgs/blogs/placeholder.jpg";

    if (details.image != "") {
        image = details.image;
    }


    element.innerHTML = 
        `<div class="h-44 bg-gray-200 bg-cover bg-center" style="background-image: url('${image}')">

        </div>
        <div class="px-5 py-4">
            <span class="text-gray-400 font-medium">
                ${tagsString}
            </span>
            <h2 class="text-blue-500 font-semibold text-lg mt-4 mb-1">${details["title"]}</h2>
            <h3 class="text-sm text-gray-500">${details.first_name} ${details.last_name}</h3>
        </div>`;

        blogsContainer.append(element);
}

let searchInput = document.querySelector('.search-input');

searchInput.addEventListener('keyup', function () {
    searchFilterRequest();
});

function searchFilterRequest() {

    let endpoint = `/api/PostApi.php?search=${searchInput.value}&tags=${([...filterTagButton].reduce((str, item) => item.classList.contains('filter-selected') ? str += item.dataset.id + ',' : str += '', ''))}`;

    fetch(endpoint, {
        method: 'GET'
    })
    .then(response => response.json())
    .then(data => {

        blogsContainer.innerHTML = "";

        if (data.response.length > 0) {
            data.response.forEach(item => {
                displayBlog(item);
            });
        } else {
            let para = document.createElement('p');

            para.className = "text-center col-span-3 text-gray-600";

            para.textContent = "Could not find any posts for the selected filters";

            blogsContainer.append(para);
        }
    });
}

document.querySelector('.search-tags').addEventListener('keyup', function () {
    filterTagButton.forEach(btn => {
        if (btn.textContent.toLowerCase().search(this.value.toLowerCase()) >= 0) {
            btn.classList.remove('hidden');
        } else {
            btn.classList.add('hidden');
        }
    });
});