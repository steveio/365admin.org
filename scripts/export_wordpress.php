<?php

require_once("../conf/config.php");
require_once(BASE_PATH."/classes/db_pgsql.class.php");
require_once(BASE_PATH."/classes/logger.php");

$dsn = array("dbhost" => "localhost","dbuser" => "", "dbpass" => "","dbname" => "","dbport" => "5432");

define("IMG_BASE_PATH","/www/vhosts/oneworld365.org/htdocs/img/");

define( ABSPATH, '/www/vhosts/365wordpress/wordpress/' );
define( WPINC, 'wp-includes' );

define( 'WP_DEBUG', true );
define( 'WP_DEBUG_DISPLAY', true );
define( 'WP_DEBUG_LOG', true );

define( 'SAVEQUERIES', false );

define( 'DB_NAME', '' );
/** Database username */
define( 'DB_USER', '' );
/** Database password */
define( 'DB_PASSWORD', '' );
/** Database hostname */
define( 'DB_HOST', 'localhost' );
/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8' );
/** The database collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );


$db = new db($dsn,$debug = false);
//print_r(var_dump($db->db));

$aTopLevelPageId = array();
$aLev2PageId = array();
$aLev3PageId = array();

$iOrphanId = null;
$iActivityId = null;
//$iCategoryId = null;
$iCountryId = null;
$iContinentId = null;

//require_once("/www/vhosts/365wordpress/wordpress/wp-admin/includes/noop.php");
require_once("/www/vhosts/365wordpress/wordpress/wp-includes/load.php");
require_once("/www/vhosts/365wordpress/wordpress/wp-includes/option.php");
require_once("/www/vhosts/365wordpress/wordpress/wp-includes/functions.php");
require_once("/www/vhosts/365wordpress/wordpress/wp-includes/plugin.php");
require_once("/www/vhosts/365wordpress/wordpress/wp-includes/class-wp-error.php");
require_once("/www/vhosts/365wordpress/wordpress/wp-includes/class-wpdb.php");

$db_wpdb = new wpdb(DB_USER, DB_PASSWORD, DB_NAME, DB_HOST);

//print_r(var_dump($db_wpdb));

//processLegacyNav();
//die();

$sql = "
select
 a.id,
 1 as post_author,
 a.created_date as post_date,
 a.created_date as post_date_gmt,
 a.full_desc as post_content,
 a.title as post_title,
 a.short_desc as post_excerpt,
 'publish' as post_status,
 'closed' as comment_status,
 'closed' as ping_status,
 '' as post_password,
 --REPLACE(m.section_uri, '/blog/','') as post_name,
 m.section_uri as post_name,
 '' as to_ping,
 '' as pinged,
 a.published_date,
 a.last_updated as post_modified,
 a.last_updated as post_modified_gmt,
 '' as post_content_filtered,
 1 as post_parent,
 '' as guid,
 0  as menu_order,
 'page' as post_type,
 '' as post_mime_type,
 0 as comment_count,
(select 1 from activity act where m.section_uri = '/'||act.url_name) as is_activity,
(select 1 from category cat where m.section_uri = '/'||cat.url_name) as is_category,
(select 1 from country cty where m.section_uri = '/travel/'||cty.url_name) as is_country,
(select 1 from continent ctn where m.section_uri = '/continent/'||ctn.url_name) as is_continent
 from article a, article_map m
where a.id = m.article_id
and m.section_uri like '/blog%'
and m.website_id = 0
order by m.section_uri asc, a.id asc
";


print_r("Running query:\n ".$sql."\n");

$db->query($sql);

$result = $db->getRows();

if($db->getNumRows() < 1) {
	$db->last_error();
  print_r("ERROR: postgres sql query");
}

$iter = 0;

insertTopLevelPages();


foreach($result as $row) {

    migrateImages($row['id']);

    continue;

        print_r("Insert row: ".++$iter."\n");

        // count number of / chars in page_name
        $uriSegCount = substr_count(
            $row['post_name'],
            "/"
        );

        print_r("Processing Page: ".$row['post_name'].",  URI segs: ".$uriSegCount."\n");

        $aUrlSegs = getUrlSegments($row['post_name']);
        print_r($aUrlSegs);
        $post_name = getPageUrl($aUrlSegs[count($aUrlSegs) -1]);


        $aPageType = array();
        $aPageType['is_activity'] = $row['is_activity'];
        $aPageType['is_category'] = $row['is_category'];
        $aPageType['is_country'] = $row['is_country'];
        $aPageType['is_continent'] = $row['is_continent'];

        $data = array(
          'post_author' => $row['post_author'],
          'post_date' => $row['post_date'],
          'post_date_gmt' => $row['post_date_gmt'],
          'post_content' => stripslashes($row['post_content']),
          'post_title' => $row['post_title'],
          'post_excerpt' => stripslashes($row['post_excerpt']),
          'post_status' => $row['post_status'],
          'comment_status' => $row['comment_status'],
          'ping_status' => $row['ping_status'],
          'post_password' => $row['post_password'],
          'post_name' => $post_name,
          'to_ping' => $row['to_ping'],
          'pinged' => $row['pinged'],
          'post_modified' => ($row['post_modified'] == "") ? $row['published_date'] : $row['post_modified'],
          'post_modified_gmt' => ($row['post_modified'] == "") ? $row['published_date'] : $row['post_modified'],
          'post_content_filtered' => $row['post_content_filtered'],
          'post_parent' => getPostParentId($post_name, $aPageType, $iOrphanId),
          'guid' => $row['guid'],
          'menu_order' => $row['menu_order'],
          'post_type' => $row['post_type'],
          'post_mime_type' => $row['post_mime_type'],
          'comment_count' => $row['comment_count']
        );

        /*
         handle page tree hierarchy
        */

        if ($uriSegCount > 1)
        {

          print_r("Create parent stub for gaps in URL hierarchy \n");

          $iLastParentId = 0;

          for($i = 0; $i < $uriSegCount-1; $i++)
          {
              if ($i == 0)
              {
                $iLastParentId = $iOrphanId;
              }

              $strSeg = $aUrlSegs[$i];
              print_r("Processing URL segment: ".$strSeg."\n");

              $page_id = getPageId($strSeg);

              if ($page_id == FALSE)
              {
                print_r("Create page: ".$strSeg."\n");

                $new_page_data = array(
                  'post_author' => $row['post_author'],
                  'post_date' => $row['post_date'],
                  'post_date_gmt' => $row['post_date_gmt'],
                  'post_content' => '',
                  'post_title' => "Stub for ".ucfirst(preg_replace("/\-/"," ",$strSeg)),
                  'post_excerpt' => '',
                  'post_status' => 'draft',
                  'comment_status' => $row['comment_status'],
                  'ping_status' => $row['ping_status'],
                  'post_password' => $row['post_password'],
                  'post_name' => $strSeg,
                  'to_ping' => $row['to_ping'],
                  'pinged' => $row['pinged'],
                  'post_modified' => ($row['post_modified'] == "") ? $row['published_date'] : $row['post_modified'],
                  'post_modified_gmt' => ($row['post_modified'] == "") ? $row['published_date'] : $row['post_modified'],
                  'post_content_filtered' => $row['post_content_filtered'],
                  'post_parent' => getPostParentId($strSeg, $aPageType, $iLastParentId),
                  'guid' => $row['guid'],
                  'menu_order' => $row['menu_order'],
                  'post_type' => $row['post_type'],
                  'post_mime_type' => $row['post_mime_type'],
                  'comment_count' => $row['comment_count']
                );

                $iLastParentId = insertPage($new_page_data);
              } else {
                $iLastParentId = $page_id;
              }

              $data['post_parent'] = $iLastParentId;
          }

        }

        insertPage($data);

        print_r("\n\n...........................\n");

}

print_r("\n\nSUCCESS: Insert loop complete\n\n");

//processLegacyNav();
die();


function insertPage($data)
{
  global $db_wpdb;

  print_r("INSERT page... \n");

  print_r("post_name = ".$data['post_name']."\n");
  print_r("post_parent = ".$data['post_parent']."\n");


  // A format is one of '%d', '%f', '%s' (integer, float, string).
  $formats = array("%d","%s","%s","%s","%s","%s","%s","%s","%s","%s","%s","%s","%s","%s","%s","%s","%d","%s","%d","%s","%s","%d");


  try {
    $result = $db_wpdb->insert( 'wp_posts', $data , $formats );


    print_r("Insert Id: ".$db_wpdb->insert_id."\n");
    print_r("Rows: ".$result."\n");
    //print_r("\n\n".$db_wpdb->last_query."\n\n");

    if ($result === FALSE)
    {
      print_r("DB INSERT ERROR\n");
      print_r($db_wpdb->last_error."\n");
      print_r($db_wpdb);
      exit();
    }
  } catch (Exception $e) {
    print_r($e);
  }

  unset($data['post_excerpt']);
  unset($data['post_content']);
  print_r($data);

  return $db_wpdb->insert_id;
}

function migrateImages($iArticleId)
{
  global $db;

  $aImage = array();

  $sql = "
  SELECT
    i.*,
    m.type
  FROM
  image_map m,
  image i,
  article ar
  WHERE
    m.img_id = i.id
    AND m.link_to = 'ARTICLE'
    AND m.link_id = ".$iArticleId."
    AND m.link_id = ar.id
  ORDER BY i.id ASC
  ";

  $db->query($sql);

  $result = $db->getRows();

  if($db->getNumRows() < 1) {
    print_r("No Attached Images: ".$iArticleId."\n");
  }

  foreach($result as $row)
  {
      print_r($row);
      $strPath = GetPath($row['id'], $row['ext']);
  }

}

function GetPartition($id)
{

  $i = sprintf("%08d", $id);

  $a[] = array();
  $a[0] = substr($i,0,3);
  $a[1] = substr($i,3,2);

  return $a[0]."/".$a[1];
}

function GetPath($id, $ext)
{

  $sPath = IMG_BASE_PATH . GetPartition($id);
  return $sPath."/".$id.$ext;
}

function getPostParentId($strPostName, $aTopLevelPage, $iDefault)
{
  global $aTopLevelPageId,$aLev2PageId,$iActivityId,$iCountryId,$iContinentId;

  //print_r($aTopLevelPage);

  if ($strPostName == "blog")
  {
    return 0;
  }

  switch($aTopLevelPage)
  {
    case $aTopLevelPage['is_activity'] == 1 :
      print_r("is_activity: 1\n");
      return $iActivityId;
      break;
    case $aTopLevelPage['is_country'] == 1 :
      print_r("is_country: 1\n");
      return $iCountryId;
      break;
    case $aTopLevelPage['is_continent'] == 1 :
      print_r("is_continent: 1\n");
      return $iContinentId;
      break;
    default:
    }

    return $iDefault;
}


function getUrlSegments($uri)
{
  $uri = preg_replace("/^\//","",$uri);
  return $aSegs =  preg_split('/\//', $uri, -1, PREG_SPLIT_NO_EMPTY);
}

// convert uri to wordpress dash delimter format
//  eg
//  /animal-volunteer-projects/baboons to
//  animal-volunteer-projects-baboons
function getPageUrl($uri)
{
  return preg_replace("/\//", "-",preg_replace("/^\//","",$uri));

}

function getPageTitle($uri)
{
  $uri = preg_replace("/^\//","",$uri);
  //print_r($uri);
  $aSegs =  preg_split('/\//', $uri, -1, PREG_SPLIT_NO_EMPTY);
  //print_r($aSegs);

  $parent_title = "";
  unset($aSegs[count($aSegs)-1]);
  $i = 0;
  foreach($aSegs as $strSeg)
  {
    if ($i++ < count($aSegs)-1)
    {
      $parent_title .= ucfirst($strSeg)." ";
    } else {
      $parent_title .= ucfirst($strSeg);
    }
  }
  return $parent_title;
}

function getPageId($strPostName)
{
  global $db_wpdb;

  if ($strPostName == "")
  {
    return FALSE;
  }

  $sql = 'select id from wp_posts where post_name = "'.$strPostName.'"';

  print_r($sql."\n");

  $aRow = $db_wpdb->get_row( $sql, ARRAY_N, $y = 0 );

  //print_r($db_wpdb);

  if ($aRow !== false && count($aRow) == 1)
  {
    return $aRow[0];
  }

  print_r("ERROR: page does not exist, id: ".$strPostName." \n");

  return FALSE;
}

function getParentPageUrl($uri)
{
  $uri = preg_replace("/^\//","",$uri);
  //print_r($uri);
  $aSegs =  preg_split('/\//', $uri, -1, PREG_SPLIT_NO_EMPTY);
  //print_r($aSegs);

  $parent_uri = "";
  unset($aSegs[count($aSegs)-1]);
  $i = 0;
  foreach($aSegs as $strSeg)
  {
    if ($i++ < count($aSegs)-1)
    {
      $parent_uri .= $strSeg."-";
    } else {
      $parent_uri .= $strSeg;
    }
  }
  return $parent_uri;
}

function insertTopLevelPages()
{
  global $db, $db_wpdb, $iOrphanId, $iActivityId,$iCountryId,$iContinentId;


  $sql = "
  select
   1 as post_author,
   a.created_date as post_date,
   a.created_date as post_date_gmt,
   '' as post_content,
   '' as post_title,
   '' as post_excerpt,
   'publish' as post_status,
   'closed' as comment_status,
   'closed' as ping_status,
   '' as post_password,
   m.section_uri as post_name,
   '' as to_ping,
   '' as pinged,
   a.published_date,
   a.last_updated as post_modified,
   a.last_updated as post_modified_gmt,
   '' as post_content_filtered,
   1 as post_parent,
   '' as guid,
   0  as menu_order,
   'page' as post_type,
   '' as post_mime_type,
   0 as comment_count,
  (select 1 from activity act where m.section_uri = '/'||act.url_name) as is_activity,
  (select 1 from category cat where m.section_uri = '/'||cat.url_name) as is_category,
  (select 1 from country cty where m.section_uri = '/travel/'||cty.url_name) as is_country,
  (select 1 from continent ctn where m.section_uri = '/continent/'||ctn.url_name) as is_continent
   from article a, article_map m
  where a.id = m.article_id
  and m.section_uri like '/blog'
  and m.website_id = 0
  order by m.section_uri asc, a.id asc
  ";

  print_r("Running query:\n ".$sql."\n");

  $db->query($sql);

  $result = $db->getRows();

  if($db->getNumRows() < 1) {
  	$db->last_error();
    print_r("ERROR: postgres sql query");
  }

  $row = $result[0];

  $data = array(
    'post_author' => $row['post_author'],
    'post_date' => $row['post_date'],
    'post_date_gmt' => $row['post_date_gmt'],
    'post_content' => '',
    'post_title' => 'Orphans',
    'post_excerpt' => '',
    'post_status' => 'draft',
    'comment_status' => $row['comment_status'],
    'ping_status' => $row['ping_status'],
    'post_password' => $row['post_password'],
    'post_name' => 'orphans',
    'to_ping' => $row['to_ping'],
    'pinged' => $row['pinged'],
    'post_modified' => ($row['post_modified'] == "") ? $row['published_date'] : $row['post_modified'],
    'post_modified_gmt' => ($row['post_modified'] == "") ? $row['published_date'] : $row['post_modified'],
    'post_content_filtered' => $row['post_content_filtered'],
    'post_parent' => 0,
    'guid' => $row['guid'],
    'menu_order' => $row['menu_order'],
    'post_type' => $row['post_type'],
    'post_mime_type' => $row['post_mime_type'],
    'comment_count' => $row['comment_count']
  );

  $iOrphanId = getPageId("orphans");
  if (!is_numeric($iOrphanId))
  {
    $iOrphanId = insertPage($data);
  }


  $data = array(
    'post_author' => $row['post_author'],
    'post_date' => $row['post_date'],
    'post_date_gmt' => $row['post_date_gmt'],
    'post_content' => '',
    'post_title' => 'Activity',
    'post_excerpt' => '',
    'post_status' => 'publish',
    'comment_status' => $row['comment_status'],
    'ping_status' => $row['ping_status'],
    'post_password' => $row['post_password'],
    'post_name' => 'activity',
    'to_ping' => $row['to_ping'],
    'pinged' => $row['pinged'],
    'post_modified' => ($row['post_modified'] == "") ? $row['published_date'] : $row['post_modified'],
    'post_modified_gmt' => ($row['post_modified'] == "") ? $row['published_date'] : $row['post_modified'],
    'post_content_filtered' => $row['post_content_filtered'],
    'post_parent' => 0,
    'guid' => $row['guid'],
    'menu_order' => $row['menu_order'],
    'post_type' => $row['post_type'],
    'post_mime_type' => $row['post_mime_type'],
    'comment_count' => $row['comment_count']
  );

  $iActivityId = getPageId("activity");
  if (!is_numeric($iActivityId))
  {
    $iActivityId = insertPage($data);
  }

/*
  $data = array(
    'post_author' => $row['post_author'],
    'post_date' => $row['post_date'],
    'post_date_gmt' => $row['post_date_gmt'],
    'post_content' => '',
    'post_title' => 'Travel',
    'post_excerpt' => '',
    'post_status' => 'draft',
    'comment_status' => $row['comment_status'],
    'ping_status' => $row['ping_status'],
    'post_password' => $row['post_password'],
    'post_name' => 'travel-tour',
    'to_ping' => $row['to_ping'],
    'pinged' => $row['pinged'],
    'post_modified' => ($row['post_modified'] == "") ? $row['published_date'] : $row['post_modified'],
    'post_modified_gmt' => ($row['post_modified'] == "") ? $row['published_date'] : $row['post_modified'],
    'post_content_filtered' => $row['post_content_filtered'],
    'post_parent' => 0,
    'guid' => $row['guid'],
    'menu_order' => $row['menu_order'],
    'post_type' => $row['post_type'],
    'post_mime_type' => $row['post_mime_type'],
    'comment_count' => $row['comment_count']
  );

  $iCountryId = insertPage($data);
*/

  $iCountryId = getPageId("travel-tour");

/*
  $data = array(
    'post_author' => $row['post_author'],
    'post_date' => $row['post_date'],
    'post_date_gmt' => $row['post_date_gmt'],
    'post_content' => '',
    'post_title' => 'Category',
    'post_excerpt' => '',
    'post_status' => 'draft',
    'comment_status' => $row['comment_status'],
    'ping_status' => $row['ping_status'],
    'post_password' => $row['post_password'],
    'post_name' => 'category',
    'to_ping' => $row['to_ping'],
    'pinged' => $row['pinged'],
    'post_modified' => ($row['post_modified'] == "") ? $row['published_date'] : $row['post_modified'],
    'post_modified_gmt' => ($row['post_modified'] == "") ? $row['published_date'] : $row['post_modified'],
    'post_content_filtered' => $row['post_content_filtered'],
    'post_parent' => 0,
    'guid' => $row['guid'],
    'menu_order' => $row['menu_order'],
    'post_type' => $row['post_type'],
    'post_mime_type' => $row['post_mime_type'],
    'comment_count' => $row['comment_count']
  );

  $iCategoryId = getPageId("category");
  if (!is_numeric($iCategoryId))
  {
    $iCategoryId = insertPage($data);
  }
  */

  $data = array(
    'post_author' => $row['post_author'],
    'post_date' => $row['post_date'],
    'post_date_gmt' => $row['post_date_gmt'],
    'post_content' => '',
    'post_title' => 'Courses',
    'post_excerpt' => '',
    'post_status' => 'publish',
    'comment_status' => $row['comment_status'],
    'ping_status' => $row['ping_status'],
    'post_password' => $row['post_password'],
    'post_name' => 'courses',
    'to_ping' => $row['to_ping'],
    'pinged' => $row['pinged'],
    'post_modified' => ($row['post_modified'] == "") ? $row['published_date'] : $row['post_modified'],
    'post_modified_gmt' => ($row['post_modified'] == "") ? $row['published_date'] : $row['post_modified'],
    'post_content_filtered' => $row['post_content_filtered'],
    'post_parent' => 0,
    'guid' => $row['guid'],
    'menu_order' => $row['menu_order'],
    'post_type' => $row['post_type'],
    'post_mime_type' => $row['post_mime_type'],
    'comment_count' => $row['comment_count']
  );

  $iCourseId = getPageId("courses");
  if (!is_numeric($iCourseId))
  {
    $iCourseId = insertPage($data);
  }

  $iContinentId = getPageId("continent");
}

function processLegacyNav()
{
  global $db_wpdb;

  $sql = "update wp_posts set post_name = 'adventure-travel' where post_title = 'Adventure Travel'";
  print_r($sql."\n");
  $db_wpdb->query($sql);

  $sql = "update wp_posts set post_name = 'adventure-travel' where post_title = 'Adventure Travel'";
  print_r($sql."\n");
  $db_wpdb->query($sql);


  require_once("../classes/template.class.php");
  require_once("../classes/navigation.class.php");

  $file = "./nav.xml";
  $xml = simplexml_load_file($file) or die ("Unable to load Navigation XML file!");

  $oNav = new Nav();

  foreach($xml->xpath('//section') as $section) {

                  $oSection = new NavSection();
                  $oSection->SetTitle((string)$section->title);
                  $oSection->SetDesc((string)$section->desc);
                  $oSection->SetLink((string)$section->link);

                  print_r($oSection->GetTitle() ." : ". $oSection->GetLink()."\n");

                  $aUrlSegs = getUrlSegments($oSection->GetLink());
                  print_r($aUrlSegs);
                  $strPostName = getPageUrl($aUrlSegs[count($aUrlSegs) -1]);

                  if (in_array($strPostName, array("blog")))
                  {
                    continue;
                  }
                  if ($oSection->GetTitle() == "VOLUNTEER")
                  {
                    $strPostName = "volunteer";
                  }
                  $id = getPageId($strPostName);
                  print_r("WordPress Page exists?: ");
                  print_r($id);
                  print_r("\n");

                  if (is_numeric($id))
                  {
                      $sql = "update wp_posts set post_parent = 0 where id = ".$id;
                      print_r($sql."\n");
                      $db_wpdb->query($sql);
                      //print_r("Affected rows: ".$db_wpdb->rows_affected."\n");
                      if ($db_wpdb->rows_affected != 1)
                      {
                        //print_r($db_wpdb);
                        //die();
                      }
                  }

                  foreach($section->subsections as $subsections) {
                          foreach($subsections as $subsection) {
                                  $oSubSection = new NavSubSection();
                                  $oSubSection->SetTitle((string)$subsection->title);
                                  $oSubSection->SetLink((string)$subsection->link);
                                  $oSubSection->SetClass((string)$subsection->class);

                                  print_r("\t".$oSubSection->GetTitle() ." : ". $oSubSection->GetLink()."\n");

                                  $aUrlSegs = getUrlSegments($oSubSection->GetLink());
                                  print_r($aUrlSegs);
                                  $strLev2PostName = getPageUrl($aUrlSegs[count($aUrlSegs) -1]);

                                  if ($strLev2PostName == $strPostName)
                                  {
                                    continue;
                                  }

                                  $idLev2 = getPageId($strLev2PostName);
                                  print_r("WordPress Page exists?: ");
                                  print_r($idLev2);
                                  print_r("\n");

                                  if (is_numeric($idLev2))
                                  {
                                    $sql = "update wp_posts set post_parent = ".$id." where id = ".$idLev2;
                                    print_r($sql."\n");
                                    $db_wpdb->query($sql);
                                    print_r("Affected rows: ".$db_wpdb->rows_affected."\n");
                                  }

                                  // only support for 2 level nav, could be made into recursive func in future
                                  foreach($subsection->subsections as $section_subsections) {
                                          foreach($section_subsections as $section_subsection) {
                                                  $oLevel2SubSection = new NavSubSection();
                                                  $oLevel2SubSection->SetTitle((string)$section_subsection->title);
                                                  $oLevel2SubSection->SetLink((string)$section_subsection->link);
                                                  $oLevel2SubSection->SetClass((string)$section_subsection->class);
                                                  $oSubSection->SetSubSection($oLevel2SubSection);

                                                  print_r("\t\t".$oLevel2SubSection->GetTitle() ." : ". $oLevel2SubSection->GetLink()."\n");

                                          }
                                  }
                                  $oSection->SetSubSection($oSubSection);
                          }
                  }

                  $oNav->SetSection($oSection);
  }
}


function viewLegacyNav()
{
  global $db_wpdb;

  require_once("../classes/template.class.php");
  require_once("../classes/navigation.class.php");

  $file = "./nav.xml";
  $xml = simplexml_load_file($file) or die ("Unable to load Navigation XML file!");

  $oNav = new Nav();

  foreach($xml->xpath('//section') as $section) {

                  $oSection = new NavSection();
                  $oSection->SetTitle((string)$section->title);
                  $oSection->SetDesc((string)$section->desc);
                  $oSection->SetLink((string)$section->link);

                  print_r($oSection->GetTitle() ." : ". $oSection->GetLink()."\n");


                  foreach($section->subsections as $subsections) {
                          foreach($subsections as $subsection) {
                                  $oSubSection = new NavSubSection();
                                  $oSubSection->SetTitle((string)$subsection->title);
                                  $oSubSection->SetLink((string)$subsection->link);
                                  $oSubSection->SetClass((string)$subsection->class);

                                  print_r("\t".$oSubSection->GetTitle() ." : ". $oSubSection->GetLink()."\n");
                                  // only support for 2 level nav, could be made into recursive func in future
                                  foreach($subsection->subsections as $section_subsections) {
                                          foreach($section_subsections as $section_subsection) {
                                                  $oLevel2SubSection = new NavSubSection();
                                                  $oLevel2SubSection->SetTitle((string)$section_subsection->title);
                                                  $oLevel2SubSection->SetLink((string)$section_subsection->link);
                                                  $oLevel2SubSection->SetClass((string)$section_subsection->class);
                                                  $oSubSection->SetSubSection($oLevel2SubSection);

                                                  print_r("\t\t".$oLevel2SubSection->GetTitle() ." : ". $oLevel2SubSection->GetLink()."\n");

                                          }
                                  }
                                  $oSection->SetSubSection($oSubSection);
                          }
                  }

                  $oNav->SetSection($oSection);
  }
}


/*

$uri = "/animal-volunteer-projects/game-reserve";
print_r(getPageUrl($uri)."\n");
print_r(getParentPageUrl($uri)."\n");

die();


select
 REPLACE(m.section_uri, '/blog/','') as post_name,
 a.title as post_title,
 m.website_id
 from article a, article_map m
where a.id = m.article_id
and m.section_uri like '/country%'
order by m.section_uri asc, a.id asc


select
id,
post_title,
post_name,
post_date,
post_modified,
post_parent,
menu_order,
SUBSTRING(post_excerpt,1,64),
post_status,
post_type,
post_mime_type
from wp_posts;


mysql> describe wp_posts;
+-----------------------+---------------------+------+-----+---------------------+----------------+
| Field                 | Type                | Null | Key | Default             | Extra          |
+-----------------------+---------------------+------+-----+---------------------+----------------+
| ID                    | bigint(20) unsigned | NO   | PRI | NULL                | auto_increment |
| post_author           | bigint(20) unsigned | NO   | MUL | 0                   |                |
| post_date             | datetime            | NO   |     | 0000-00-00 00:00:00 |                |
| post_date_gmt         | datetime            | NO   |     | 0000-00-00 00:00:00 |                |
| post_content          | longtext            | NO   |     | NULL                |                |
| post_title            | text                | NO   |     | NULL                |                |
| post_excerpt          | text                | NO   |     | NULL                |                |
| post_status           | varchar(20)         | NO   |     | publish             |                |
| comment_status        | varchar(20)         | NO   |     | open                |                |
| ping_status           | varchar(20)         | NO   |     | open                |                |
| post_password         | varchar(255)        | NO   |     |                     |                |
| post_name             | varchar(200)        | NO   | MUL |                     |                |
| to_ping               | text                | NO   |     | NULL                |                |
| pinged                | text                | NO   |     | NULL                |                |
| post_modified         | datetime            | NO   |     | 0000-00-00 00:00:00 |                |
| post_modified_gmt     | datetime            | NO   |     | 0000-00-00 00:00:00 |                |
| post_content_filtered | longtext            | NO   |     | NULL                |                |
| post_parent           | bigint(20) unsigned | NO   | MUL | 0                   |                |
| guid                  | varchar(255)        | NO   |     |                     |                |
| menu_order            | int(11)             | NO   |     | 0                   |                |
| post_type             | varchar(20)         | NO   | MUL | post                |                |
| post_mime_type        | varchar(100)        | NO   |     |                     |                |
| comment_count         | bigint(20)          | NO   |     | 0                   |                |
+-----------------------+---------------------+------+-----+---------------------+----------------+

*/

?>
