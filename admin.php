<?php include_once 'db.php';include('templates/header.php') ?>
<!DOCTYPE html>
<html>
<link rel="stylesheet" href="css/style.css">
<body>
<div class="container">
    <?php
           if (!isset($_SESSION['login_mail']) || empty($_SESSION['login_mail'])) {
            header('location: login.php');
            exit;
        }

        // получить тип пользователя из базы данных
        $login_mail = $_SESSION['login_mail'];
        $res = $db->query("SELECT * FROM users WHERE username = '{$login_mail}'");
        $row = $res->fetch(PDO::FETCH_ASSOC);
        $user_type = $row['type'];

        $sql = "SELECT * FROM subjects";
        $result = $db->query($sql);


        if ($result->rowCount() > 0) {
            while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                echo "<a href='tests.php?subject_id=" . $row["id"] . "'>";
                echo "<h2>" . $row["name"] . "</h2>";
                echo "</a>";

                $sql2 = "SELECT * FROM tests WHERE subject_id=" . $row["id"];
                $result2 = $db->query($sql2);

                if ($result2->rowCount() > 0) {
                    echo "<ul>";
                    while ($row2 = $result2->fetch(PDO::FETCH_ASSOC)) {
                        echo "<li><a href='test.php?id=" . $row2["id"] . "'>" . $row2["name"] . "</a></li>";
                    }
                    echo "</ul>";
                } else {
                    echo "<p>Тесты отсутствуют.</p>";
                }
            }

            // отобразить кнопку только для администраторов
            if ($user_type == 'admin') {
                echo "<a href='constructor.php' class='btn btn-primary'>Добавить тест</a>";
                echo "<a href='add_questions.php' class='btn btn-primary' style='margin-left: 10px;'>Добавить вопрос</a>";

            }
        } else {
            echo "<p>Предметы отсутствуют.</p>";
        }
    ?>
