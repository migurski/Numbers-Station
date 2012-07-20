<?php

    require_once 'library.php';
    
    if(!is_numeric(ltrim($_SERVER['PATH_INFO'], '/')))
    {
        header('HTTP/1.1 400');
        header('Content-Type: text/plain');
        die("Not a number.\n");
    }
    
    $num = ltrim($_SERVER['PATH_INFO'], '/');
    $format = isset($_GET['format']) ? $_GET['format'] : 'text';
    
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
            break;
        
        default:
            header('Content-Type: text/plain');
            printf("%s\n", print_r($info, 1));
            echo $_SERVER['REQUEST_METHOD'];
    }

?>
