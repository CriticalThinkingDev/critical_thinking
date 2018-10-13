<?php   
$dir = getcwd()."/var/session/";//dir absolute path
$interval = @strtotime('-120 hours');//files older than 5 Days
$i=0;

foreach (glob($dir."*") as $file)  { 
    //delete if older
    if (filemtime($file) <= $interval ) { 
        unlink($file); 
        $i++;
       
    } 
}
 echo $i. ' file(s) removed. Cron to renove 5 days old session files is executed!';
?>