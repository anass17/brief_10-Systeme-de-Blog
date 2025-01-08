let commentSection = document.querySelector('.comments-section');
let commentForm = document.querySelector('.comment-form');
let commentSubmitBtn = commentForm.querySelector('.comment-submit-btn');
let commentPostIdInput = commentForm.querySelector('[name="post-id"]');
let commentPostContentInput = commentForm.querySelector('[name="post-content"]');
let deleteCommentBtns = document.querySelectorAll('.delete-comment-btn');
let editCommentBtns = document.querySelectorAll('.edit-comment-btn');


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