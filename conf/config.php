<?php


ini_set('display_errors',0);
ini_set('log_errors', 1);
ini_set('error_log', '/www/vhosts/365admin.org/logs/365admin_error.log');
error_reporting(E_ALL & ~E_NOTICE & ~ E_STRICT);

date_default_timezone_set('Europe/London');

// db connection
$dsn = array("dbhost" => "localhost","dbuser" => "", "dbpass" => "","dbname" => "","dbport" => "5432");


$solr_config = array(
    'adapteroptions' => array(
        'host' => '127.0.0.1',
        'port' => 8983,
        'path' => '/solr/collection1/'
        
    )
);


define('DEBUG',false);
define('DEV',FALSE);

define('TEST_MODE', false);
define('TEST_EMAIL','');

define('BLOCK_ADS', true);

/* 0 = none, 1 = error / messages, 2 = debug, 3 = verbose debug */
define('LOG_PATH',"/www/vhosts/365admin.org/logs/365admin_app.log");
define('LOG_LEVEL',1);

define('HOSTNAME',"oneworld365.org");
define('BASE_URL','http://admin.'.HOSTNAME);
define('WEBSITE',HOSTNAME);
define('ADMIN_SYSTEM_HOSTNAME', 'admin.oneworld365.org');
define('ADMIN_SYSTEM',"http://".ADMIN_SYSTEM_HOSTNAME);
define('API_URL','http://api.'.HOSTNAME);
define('CURRENT_SITE', ADMIN_SYSTEM);

define('BASE_PATH','/www/vhosts/365admin.org/htdocs');
define('ROOT_PATH',BASE_PATH); // required for some classes
define('ROOT_PATH_IMAGE_UPLOAD','/www/vhosts/oneworld365.org/htdocs'); // image upload (ProfileController)
define('PATH_CLASSES',BASE_PATH. '/classes/');
define('PATH_CONTROLLERS',BASE_PATH. '/controllers/');
define("PATH_TO_MVC_ROUTE_MAP", BASE_PATH."/conf/routes.xml"); // MVC routes (URL -> Class / Method)
define("PATH_TO_STATIC_ROUTE_MAP", BASE_PATH."/conf/routes_static.xml"); // Static routes (URL -> PHP Script file)
define('PATH_NAV_CONFIG','/www/vhosts/oneworld365.org/htdocs/conf/nav.xml');

define('PATH_2_DATA_DIR',BASE_PATH. '/data/');
define('PATH_UNDER_MAINTENANCE','/back_soon.php');

define('SITE_TITLE','365 Admin');
define("COOKIE_DOMAIN", ".".HOSTNAME);
define('CACHE_ENABLED', false);

/* profile types - from db table profile_types */
define("PROFILE_COMPANY",0);
define("PROFILE_PLACEMENT",1);
define("PROFILE_VOLUNTEER",2);   // placement
define("PROFILE_TOUR",3); // placement
define("PROFILE_JOB",4); // placement
define("PROFILE_SUMMERCAMP",5); // company profile
define("PROFILE_VOLUNTEER_PROJECT",6); // company profile
define("PROFILE_SEASONALJOBS",7); // company profile
define("PROFILE_TEACHING",8); // company profile
define("PROFILE_COURSES",9); // company profile


// general content type id - used to fetch related content and by  SOLR for indexing
define("CONTENT_COMPANY", 0);
define("CONTENT_PLACEMENT", 1);
define("CONTENT_ARTICLE", 2);

// specific page content types
define("CONTENT_TYPE_COMPANY", "COMPANY");
define("CONTENT_TYPE_PLACEMENT", "PLACEMENT");
define("CONTENT_TYPE_ARTICLE", "ARTICLE");
define("CONTENT_TYPE_CATEGORY", "CATEGORY");
define("CONTENT_TYPE_ACTVITY", "ACTIVITY");
define("CONTENT_TYPE_COUNTRY", "COUNTRY");
define("CONTENT_TYPE_CONTINENT", "CONTINENT");
define("CONTENT_TYPE_RESULTS", "RESULTS");
define("CONTENT_TYPE_DESTINATION", "DESTINATION");


/* Fetch full or summary details only */
define("FETCHMODE__FULL",0);
define("FETCHMODE__ SUMMARY",1);


/* listing types */
define("NEW_LISTING",-1);
define("FREE_LISTING",0);
define("BASIC_LISTING",1);
define("ENHANCED_LISTING",2);
define("SPONSORED_LISTING",3);

/* default placement quotas */
define("FREE_PQUOTA",0);
define("BASIC_PQUOTA",1);
define("ENHANCED_PQUOTA",10);
define("SPONSORED_PQUOTA",25);


/* default 8bit profile / enquiry option bitmaps for all new company listing requests */
define("DEFAULT_PROFILE_OPT",'11100000');
define("DEFAULT_ENQUIRY_OPT",'10000000');


define('LISTING_REQUEST_NEW','NEW');
define('LISTING_REQUEST_UPDATE','EDIT');




/* error messages */
define('ERROR_INVALID_SESSION','No valid session or session expired');
define('ERROR_INVALID_XML_FILE_PATH','No XML route config file found at supplied path: ');
define('ERROR_INVALID_XML_ROUTE_DEFS','XML route definitions missing or in an invalid format');
define('ERROR_404_REQUEST_URI_NOT_FOUND','No route defination found for request uri: ');
define('ERROR_404_ROUTE_NOT_FOUND','Route not found : URL: ');
define('ERROR_404_INVALID_REQUEST','Invalid request uri: ');
define('ERROR_INVALID_PROFILE_TYPE','Invalid profile type for: ');
define('ERROR_COMPANY_PROFILE_NOT_FOUND','Company profile not found id: ');
define('ERROR_PLACEMENT_PROFILE_NOT_FOUND','Placement profile not found id: ');
define('ERROR_INVALID_MODE','Profile mode (add/edit/view) not set');
define('ERROR_COMPANY_PROFILE_INVALID_URL','Company profile url was missing or not valid: ');
define('ERROR_COMPANY_PROFILE_PERMISSIONS_FAIL','Insufficient access writes for operation: ');
define('ERROR_COMPANY_PROFILE_EXTENDED_ERROR','An error occured updating profile - contact us for assistance');
define('ERROR_ADD_ACCOUNT_FAILED','Add account failed :');


/* config params required to make classes work */
$_CONFIG = array( 
        'site_id' => 0,
        'url' => 'http://admin.'.HOSTNAME,
        'root_path' => ROOT_PATH,
        'template_home' => '/templates',
        'company_table' => 'company',
        'placement_table' => 'profile_hdr',
        'profile_hdr_table' => 'profile_hdr', /* placement table is a view in some sites, these must use profile_hdr for add/update */
        'index_table' => 'keyword_idx_2',
        'tagcloud_table' => 'keyword_idx_1',
        'comp_country_map' => 'comp_country_map',
        'image_map' => 'image_map',
        'image' => 'image',
    
        'site_title' => 'One World 365',
        'logo_url' => '/images/oneworld365_logo_small.png',
        'admin_email' => 'admin@oneworld365.org',
        'website_email' => 'website@oneworld365.org',
        'bcc_list' => 'admin@oneworld365.org',    
        'email_template_hdr' => '/email_html_header.php',
        'email_template_footer' => '/email_html_footer.php',    
        'company_home' => 'company',

        'aProfileVersion' => array(     0 => "oneworld365.org" ),

        'profile_category_defaults' => array(
							PROFILE_SUMMERCAMP => array(3),
							PROFILE_VOLUNTEER_PROJECT => array(2)
									),

        'profile_activity_defaults' => array(
							PROFILE_SUMMERCAMP => array(27,21)	
									),

        'profile_country_defaults' => array(
							PROFILE_SUMMERCAMP => array(71)	
									)
									
);


/* image/ file upload params */
define("IMAGE_MAX_UPLOAD_SIZE",6291456);

define("IMG_PATH_IDENTIFY","/usr/bin/identify");
define("IMG_PATH_CONVERT","/usr/bin/convert");

define("LANDSCAPE","L");
define("PORTRAIT","P");
define("SQUARE","S");

define("PROFILE_IMAGE",0);
define("LOGO_IMAGE",1);
define("PROMO_IMAGE",2);

define("LOGO__DIMENSIONS_MAXWIDTH", 2000);
define("LOGO__DIMENSIONS_MINWIDTH", 200);
define("LOGO__DIMENSIONS_MAXHEIGHT", 1500);
define("LOGO__DIMENSIONS_MINHEIGHT", 100);

define("PROMO__DIMENSIONS_MAXWIDTH", 2000);
define("PROMO__DIMENSIONS_MINWIDTH", 250);
define("PROMO__DIMENSIONS_MAXHEIGHT", 1500);
define("PROMO__DIMENSIONS_MINHEIGHT", 200);

define("LOGO__DIMENSIONS_SMALL_WIDTH", 120); /* width of auto generated logo small version */
define("LOGO__DIMENSIONS_SMALL_HEIGHT", 60); /* width of auto generated logo small version */

define("PROMO__DIMENSIONS_WIDTH", 250); /* width of auto generated logo small version */
define("PROMO__DIMENSIONS_HEIGHT", 250); /* width of auto generated logo small version */

define("IMG_HOST","http://www.oneworld365.org/");
define("IMG_BASE_URL",IMG_HOST ."img/");
define("IMG_BASE_PATH","/www/vhosts/oneworld365.org/htdocs/img/");
define("IMG_SEQ","image_seq");

/* cookie params */
define("COOKIE_NAME", "oneworld365");
define("COOKIE_TAB_NAME", "365admin_tab");
define("COOKIE_PATH", "/");
define("COOKIE_EXPIRES", 1056000);

/* no of permitted login attempts before account is locked */ 
define("MAX_LOGIN_ATTEMPTS",20);

/* Password Encryption md5 Hash Salt Length - do not change */
define('SALT_LENGTH', 9);

?>
