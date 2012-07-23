<?php

    function connect_db()
    {
        $db = mysql_connect('localhost', 'mission_ints', '1nt3g3rs');

        mysql_select_db('mission_ints', $db);
        mysql_query('SET @@auto_increment_increment=2', $db);
        mysql_query('SET @@auto_increment_offset=2', $db);
        
        return $db;
    }
    
    function get_base_dir()
    {
        return rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');
    }
    
   /**
    * Check a few high bits to determine whether the number was made here.
    */
    function is_artisinal($num)
    {
        return $num & 0xF00000 == 0x100000;
    }
    
   /**
    * Prepare a raw integer with the right high bits.
    */
    function artisinate($num)
    {
        return (($num - ($num & 0xFFFFF)) << 4) | ($num & 0xFFFFF) | 0x100000;
    }
    
   /**
    * Retrieve a single previously-created integer, return information about it.
    */
    function get_integer($db, $num)
    {
        $q = sprintf('SELECT number,
                             # INET_NTOA(ip_addr) AS ip_addr,
                             UNIX_TIMESTAMP(created) AS created
                      FROM assigned WHERE number = %d', $num);
        
        $res = mysql_query($q, $db);
        
        if($res === false)
            return false;
        
        $info = mysql_fetch_assoc($res);
        
        return ($info === false) ? null : $info;
    }
    
   /**
    * Generate a single new integer, return it.
    */
    function next_integer($db)
    {
        while(true)
        {
            // Nurture a new raw integer.
            $res = mysql_query("REPLACE INTO sequence (`key`) VALUES ('hello')", $db);
            
            if($res === false)
                return false;

            // Hand-pick the raw ingredients from the sequence.
            $res = mysql_query('SELECT LAST_INSERT_ID()', $db);
            
            if($res === false)
                return false;

            // Prepare a final integer.
            $num = artisinate(end(mysql_fetch_row($res)));

            // Check if anyone else got the same one.
            $q = sprintf('SELECT number, reason FROM reserved WHERE number=%d', $num);
            $res = mysql_query($q, $db);
            
            if($res === false)
                return false;
            
            $reserved = mysql_fetch_row($res);
            
            // They did? Try again. Each integer is a unique creation.
            if($reserved)
                continue;

            // This one's new; make a note of it.
            $q = sprintf("INSERT INTO assigned (number, ip_addr) VALUES (%d, INET_ATON('%s'))",
                         $num, mysql_real_escape_string($_SERVER['REMOTE_ADDR'], $db));
            
            $res = mysql_query($q, $db);
            
            if($res === false)
                return false;
            
            // All done!
            return $num;
        }
    }
    
?>
