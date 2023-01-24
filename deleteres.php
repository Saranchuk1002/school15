<?php
    include_once  'db.php';
    $testId=$_POST['testId'];
    $db->query("DELETE FROM test_result WHERE id = {$test_result_id}");
    header ('location: result.php');
    ?>
