<?PHP

//$time_now=mktime(date('h')+0,date('i')+30,date('s'));
//$filename='session_track/session_'. date('d_m_h_i_A',$time_now) .'.zip';
$timezone = new DateTimeZone("Asia/Kolkata" );
$date = new DateTime();
$date->setTimezone($timezone ); 
$filename='session_track/session_'. $date->format('d_m_h_i_A') .'.zip';
$zip = new ZipArchive;
$zip->open($filename, ZipArchive::CREATE);
foreach (glob("var/session/*") as $file) {
    $zip->addFile($file);
    //if ($file!='target_folder/important.txt') unlink($file);
}
$zip->close();
//mail('bijal@krishinc.com','session track cron run successfully','http://www.criticalthinking.com/'.$filename .' created!!');
echo "done";
?>