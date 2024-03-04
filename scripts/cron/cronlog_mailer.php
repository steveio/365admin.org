<?
$path = "/home/web_developer/cron/";
$f = file_get_contents($path."df.out");
$f2 = file_get_contents($path."biggest_files.out");
$f3 = file_get_contents($path."oneworld_db_size.out");

$message = "Disk Useage :\n\n" . $f ."\n\nLargest Files : \n\n" . $f2 ."\n\nDatabase Size: oneworld365.org\n\n" . $f3 ."\n\nDatabase Size: phpbb\n\n"; 
$to = "steve@oneworld365.org";
$subject = "Daily Status Report for : oneworld365.org";
$headers = 'From: root@oneworld365.org' . "\r\n";

mail($to, $subject, $message, $headers);

?>

