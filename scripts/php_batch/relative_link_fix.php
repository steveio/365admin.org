<?php

require_once("../../conf/config.php");
require_once(BASE_PATH."/classes/db_pgsql.class.php");
require_once(BASE_PATH."/classes/logger.php");

$dsn = array("dbhost" => "localhost","dbuser" => "", "dbpass" => "","dbname" => "","dbport" => "5432");


$db = new db($dsn,$debug = false);

$sql = "select a.id, m.section_uri, a.full_desc from article a, article_map m where a.id = m.article_id order by last_updated desc";

print_r(var_dump($sql));

$db->query($sql);

foreach($db->getRows() as $aRow)
{
    $aLines = explode("\n", $aRow['full_desc']);
    $aNewLines = array();
    $bPatternMatch = false;
    
    foreach($aLines as $line)
    {

        if (preg_match('/\/jobs\//xs', $line))
        {
            print_r($aRow['id']. "  " . $aRow['section_uri']."\n");
            
            $bPatternMatch = true;

            print_r("\nPattern Match: \n");
            print_r($line);
            
            $new_line =  preg_replace("/jobs/xs", "seasonal-jobs-working-holidays", $line);

            print_r("\nUpdated Pattern: \n");
            print_r($new_line);

            $aNewLines[] = $new_line;
            
            print_r("\n\n\n");
        } else {
            $aNewLines[] = $line;
        }        
    }

    if ($bPatternMatch)
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
