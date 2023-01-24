<?php
    include_once  'db.php';
    $testId=$_POST['testId'];
    $db->query("DELETE FROM tests WHERE id = {$testId}");
    header ('location: admin.php');
    ?>