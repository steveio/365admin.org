<?


require_once("./conf/config.php");
require_once("./classes/db_pgsql.class.php");
require_once("./classes/logger.php");

$dsn = array("dbhost" => "localhost","dbuser" => "oneworld365_pgsql", "dbpass" => "tH3a1LAn6iA","dbname" => "oneworld365","dbport" => "5432");

$db = new db($dsn,$debug = false);

print_r($db);

$db->query("SELECT id,uname, pass from euser order by id DESC");


$result = $db->getRows();

if($db->getNumRows() < 1) {
	$db->last_error();
  	print_r("ERROR: postgres sql query");
}

$password_hash = "";

foreach($result as $row) {

	print_r($row['id']."\t".$row['uname']."\t".$row['pass']."\n");

	$password_hash = password_hash($row['pass'],PASSWORD_DEFAULT);

	$sql = "UPDATE euser SET pass_hash = '".$password_hash."' WHERE id = ".$row['id'];

	print_r($sql);

	$db->query($sql);

	if($db->getAffectedRows() != 1) {
		$db->last_error();
 		print_r("ERROR: postgres sql update query");
	}

}


//print password_hash("password123",PASSWORD_DEFAULT);

?>
