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
    
    $integer_href = sprintf('%s/integer/%d', dirname($_SERVER['SCRIPT_NAME']), $nums[0]);
    
    switch($format)
    {
        case 'json':
            header('HTTP/1.1 201');
            header('Content-Type: application/json');

            if($count == 1)
                header("Location: {$integer_href}?format=json");

            printf("%s\n", json_encode($nums));
            break;
        
        case 'text':
            header('HTTP/1.1 201');
            header('Content-Type: text/plain');

            if($count == 1)
                header("Location: {$integer_href}?format=text");

            printf("%s\n", join("\n", $nums));
            break;
        
        default:
            header('HTTP/1.1 201');
            header('Content-Type: text/html');

            if($count == 1)
                header("Location: {$integer_href}");

            echo '<h1>Your Integer(s):</h1>';
            
            foreach($nums as $num)
            {
                printf('<br><a href="%s/integer/%d">%d</a>',
                       htmlspecialchars(dirname($_SERVER['SCRIPT_NAME'])),
                       $num, $num);
            }
    }

?>
