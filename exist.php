<?PHP
echo phpinfo();exit;
//if(file_exists('var/session/sess_145ef8648637be923b1ae1bafc40c280')) { // commented on 08/12/2014 12:08:00
if(file_exists('var/session/sess_acb3979e5481ff67275c5be1eff0805e')) {
    echo "exists - ";
} else {
    echo "not exists - ";
}
?>
<?PHP
date_default_timezone_set("America/Los_Angeles");
$dt = new DateTime();
$timezone1 = new DateTimeZone("America/Los_Angeles");
$dt->setTimezone($timezone1);
echo $dt->format('m/d/Y H:i:s');
$timezone = new DateTimeZone("Asia/Kolkata" );
$date = new DateTime();
$date->setTimezone($timezone );
echo  ' -- '. $date->format('m/d/Y H:i:s');
?>