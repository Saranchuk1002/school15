<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// Подключаемся к базе данных
include_once 'db.php';

// Получаем данные из формы
$name = isset($_POST['name']) ? $_POST['name'] : '';
$time = isset($_POST['time']) ? $_POST['time'] : '';
$subject_id = isset($_POST['subject_id']) ? $_POST['subject_id'] : '';
$class_id = isset($_POST['class_id']) ? $_POST['class_id'] : '';

// Добавляем тест в базу данных
if (!empty($name) && !empty($time) && !empty($subject_id) && !empty($class_id)) {
    $query = $db->prepare("INSERT INTO tests (name, time, subject_id, class_id) VALUES (?, ?, ?, ?)");
    $query->execute([$name, $time, $subject_id, $class_id]);

    // Получаем ID добавленного теста
    $test_id = $db->lastInsertId();

    // Добавляем каждый вопрос в базу данных
    foreach ($_POST as $name => $value) {
        if (strpos($name, 'question-') === 0) {
            // Получаем текст вопроса и баллы за него
            $question_text = isset($_POST[$name]) ? $_POST[$name] : '';
            $score = isset($_POST[$name . '-points']) ? $_POST[$name . '-points'] : '';

            // Проверяем наличие обязательных значений
            if (!empty($question_text) && !empty($score)) {
                // Добавляем вопрос в базу данных
                $query = $db->prepare("INSERT INTO questions (question_text, test_id) VALUES (?, ?)");
                $query->execute([$question_text, $test_id]);

                // Получаем ID добавленного вопроса
                $question_id = $db->lastInsertId();

                // Добавляем каждый ответ к этому вопросу в базу данных
                foreach ($_POST as $name => $value) {
                    if (strpos($name, 'question-') === 0 && strpos($name, '-answer-') !== false) {
                        // Получаем текст ответа и баллы за него
                        $answer_text = isset($_POST[$name]) ? $_POST[$name] : '';
                        $score = isset($_POST[$name . '-points']) ? $_POST[$name . '-points'] : '';

                        // Проверяем наличие обязательных значений
                        if (!empty($answer_text) && !empty($score)) {
                            // Добавляем ответ в базу данных
                            $query = $db->prepare("INSERT INTO answers (question_id, answer_text, score) VALUES (?, ?, ?)");
                            $query->execute([$question_id, $answer_text, $score]);
                        }
                    }
                }
            }
        }
    }


// Перенаправляем пользователя на страницу со списком тестов
            header("Location: admin.php");
            exit(); // рекомендуется использовать exit() после перенаправления, чтобы код после него не выполнялся.
}
