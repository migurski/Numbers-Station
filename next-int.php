<?php

    require_once 'library.php';
    
    if(isset($_GET['count']) && !is_numeric($_GET['count']))
    {
        header('HTTP/1.1 403');
        header('Content-Type: text/plain');
        die("Please use a number for the count.\n");
    }
    
    $count = is_numeric($_GET['count']) ? intval($_GET['count']) : 1;
    $format = isset($_GET['format']) ? $_GET['format'] : 'text';
    
    if($count > 10)
    {
        header('HTTP/1.1 403');
        header('Content-Type: text/plain');
        die("Please use a number under 10 for the count.\n");
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
    
    switch($format)
    {
        case 'json':
            header('Content-Type: application/json');
            printf("%s\n", json_encode($nums));
            break;
        
        default:
            header('Content-Type: text/plain');
            printf("%s\n", join("\n", $nums));
    }

?>
