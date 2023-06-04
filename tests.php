<?php
include_once 'db.php';
include('templates/header.php');
?>
<div class="container">
    <?php
    if (isset($_GET['subject_id'])) {
        $subject_id = $_GET['subject_id'];
        $test_type = isset($_GET['test_type']) ? $_GET['test_type'] : '';

        // Вывод заголовка перед циклом while
        if ($test_type === 'self_check') {
            echo "<h1 style='text-align: center;'>Список тестов для самоконтроля</h1>";
        } elseif ($test_type === 'exam') {
            echo "<h1 style='text-align: center;'>Контрольные тесты</h1>";
        }
    ?>
        </div>
<div class="container_test">
<?php
        if ($test_type === 'exam') {
            $sql = "SELECT * FROM tests WHERE subject_id = $subject_id AND type = 'exam'";
        } elseif ($test_type === 'self_check') {
            $sql = "SELECT * FROM tests WHERE subject_id = $subject_id AND type = 'self_check'";
        } else {
            $sql = "SELECT * FROM tests WHERE subject_id = $subject_id";
        }

        $result = $db->query($sql);
        if ($result->rowCount() > 0) {
            while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                if ($row["type"] === "exam") {
                    echo "<a class='subject-title' href='javascript:void(0);' onclick='showPasswordPrompt(" . $row["id"] . ", \"" . $row["password"] . "\")'>" . $row["name"] . "</a>";
                } else {
                    echo "<a class='subject-title' href='test.php?id=" . $row["id"] . "'>" . $row["name"] . "</a>";
                }
            }
        } else {
            echo "Тесты отсутствуют.";
        }
    } else {
        echo "Предмет не выбран.";
    }
    ?>
</div>


<!-- Модальное окно ввода пароля -->
<div id="passwordModal" class="modal">
    <div class="modal-content">
        <h2>Введите пароль</h2>
        <input type="password" id="passwordInput">
        <button onclick="checkPassword()">Войти</button>
    </div>
</div>
<style>
    body {
        background-color: #222;
        color: #fff;
    }

    .container {
        display: flex;
        justify-content: center;
        align-items: center;
        margin: 20px auto;
        max-width: 800px;
        padding: 20px;
        background-color: #333;
        border-radius: 8px;
    }
    .container_test {
        margin: 20px auto;
        max-width: 800px;
        padding: 20px;
        background-color: #333;
        border-radius: 8px;
        display: flex;
        flex-direction: column;
        align-items: flex-start;
    }
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
        background-color: #484848;
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
    h1 {
        text-align: center;
        margin-bottom: 30px;
    }

    form {
        text-align: center;
        margin-bottom: 20px;
    }

    label {
        color: #fff;
    }

    select {
        padding: 8px 12px;
        font-size: 16px;
        border: none;
        background-color: #555;
        color: #fff;
        border-radius: 4px;
    }

    .subject {
        margin-bottom: 30px;
    }

    .subject-title {
        font-size: 24px;
        margin-bottom: 10px;
        transition: color 0.3s ease;
    }

    .subject-title:hover {
        color: #ffca28;
    }

    .test-list li {
        margin-bottom: 5px;
    }

    .admin-buttons {
        text-align: center;
        margin-top: 30px;
    }

    .btn {
        display: inline-block;
        padding: 10px 20px;
        margin-left: 30px;
        font-size: 16px;
        background-color: #ffca28;
        color: #222;
        text-decoration: none;
        border-radius: 4px;
        transition: background-color 0.3s ease;
    }

    .btn:hover {
        background-color: #ffc107;
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
