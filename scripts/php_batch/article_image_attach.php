<?php

require_once("../conf/config.php");
require_once(BASE_PATH."/classes/db_pgsql.class.php");
require_once(BASE_PATH."/classes/logger.php");

$dsn = array("dbhost" => "localhost","dbuser" => "oneworld365_pgsql", "dbpass" => "bra@zi1","dbname" => "oneworld365_20240218","dbport" => "5432");

$db = new db($dsn,$debug = false);
print_r(var_dump($db->db));


$sql = "select 
        a1.id as article_id,
        a1.title as title,
        a1.full_desc 
        from 
        article a1
        where not exists
        ( 
        select 
        1
        from 
        image_map m, 
        image i, 
        article a 
        where 
        m.img_id = i.id
        and m.link_to = 'ARTICLE' 
        and m.link_id = a.id 
        and m.link_id = a1.id)";

print_r(var_dump($sql));

$db->query($sql);

foreach($db->getRows() as $aRow)
{
    // try to grab an image from article body text
    $html = $aRow['full_desc'];
    $aImgUrl = array();
    preg_match_all( '|<img.*?src=[\'"](.*?)[\'"].*?>|i',$html, $aImgUrl );

    if (count($aImgUrl[1]) > 1)
    {
        print_r($aRow['article_id']." :: ".$aRow['title']);
        print_r("\n");
        print_r(count($aImgUrl)."\n");
        print_r($aImgUrl);
        print_r("\n\n\n");
    }
    
}


?>