<?php
include_once  'db.php';
$id = (int) $_GET['id'];

$testId = $id;

$res = $db->query("SELECT * FROM tests WHERE id = {$testId}");
$row = $res->fetch();
$testTitle = $row['title'];

$res = $db->query("SELECT * FROM test_result WHERE test_id = {$testId}");
$results = $res->fetchAll();

?>
<div class="col-md-7" style=" position: relative; top: 300px;">
    <div class="card mt-4">
        <h5 class="card-header">Список тестов</h5>
        <div class="card-body">
            <ul class="main" >
                <?php
                $res = $db->query("SELECT * FROM tests");
                //вывод тестов на главную страницу
                while ($row = $res->fetch()) {
                    ?>
                    <li><a href="test.php?id=<?php echo $row['id']; ?>"><?php echo $row['title']; ?></a></li>
                    <a class="btn btn-warning" style="position: relative; left: 500px; top: -30px;"  href="results.php?id=<?php echo $row['id'] ?>">Подробнее</a>
                <?php } ?>
            </ul>
        </div>
    </div>
    <div class="card-body text-center">
        <a href="admin.php?do=constructor" class="btn btn-outline-primary">Добавить тест</a>
    </div>
</div>


