<?php
include_once 'db.php';
include('templates/header.php');
echo "<h1>Список тестов</h1>";
if (isset($_GET['subject_id'])) {
    $subject_id = $_GET['subject_id'];
    $sql = "SELECT * FROM tests WHERE subject_id = $subject_id";
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

