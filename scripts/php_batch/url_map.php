<?

ini_set('display_errors',1);


require_once("/www/vhosts/365admin.org/htdocs/conf/config.php");
require_once(BASE_PATH."/conf/brand_config.php");
require_once(BASE_PATH."/classes/session.php");
require_once(BASE_PATH."/classes/json.class.php");
require_once(BASE_PATH."/classes/db_pgsql.class.php");
require_once(BASE_PATH."/classes/logger.php");
require_once(BASE_PATH."/classes/Brand.php");


if (!is_object($oBrand))
{
    $oBrand = new Brand($aBrandConfig[HOSTNAME]);
}


$db = new db($dsn,$debug = false);


/*
$sql = "select * from article_map where section_uri like '/jobs/%' order by section_uri ASC";

$db->query($sql);

$aRows = $db->getRows();

foreach($aRows as $row)
{
    $from = "/jobs/";
    $to = "seasonal-jobs-working-holidays";
    $url_from = $row['section_uri'];
    $url_to = preg_replace($from, $to, $row['section_uri']);
    
    $sql = "insert into url_map (url_from, url_to, date) values ('".$url_from."','".$url_to."', now()::timestamp);";

    print_r($sql);
    print_r("\n");

    $sql = "update article_map set section_uri = '".$url_to."' where section_uri = (select section_uri from article_map where section_uri = '".$url_from."');";

    print_r($sql);
    print_r("\n");
    
    //print_r("\n\n");
    
}
*/


$sql = "select oid,* from article_map where section_uri like '/seasonal-jobs-working-holidays/%' order by section_uri ASC";

$db->query($sql);

$aRows = $db->getRows();

foreach($aRows as $row)
{
    
    print_r("-- ".$row['section_uri']."\n");
    
    $sql = "select 1 from article where id = ".$row['article_id'];
    
    $db->query($sql);
    
    if ($db->getNumRows() == 1)
    {
        $a = explode("/", $row['section_uri']);
        
        $new_title = "Seasonal Jobs & Working Holidays in ".ucfirst($a[count($a)-1]);
        
        $sql = "update article set title = '".$new_title."' where id = ".$row['article_id'].";";
        
        print_r($sql);
        print_r("\n");
    }

    $sql = "select * from article_map_opts where article_map_oid = ".$row['oid'];
    
    $db->query($sql);
    
    if ($db->getNumRows() == 1)
    {
        $sql = "update article_map_opts set opt_placement = 't', opt_article = 't' where article_map_oid = ".$row['oid'].";";
        
        print_r($sql);
        print_r("\n");
    } else {
        $sql = "insert into article_map_opts (article_map_oid, opt_placement, opt_article, opt_social) values (".$row['oid'].",'t','t','t');";

        print_r($sql);
        print_r("\n");        
    }
    
}

?>
