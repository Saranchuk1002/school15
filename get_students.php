<?php
include_once 'db.php';



if (isset($_GET['class_id'])) {
    $classId = $_GET['class_id'];

    if ($classId === '') {
        $usersQuery = $db->prepare("SELECT * FROM users");
        $usersQuery->execute();
        $users = $usersQuery->fetchAll(PDO::FETCH_ASSOC);
    }


    // Запрос к базе данных для получения списка учеников
    $usersQuery = $db->prepare("SELECT * FROM users WHERE class_id = ?");
    $usersQuery->execute([$classId]);
    $users = $usersQuery->fetchAll(PDO::FETCH_ASSOC);

    // Создание массива данных с учениками
    $response = array();
    foreach ($users as $user) {
        $response[] = array(
            'id' => $user['id'],
            'full_name' => $user['full_name']
        );
    }

    // Установка заголовка ответа на JSON
    header('Content-Type: application/json');

    // Возвращение JSON-ответа
    echo json_encode($response);
}
?>
