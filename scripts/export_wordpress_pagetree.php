<?

define( 'ABSPATH', '/www/vhosts/365wordpress/wordpress/' );
define( 'WPINC', 'wp-includes' );

define( 'WP_DEBUG', true );
define( 'WP_DEBUG_DISPLAY', true );
define( 'WP_DEBUG_LOG', true );

define( 'SAVEQUERIES', false );

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


//require_once("/www/vhosts/365wordpress/wordpress/wp-admin/includes/noop.php");
require_once("/www/vhosts/365wordpress/wordpress/wp-includes/load.php");
require_once("/www/vhosts/365wordpress/wordpress/wp-includes/option.php");
require_once("/www/vhosts/365wordpress/wordpress/wp-includes/functions.php");
require_once("/www/vhosts/365wordpress/wordpress/wp-includes/plugin.php");
require_once("/www/vhosts/365wordpress/wordpress/wp-includes/class-wp-error.php");
require_once("/www/vhosts/365wordpress/wordpress/wp-includes/class-wpdb.php");

$db_wpdb = new wpdb(DB_USER, DB_PASSWORD, DB_NAME, DB_HOST);

//print_r(var_dump($db_wpdb));

$iRecursionLimit = 25;
$iRecursionIteration = 0;
$sLastParentName = null;


$aRow = getPages(0);#
getPageTree($aRow);

function getPageTree($aRow)
{
  global $sLastParentName, $iRecursionLimit, $iRecursionIteration;

  foreach($aRow as $oRow)
  {

    print_r($oRow->post_name." (".$sLastParentName.")\n");

    //print_r("RowId: ".$oRow->id."\n");
    //print_r("ParentId: ".$oRow->post_parent."\n");

    $aNextRow = getPages($oRow->id);

    if (is_array($aNextRow) && count($aNextRow) >= 1)
    {
      //print_r("NextRow: \n");
      //print_r($aNextRow);
      //print_r("\n");

      getPageTree($aNextRow);

    }

  }
}

function getPages($post_parent)
{
  global $db_wpdb;

  $sql = 'select id,post_name, post_parent from wp_posts where post_parent = '.$post_parent.' order by post_name asc';

  return $aRow = $db_wpdb->get_results( $sql, $output = OBJECT );

}

?>
