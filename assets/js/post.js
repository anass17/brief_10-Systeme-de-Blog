let commentSection = document.querySelector('.comments-section');
let commentForm = document.querySelector('.comment-form');
let commentSubmitBtn = commentForm.querySelector('.comment-submit-btn');
let commentPostIdInput = commentForm.querySelector('[name="post-id"]');
let commentPostContentInput = commentForm.querySelector('[name="post-content"]');
let deleteCommentBtns = document.querySelectorAll('.delete-comment-btn');
let editCommentBtns = document.querySelectorAll('.edit-comment-btn');
let postMenuBtn = document.querySelector('.post-menu-btn');

commentSubmitBtn.addEventListener('click', function (e) {
    e.preventDefault();

    if (commentPostContentInput.value.trim().length < 5) {
        commentPostContentInput.nextElementSibling.textContent = "Please write a comment!";

        return;
    } else {
        commentPostContentInput.nextElementSibling.textContent = "";
    }

    fetch('/api/CommentApi.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            postId: commentPostIdInput.value,
            commentContent: commentPostContentInput.value
        })
    })
    .then(response => response.json())
    .then(data => {
        console.log(data)
        if (data.result === true) {
            let comment = createComment(data.details.firstName, data.details.lastName, data.details.content);

            commentSection.append(comment);

            commentPostContentInput.value = '';
        } else {
            createAlert('Error!', data.error);
        }
    });
});


deleteCommentBtns.forEach((btn) => {
    btn.addEventListener('click', function () {

        fetch('/api/CommentApi.php', {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                commentId: this.parentElement.dataset.id
            })
        })
        .then(response => response.json())
        .then(data => {
            console.log(data);

            if (data.result === true) {
                this.closest('.comment-item').remove();
            } else {
                createAlert('Error!', data.error);
            }
        });

    });
});


editCommentBtns.forEach((btn) => {
    btn.addEventListener('click', function () {

        this.parentElement.classList.add('hidden');

        let commentContentElement = this.closest('.comment-item').querySelector('.comment-content');

        let originalContent = commentContentElement.innerHTML;

        let decodedContent = decodeText(commentContentElement.innerHTML);

        commentContentElement.innerHTML = decodedContent;

        commentContentElement.setAttribute('contenteditable', 'true');

        let btns = document.createElement('div');

        btns.className = "comment-buttons ml-6 mt-4 flex gap-2";

        btns.innerHTML =
        `<button class="px-3 py-1 rounded bg-orange-500 text-white">Save</button>
        <button class="px-3 py-1 rounded bg-gray-500 text-white">Cancel</button>`

        commentContentElement.after(btns);


        btns.lastElementChild.addEventListener('click', function () {
            commentContentElement.removeAttribute('contenteditable');
            commentContentElement.innerHTML = originalContent;
            btns.remove();
        })

        btns.firstElementChild.addEventListener('click', () => {
            commentContentElement.removeAttribute('contenteditable');

            fetch('/api/CommentApi.php', {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    commentId: this.parentElement.dataset.id,
                    commentContent: commentContentElement.innerText
                })
            })
            .then(response => response.json())
            .then(data => {
                
                console.log(data);
                if (data.result === true) {
                    commentContentElement.innerHTML = data.content
                } else {
                    createAlert('Error!', data.error);
                }
            });
            btns.remove();
        })
        

    });
});

document.querySelectorAll('.comment-menu-btns').forEach((btn) => {
    btn.addEventListener('click', function () {
        this.nextElementSibling.classList.toggle('hidden');
    });
});

document.querySelectorAll('.react-btn').forEach((btn, index, elements) => {
    btn.addEventListener('click', function () {
    
        let method = "POST";
        let reactionElement = [];

        if (this.classList.contains('reacted')) {
            method = "DELETE";
        } else {
            reactionElement = [...elements].filter(item => item.classList.contains('reacted'));

            if (reactionElement.length > 0) {
                method = "PUT";
            }
        }

        fetch('/api/ReactionApi.php', {
            method: method,
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                postId: commentPostIdInput.value,
                type: this.dataset.type
            })
        })
        .then(response => response.json())
        .then(data => {
            
            console.log(data);

            let color = "dodgerblue";
            if (this.dataset.type == "Dislike") {
                color = "red";
            }

            if (data.result === true) {
                if (data.status == "Added" || data.status == "Updated") {
                    this.firstElementChild.style.fill = color;
                    this.nextElementSibling.textContent = +this.nextElementSibling.textContent + 1;
                    this.classList.add('reacted');
                } else if (data.status == "Deleted") {
                    this.firstElementChild.style.fill = "";
                    this.nextElementSibling.textContent = +this.nextElementSibling.textContent - 1;
                    this.classList.remove('reacted');
                } 
                
                if (data.status == "Updated") {
                    if (reactionElement.length > 0) {
                        reactionElement[0].firstElementChild.style.fill = "";
                        reactionElement[0].nextElementSibling.textContent = +reactionElement[0].nextElementSibling.textContent - 1;
                        reactionElement[0].classList.remove('reacted');
                    }
                }
            }
        });
    });
})

//-----------------------------------
// Post Menu
//-----------------------------------

postMenuBtn.addEventListener('click', function () {
    this.nextElementSibling.classList.toggle('hidden');
});

//-----------------------------------
// Delete Post
//-----------------------------------

document.querySelector('.delete-post').addEventListener('click', function () {
    postMenuBtn.nextElementSibling.classList.add('hidden');
    postDeleteConfimation.classList.remove('hidden');
    postDeleteConfimation.classList.add('flex');
});

let postDeleteConfimation = document.querySelector('.post-delete-confirmation');

postDeleteConfimation.querySelector('.close-btn').addEventListener('click', function () {
    postDeleteConfimation.classList.add('hidden');
    postDeleteConfimation.classList.remove('flex');
});

//-----------------------------------
// Edit Post
//-----------------------------------

let postTitle = document.querySelector('.post-title');
let postContent = document.querySelector('.post-content');
let postTags = document.querySelector('.post-tags');
let editBlock = document.querySelector('.edit-block');
let tagsBtns = document.querySelector('.tags-btns');

let originalContent;

document.querySelector('.edit-post').addEventListener('click', function () {
    postMenuBtn.nextElementSibling.classList.add('hidden');

    postTitle.lastElementChild.setAttribute('contenteditable', 'true');
    postTitle.classList.add('bg-gray-200');
    postTitle.firstElementChild.classList.remove('hidden');

    postContent.lastElementChild.setAttribute('contenteditable', 'true');
    postContent.classList.add('bg-gray-200');
    postContent.firstElementChild.classList.remove('hidden');

    postTags.classList.add('hidden');
    editBlock.classList.remove('hidden');

});

document.querySelectorAll('.tag-btn').forEach((btn) => {
    btn.addEventListener('click', function () {
        if (this.classList.contains('selected')) {
            this.classList.remove('selected', 'border-orange-500');
            // postTags.value = postTags.value.replace(this.dataset.tag + ',', '');
        } else {
            this.classList.add('selected', 'border-orange-500');
            // postTags.value += this.dataset.tag + ',';
        }
    });
});

document.querySelector('.cancel-edit-post').addEventListener('click', function () {
    postTitle.lastElementChild.removeAttribute('contenteditable');
    postTitle.classList.remove('bg-gray-200');
    postTitle.firstElementChild.classList.add('hidden');

    postContent.lastElementChild.removeAttribute('contenteditable');
    postContent.classList.remove('bg-gray-200');
    postContent.firstElementChild.classList.add('hidden');

    postTags.classList.remove('hidden');
    editBlock.classList.add('hidden');
});

document.querySelector('.save-edit-post').addEventListener('click', function () {

    let tagsString = "";
    tagsBtns.querySelectorAll('button.selected').forEach(item => {
        tagsString += item.dataset.id + ',';
    });

    fetch('/api/PostApi.php', {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            postId: +location.search.replace('?id=', ''),
            postTitle: postTitle.textContent.trim(),
            postContent: postContent.textContent.trim(),
            tags: tagsString
        })
    })
    .then(response => response.json())
    .then(data => {
        
        console.log(data);
        if (data.result === true) {
            
            postTitle.lastElementChild.removeAttribute('contenteditable');
            postTitle.classList.remove('bg-gray-200');
            postTitle.firstElementChild.classList.add('hidden');

            postContent.lastElementChild.removeAttribute('contenteditable');
            postContent.classList.remove('bg-gray-200');
            postContent.firstElementChild.classList.add('hidden');

            postTags.classList.remove('hidden');
            editBlock.classList.add('hidden');

            postTags.innerHTML = "";
            
            let tags = JSON.parse(data.tags);

            tags.forEach(tag => {
                let element = document.createElement('a');

                element.href = "#";
                element.className = "text-blue-500 font-semibold mr-5";

                element.innerHTML = `# ${tag[0]}`;

                postTags.append(element);

            });

        } else {
            createAlert('Error!', data.error);
        }
    });
});