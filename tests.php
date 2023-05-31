<?php
include_once 'db.php';
include('templates/header.php');

if (isset($_GET['subject_id'])) {
    $subject_id = $_GET['subject_id'];
    $test_type = isset($_GET['test_type']) ? $_GET['test_type'] : '';

    if ($test_type === 'exam') {
        echo "<h1>Список контрольных тестов</h1>";
        $sql = "SELECT * FROM tests WHERE subject_id = $subject_id AND type = 'exam'";
    } elseif ($test_type === 'self_check') {
        echo "<h1>Список тестов для самоконтроля</h1>";
        $sql = "SELECT * FROM tests WHERE subject_id = $subject_id AND type = 'self_check'";
    } else {
        $sql = "SELECT * FROM tests WHERE subject_id = $subject_id";
    }

    $result = $db->query($sql);
    if ($result->rowCount() > 0) {
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            if ($row["type"] === "exam") {
                echo "<li><a href='javascript:void(0);' onclick='showPasswordPrompt(" . $row["id"] . ", \"" . $row["password"] . "\")'>" . $row["name"] . "</a></li>";
            } else {
                echo "<li><a href='test.php?id=" . $row["id"] . "'>" . $row["name"] . "</a></li>";
            }
        }
    } else {
        echo "Тесты отсутствуют.";
    }
} else {
    echo "Предмет не выбран.";
}
?>

<!-- Модальное окно ввода пароля -->
<div id="passwordModal" class="modal">
    <div class="modal-content">
        <h2>Введите пароль</h2>
        <input type="password" id="passwordInput">
        <button onclick="checkPassword()">Войти</button>
    </div>
</div>
<style>
    /* Стили для модального окна */
    .modal {
        display: none;
        position: fixed;
        z-index: 1;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        overflow: auto;
        background-color: rgba(0, 0, 0, 0.4);
    }

    .modal-content {
        background-color: #fefefe;
        margin: 15% auto;
        padding: 20px;
        border: 1px solid #888;
        width: 300px;
        text-align: center;
        border-radius: 5px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.3);
    }

    .modal input[type="password"] {
        width: 100%;
        padding: 10px;
        margin-bottom: 10px;
        border-radius: 5px;
        border: 1px solid #ccc;
    }

    .modal button {
        padding: 10px 20px;
        background-color: #4CAF50;
        color: #fff;
        border: none;
        border-radius: 5px;
        cursor: pointer;
    }

    .modal button:hover {
        background-color: #45a049;
    }
</style>

<script>
    var modal = document.getElementById("passwordModal");
    var passwordInput = document.getElementById("passwordInput");
    var currentTestId;
    var currentTestPassword;

    function showPasswordPrompt(testId, testPassword) {
        currentTestId = testId;
        currentTestPassword = testPassword;
        modal.style.display = "block";
        passwordInput.value = "";
        passwordInput.focus();
    }

    function checkPassword() {
        var password = passwordInput.value;

        if (password === currentTestPassword) {
            window.location.href = "test.php?id=" + currentTestId;
        } else {
            alert("Неверный пароль. Попробуйте снова.");
        }

        modal.style.display = "none";
    }

    window.onclick = function(event) {
        if (event.target === modal) {
            modal.style.display = "none";
        }
    };
</script>
