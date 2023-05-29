<?php
include_once 'db.php';
include('templates/header.php');


if (isset($_GET['subject_id'])) {
    $subject_id = $_GET['subject_id'];
    $test_type = isset($_GET['test_type']) ? $_GET['test_type'] : ''; // Получение значения типа теста

    // Формирование запроса в зависимости от значения типа теста
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
            echo "<li><a href='test.php?id=" . $row["id"] . "'>" . $row["name"] . "</a></li>";
        }
    } else {
        echo "Тесты отсутствуют.";
    }
} else {
    echo "Предмет не выбран.";
}
?>
