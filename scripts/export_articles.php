<?php
ini_set('display_errors',1);


require_once("../conf/config.php");
require_once(BASE_PATH."/classes/db_pgsql.class.php");
require_once(BASE_PATH."/classes/logger.php");

$dsn = array("dbhost" => "localhost","dbuser" => "oneworld365_pgsql", "dbpass" => "","dbname" => "oneworld365","dbport" => "5432");
$db = new db($dsn,$debug = false);

print_r($db);

print $sql = "select
a.title,
a.short_desc,
a.full_desc
from article a LEFT OUTER JOIN article_map m on a.id = m.article_id
where m.section_uri is NULL
order by title ASC;
";

$db->query($sql);

$result = $db->getObjects();

foreach($result as $o) {
    print_r($o->title);
    print_r("\n");
    print_r(strip_tags(html_entity_decode($o->short_desc)));
    print_r("\n");
    print_r(strip_tags(html_entity_decode($o->full_desc)));
    print_r("\n\n");
    print_r("...............................................................................");
    print_r("\n\n");
}


print $sql = "select
a.title,
a.short_desc,
a.full_desc
from article a LEFT OUTER JOIN article_map m on a.id = m.article_id
where m.section_uri is NULL OR (m.website_id is not NULL AND m.website_id != 0)
order by title ASC;
";

$db->query($sql);

$result = $db->getObjects();

foreach($result as $o) {
    print_r($o->title);
    print_r("\n");
    print_r(strip_tags(html_entity_decode(stripslashes($o->short_desc))));
    print_r("\n");
    print_r(strip_tags(html_entity_decode(stripslashes($o->full_desc))));
    print_r("\n\n");
    print_r("...............................................................................");
    print_r("\n\n");
}

?>
