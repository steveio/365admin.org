<?php

require_once("../conf/config.php");
require_once(BASE_PATH."/classes/db_pgsql.class.php");
require_once(BASE_PATH."/classes/logger.php");

$dsn = array("dbhost" => "localhost","dbuser" => "oneworld365_pgsql", "dbpass" => "bra@zi1","dbname" => "oneworld365","dbport" => "5432");

define( ABSPATH, '/www/vhosts/365wordpress/wordpress/' );
define( WPINC, 'wp-includes' );

define( 'WP_DEBUG', true );
define( 'WP_DEBUG_DISPLAY', true );
define( 'WP_DEBUG_LOG', true );

define( 'SAVEQUERIES', true );

define( 'DB_NAME', '365wordpress' );
/** Database username */
define( 'DB_USER', '365wordpress' );
/** Database password */
define( 'DB_PASSWORD', 'Buster123!' );
/** Database hostname */
define( 'DB_HOST', 'localhost' );
/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8' );
/** The database collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );


$db = new db($dsn,$debug = false);
print_r(var_dump($db->db));


//require_once("/www/vhosts/365wordpress/wordpress/wp-admin/includes/noop.php");
require_once("/www/vhosts/365wordpress/wordpress/wp-includes/load.php");
require_once("/www/vhosts/365wordpress/wordpress/wp-includes/option.php");
require_once("/www/vhosts/365wordpress/wordpress/wp-includes/functions.php");
require_once("/www/vhosts/365wordpress/wordpress/wp-includes/plugin.php");
require_once("/www/vhosts/365wordpress/wordpress/wp-includes/class-wp-error.php");
require_once("/www/vhosts/365wordpress/wordpress/wp-includes/class-wpdb.php");

$db_wpdb = new wpdb(DB_USER, DB_PASSWORD, DB_NAME, DB_HOST);

print_r(var_dump($db_wpdb));

$sql = "
select
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
 m.section_uri as post_name,
 '' as to_ping,
 '' as pinged,
 a.last_updated as post_modified,
 a.last_updated as post_modified_gmt,
 '' as post_content_filtered,
 1 as post_parent,
 '' as guid,
 0  as menu_order,
 'page' as post_type,
 '' as post_mime_type,
 0 as comment_count
 from article a, article_map m
where a.id = m.article_id
and m.section_uri like '/blog%'
order by a.id asc
";


print_r("Running query:\n ".$sql."\n");

$db->query($sql);


$result = $db->getRows();

if($db->getNumRows() < 1) {
	$db->last_error();
}

$iter = 0;
$loop = 5;

foreach($result as $row) {


        print_r("Insert row: ".$iter."\n");

        $data = array(
          'post_author' => $row['post_author'],
          'post_date' => $row['post_date'],
          'post_date_gmt' => $row['post_date_gmt'],
          'post_content' => $row['post_content'],
          'post_title' => $row['post_title'],
          'post_excerpt' => $row['post_excerpt'],
          'post_status' => $row['post_status'],
          'comment_status' => $row['comment_status'],
          'ping_status' => $row['ping_status'],
          'post_password' => $row['post_password'],
          'post_name' => $row['post_name'],
          'to_ping' => $row['to_ping'],
          'pinged' => $row['pinged'],
          'post_modified' => $row['post_modified'],
          'post_modified_gmt' => $row['post_modified_gmt'],
          'post_content_filtered' => $row['post_content_filtered'],
          'post_parent' => $row['post_parent'],
          'guid' => $row['guid'],
          'menu_order' => $row['menu_order'],
          'post_type' => $row['post_type'],
          'post_mime_type' => $row['post_mime_type'],
          'comment_count' => $row['comment_count']
        );

/*

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

        // A format is one of '%d', '%f', '%s' (integer, float, string).
        $formats = array("%d","%s","%s","%s","%s","%s","%s","%s","%s","%s","%s","%s","%s","%s","%s","%s","%d","%s","%d","%s","%s","%d");

        try {
          $result = $db_wpdb->insert( 'wp_posts', $data , $formats );

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

        unset($row['post_excerpt']);
        unset($row['post_content']);
        print_r($row);

        if (++$iter == $loop)
        {
          print_r("Insert loop complete");
          exit();
        }
}



?>
