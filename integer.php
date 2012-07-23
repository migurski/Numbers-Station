<?php

    require_once 'library.php';
    putenv("TZ=America/Los_Angeles");
    
    if(!is_numeric(ltrim($_SERVER['PATH_INFO'], '/')))
    {
        header('HTTP/1.1 400');
        header('Content-Type: text/plain');
        die("Not a number.\n");
    }
    
    $num = ltrim($_SERVER['PATH_INFO'], '/');
    $format = isset($_GET['format']) ? $_GET['format'] : 'html';
    
    $db = connect_db();
    $info = get_integer($db, $num);

    mysql_close($db);
    
    if(is_null($info))
    {
        header('HTTP/1.1 404');
        header('Content-Type: text/plain');
        die("No such number here.\n");
    }
    
    switch($format)
    {
        case 'json':
            header('Content-Type: application/json');
            printf("%s\n", json_encode($info));
            exit();
        
        case 'text':
            header('Content-Type: text/plain');
            printf("%s\n", print_r($info, 1));
            exit();
    }

    header('Content-Type: text/html');

?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8" />
	<title>#<?= $info['number'] ?> (Mission Integers)</title>
    <link rel="stylesheet" type="text/css" media="all" href="<?= htmlspecialchars(get_base_dir()) ?>/style.css">
</head>
<body>
    <h1><a href="<?= htmlspecialchars(get_base_dir()) ?>/"><img src="<?= htmlspecialchars(get_base_dir()) ?>/logo.png" width="364" height="166" alt="Mission Integers"></a></h1>
    <h2>#<?= $info['number'] ?></h2>
    
    <p>
        Minted <?= date('l, F jS Y', $info['created']) ?>
        at <?= date('g:ia', $info['created']) ?>.
    </p>

</body>
</html>
