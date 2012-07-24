<?php

    require_once 'library.php';
    
    if($_SERVER['REQUEST_METHOD'] != 'POST')
    {
        header('HTTP/1.1 405');
        header('Allow: POST');
        header('Content-Type: text/plain');
        die("Please submit a POST request to get your integer.\n");
    }
    
    if(isset($_POST['count']) && !is_numeric($_POST['count']))
    {
        header('HTTP/1.1 403');
        header('Content-Type: text/plain');
        die("Please use a number for the count.\n");
    }
    
    $count = is_numeric($_POST['count']) ? intval($_POST['count']) : 1;
    $format = isset($_POST['format']) ? $_POST['format'] : 'html';
    
    if($count <= 0 || 10 < $count)
    {
        header('HTTP/1.1 403');
        header('Content-Type: text/plain');
        die("Please use a positive number under 10 for the count.\n");
    }
    
    $db = connect_db();
    $nums = array();
    
    for($i = 0; $i < $count; $i++)
    {
        mysql_query('BEGIN', $db);
        $nums[] = next_integer($db);
        mysql_query('COMMIT', $db);
    }

    mysql_close($db);
    
    $integer_href = sprintf('%s/integer/%d', get_base_dir(), $nums[0]);
    
    switch($format)
    {
        case 'json':
            header('HTTP/1.1 201');
            header('Content-Type: application/json');

            if($count == 1)
                header("Location: {$integer_href}?format=json");

            printf("%s\n", json_encode($nums));
            exit();
        
        case 'text':
            header('HTTP/1.1 201');
            header('Content-Type: text/plain');

            if($count == 1)
                header("Location: {$integer_href}?format=text");

            printf("%s\n", join("\n", $nums));
            exit();
    }

    header('HTTP/1.1 201');
    header('Content-Type: text/html');

    if($count == 1)
        header("Location: {$integer_href}");

?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8" />
	<title>Your Integers (Mission Integers)</title>
    <link rel="stylesheet" type="text/css" href="style.css" media="all">
    <link rel="stylesheet" type="text/css" href="narrow.css" media="only screen and (max-device-width: 400px)">
    <meta name="viewport" content="width = device-width, initial-scale = 1.0">
    <meta name="apple-mobile-web-app-capable" content="yes">
</head>
<body>
    <h1><a href="./"><img src="logo.png" width="364" height="166" alt="Mission Integers"></a></h1>
    <h2>Your Integers</h2>
    
    <ul>
        <? foreach($nums as $num) { ?>
            <li><a href="<?= htmlspecialchars(get_base_dir()) ?>/integer/<?= $num ?>"><?= $num ?></a></li>
        <? } ?>
    </ul>

</body>
</html>
