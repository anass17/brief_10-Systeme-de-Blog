// -------------------------

function createAlert(type, message) {
    let alert = document.createElement('div');
    alert.className = "px-6 rounded border bg-gray-100 border-gray-400 fixed bottom-5 left-5 z-20 max-w-xl min-w-96 text-center shadow-lg py-4";

    alert.innerHTML = 
    `<h3 class="mb-2 font-semibold">${type}</h3>
    <p class="text-gray-600">${message}</p>`;

    document.body.append(alert);

    setTimeout(() => {
        alert.remove();
    }, 4000);
}

// -------------------------

function createComment(first_name, last_name, content) {
    let comment = document.createElement('div');

    comment.className = "py-5";
    comment.innerHTML = 
        `<div class="flex items-start justify-between">
            <div class="flex gap-5">
                <div class="w-12 h-12 rounded-full overflow-hidden border-2 border-orange-500">
                    <img src="/assets/imgs/users/default.webp" alt="">
                </div>
                <div>
                    <h3 class="flex items-center gap-3">${first_name} ${last_name}<span>•</span><span>You</span></h3>
                    <span class="text-gray-500 text-sm">Now</span>
                </div>
            </div>
            <button type="button" class="text-gray-500 tracking-widest w-8 h-8">
                •••
            </button>
        </div>
        <div class="whitespace-pre-line mt-4 text-gray-700 border-l-2 py-2 ml-6 border-gray-500 pl-4">${content}</div>`;

    return comment;

}

// -------------------------------

function decodeText(text) {
    let decodedComment = text.replace(/<h2 .+?>/g, '# ');
    decodedComment = decodedComment.replace(/<\/h2>/g, '\n');
    decodedComment = decodedComment.replace(/(<b>|<\/b>)/g, '**');

    return decodedComment;
}