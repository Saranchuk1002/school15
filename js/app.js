// Function to save the selected answer to local storage
function saveSelectedAnswer(questionId) {
    var answerId = document.querySelector('input[name="answer_id"]:checked').value;
    localStorage.setItem('selected_answer_' + questionId, answerId);
}

// Function to load the selected answer from local storage
function loadSelectedAnswer(questionId) {
    var answerId = localStorage.getItem('selected_answer_' + questionId);
    if (answerId) {
        document.querySelector('input[name="answer_id"][value="' + answerId + '"]').checked = true;
    }
}
