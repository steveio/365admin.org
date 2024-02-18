<?php

require_once("./conf/config.php");
require_once(BASE_PATH."/classes/db_pgsql.class.php");
require_once(BASE_PATH."/classes/logger.php");

$dsn = array("dbhost" => "localhost","dbuser" => "oneworld365_pgsql", "dbpass" => "bra@zi1","dbname" => "oneworld365_20240218","dbport" => "5432");

$db = new db($dsn,$debug = false);
print_r(var_dump($db->db));

$sql = "update article set full_desc = '<a href=\"/country/costa-rica\">Costa Rica Holidays</a>\n <a href=\"country/costa-rica\">Costa Rica Holidays</a>\n <a href=\"http://www.website.com/country/costa-rica\">Costa Rica Holidays</a>' where id = 4250;";

$db->query($sql);

$sql = "select a.id, m.section_uri, a.full_desc from article a, article_map m where a.id = m.article_id order by last_updated desc";

print_r(var_dump($sql));

$db->query($sql);

foreach($db->getRows() as $aRow)
{
    $aLines = explode("\n", $aRow['full_desc']);
    $aNewLines = array();
    $bHasBrokenLink = false;

    foreach($aLines as $line)
    {

        if (preg_match('/(href=\"(?!http|www|bit|\/))/xs', $line))
        {
            $bHasBrokenLink = true;

            print_r($aRow['id']. "  " . $aRow['section_uri']);
            print_r("\nBroken Relative link: \n");
            print_r($line);
            
            $new_line =  preg_replace("/(href=\")/xs", "href=\"/", $line);

            print_r("\nFixed relative link: \n");
            print_r($new_line);

            $aNewLines[] = $new_line;
            
            print_r("\n\n\n");
        } else {
            $aNewLines[] = $line;
        }        
    }

    if ($bHasBrokenLink)
    {
        $full_desc = implode("\n",$aNewLines);
        
        $sql = "update article set full_desc = '".pg_escape_string($full_desc)."' where id=".$aRow['id'];

        $db->query($sql);
        
        if ($db->getAffectedRows() == 1)
        {
            print_r("\n");
            print_r("OK");
            print_r("\n\n");
        } else {
            print_r("\n");
            print_r("ERROR - update failed \n");
            print_r("\n\n");
        }
    }
}


?>