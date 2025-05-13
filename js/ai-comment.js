document.addEventListener("DOMContentLoaded", function () {
    const form = document.querySelector("#commentform");
    if (!form) return;

    const commentField = form.querySelector("#comment");
    const messageArea = document.createElement("div");
    messageArea.id = "ai-spam-warning";
    messageArea.style.color = "red";
    messageArea.style.marginBottom = "10px";
    form.insertBefore(messageArea, form.firstChild);

    form.addEventListener("submit", function (e) {
        if (!commentField.value.trim()) return;

        e.preventDefault();

        messageArea.innerText = "Checking comment...";

        fetch(aiSpamAjax.ajax_url, {
            method: "POST",
            headers: {
                "Content-Type": "application/x-www-form-urlencoded"
            },
            body: new URLSearchParams({
                action: "ai_check_spam",
                nonce: aiSpamAjax.nonce,
                comment: commentField.value
            })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                messageArea.innerText = "";
                form.submit(); // safe to submit
            } else {
                messageArea.innerText = data.data.message || "Comment blocked.";
            }
        })
        .catch(() => {
            messageArea.innerText = "Error contacting spam checker.";
        });
    });
});