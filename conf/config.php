<?php


ini_set('display_errors',1);
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
define('TEST_EMAIL','steveedwards01@yahoo.co.uk');

define('BLOCK_ADS', true);

/* 0 = none, 1 = error, 2 = debug, 3 = verbose debug */
define('LOG_PATH',"/www/vhosts/365admin.org/logs/365admin_app.log");
define('LOG_LEVEL',3);

define('HOSTNAME',"oneworld365.org");
define('BASE_URL','https://admin.'.HOSTNAME);
define('WEBSITE',HOSTNAME);
define('ADMIN_SYSTEM_HOSTNAME', 'admin.oneworld365.org');
define('ADMIN_SYSTEM',"https://".ADMIN_SYSTEM_HOSTNAME);
define('API_URL','https://api.'.HOSTNAME);
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
define("FETCHMODE__SUMMARY",1);


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


/* request route mappings */
define('ROUTE_NEW','new');
define('ROUTE_UPDATE','update');
define('ROUTE_EDIT','edit');
define('ROUTE_DELETE','delete');
define('ROUTE_DASHBOARD','dashboard');
define('ROUTE_ERROR','error');
define('ROUTE_SEARCH','search');
define('ROUTE_LOGIN','login');
define('ROUTE_PASSWD','password');
define('ROUTE_REGISTRATION','registration');
define('ROUTE_COMPANY','company');
define('ROUTE_PLACEMENT','placement');
define('ROUTE_CONFIRMATION','confirmation');
define('ROUTE_CONTACT','contact');
define('ROUTE_MAILSHOT','mailshot');

define('LISTING_REQUEST_NEW','NEW');
define('LISTING_REQUEST_UPDATE','EDIT');


/* refdata type mappings 
 * @todo - add a dynamic loader to read these from DB 
 */
define('REFDATA_US_STATE',0);
define('REFDATA_CAMP_TYPE',1);
define('REFDATA_CAMP_JOB_TYPE',2);
define('REFDATA_ACTIVITY',3);
define('REFDATA_INT_RANGE',4);
define('REFDATA_DURATION',5);
define('REFDATA_ORG_SUBTYPE',6);
define('REFDATA_BONDING',7);
define('REFDATA_STAFF_ORIGIN',8);
define('REFDATA_GENDER',9);
define('REFDATA_APPROX_COST',10);
define('REFDATA_HABITATS',11);
define('REFDATA_SPECIES',12);
define('REFDATA_ACCOMODATION',13);
define('REFDATA_MEALS',14);
define('REFDATA_TRAVEL_TRANSPORT',15);
define('REFDATA_ADVENTURE_SPORTS',16);
define('REFDATA_ORG_PROJECT_TYPE',17);
define('REFDATA_CURRENCY',18);
define('REFDATA_JOB_OPTIONS',19);
define('REFDATA_INT_SMALL_RANGE',20);
define('REFDATA_JOB_CONTRACT_TYPE',21);
define('REFDATA_US_REGION',22);
define('REFDATA_AGE_RANGE',23);
define('REFDATA_RELIGION',24);
define('REFDATA_CAMP_GENDER',25);

/* multiple choice refdata form element prefixes */
define('REFDATA_ACTIVITY_PREFIX','CA_');
define('REFDATA_CAMP_TYPE_PREFIX','CT_');
define('REFDATA_CAMP_JOB_TYPE_PREFIX','JT_');
define('REFDATA_SPECIES_PREFIX','SP_');
define('REFDATA_HABITATS_PREFIX','HA_');
define('REFDATA_TRAVEL_TRANSPORT_PREFIX','TT_');
define('REFDATA_ACCOMODATION_PREFIX','AC_');
define('REFDATA_MEALS_PREFIX','ML_');
define('REFDATA_JOB_OPTIONS_PREFIX','JO_');


/* Company profile field id's */
define('PROFILE_FIELD_COMP_PROFILE_TYPE_ID','profile_type');
define('PROFILE_FIELD_COMP_TITLE','title');
define('PROFILE_FIELD_COMP_DESC_SHORT','desc_short');
define('PROFILE_FIELD_COMP_DESC_LONG','desc_long');
define('PROFILE_FIELD_COMP_URL','url');
define('PROFILE_FIELD_COMP_EMAIL','email');
define('PROFILE_FIELD_COMP_APPLY_URL','apply_url');
define('PROFILE_FIELD_COMP_ADDRESS','address');
define('PROFILE_FIELD_COMP_COUNTRY_ID','country_id');
define('PROFILE_FIELD_COMP_STATE_ID','state_id');
define('PROFILE_FIELD_COMP_LOCATION','location');
define('PROFILE_FIELD_COMP_TELEPHONE','tel');

/* Company profile - admin options */
define('PROFILE_FIELD_COMP_PROD_TYPE','prod_type');
define('PROFILE_FIELD_COMP_LISTING_TYPE','listing_type');
define('PROFILE_FIELD_COMP_LISTING_START_DATE','listing_start_date');
define('PROFILE_FIELD_COMP_PROFILE_QUOTA','profile_quota');
define('PROFILE_FIELD_COMP_PROFILE_OPTIONS','prof_opt');
define('PROFILE_FIELD_COMP_ENQUIRY_OPTIONS','enq_opt');


/* Extended profile - general company profile */
define('PROFILE_FIELD_COMP_GENERAL_PLACEMENT_INFO','job_info');
define('PROFILE_FIELD_COMP_GENERAL_DURATION','duration');
define('PROFILE_FIELD_COMP_GENERAL_COSTS','costs');


/* Extended profile - summer camp field id's */
define('PROFILE_FIELD_SUMMERCAMP_DURATION_FROM','sc_duration_from_id');
define('PROFILE_FIELD_SUMMERCAMP_DURATION_TO','sc_duration_to_id');
define('PROFILE_FIELD_SUMMERCAMP_DURATION_LABEL','sc_duration_label');
define('PROFILE_FIELD_SUMMERCAMP_NO_STAFF','sc_no_staff');
define('PROFILE_FIELD_SUMMERCAMP_STAFF_GENDER','sc_staff_gender');
define('PROFILE_FIELD_SUMMERCAMP_STAFF_ORIGIN','sc_staff_origin');
define('PROFILE_FIELD_SUMMERCAMP_SEASON_DATES','sc_season_dates');
define('PROFILE_FIELD_SUMMERCAMP_REQUIREMENTS','sc_requirements');
define('PROFILE_FIELD_SUMMERCAMP_HOW_TO_APPLY','sc_how_to_apply');
define('PROFILE_FIELD_SUMMERCAMP_CAMP_TYPE','sc_camp_type');
define('PROFILE_FIELD_SUMMERCAMP_CAMP_JOB_TYPE','sc_camp_job_type');
define('PROFILE_FIELD_SUMMERCAMP_CAMP_ACTIVITY','sc_camp_activity');
define('PROFILE_FIELD_SUMMERCAMP_CAMP_GENDER','sc_camp_gender');
define('PROFILE_FIELD_SUMMERCAMP_CAMP_RELIGION','sc_camp_religion');
define('PROFILE_FIELD_SUMMERCAMP_CAMPER_AGE_FROM','sc_camper_age_from');
define('PROFILE_FIELD_SUMMERCAMP_CAMPER_AGE_TO','sc_camper_age_to');
define('PROFILE_FIELD_SUMMERCAMP_CAMPER_AGE_LABEL','sc_camper_label');
define('PROFILE_FIELD_SUMMERCAMP_PRICE_FROM','sc_price_from_id');
define('PROFILE_FIELD_SUMMERCAMP_PRICE_TO','sc_price_to_id');
define('PROFILE_FIELD_SUMMERCAMP_PRICE_LABEL','sc_price_label');
define('PROFILE_FIELD_SUMMERCAMP_CURRENCY','sc_currency_id');



/* Extended profile - seasonal jobs field id's */
define('PROFILE_FIELD_SEASONALJOBS_DURATION_FROM','sj_duration_from_id');
define('PROFILE_FIELD_SEASONALJOBS_DURATION_TO','sj_duration_to_id');
define('PROFILE_FIELD_SEASONALJOBS_DURATION_LABEL','sj_duration_label');
define('PROFILE_FIELD_SEASONALJOBS_JOB_TYPES','sj_job_types');
define('PROFILE_FIELD_SEASONALJOBS_PAY','sj_pay');
define('PROFILE_FIELD_SEASONALJOBS_BENEFITS','sj_benefits');
define('PROFILE_FIELD_SEASONALJOBS_NO_STAFF','sj_no_staff');
define('PROFILE_FIELD_SEASONALJOBS_HOW_TO_APPLY','sj_how_to_apply');
define('PROFILE_FIELD_SEASONALJOBS_REQUIREMENTS','sj_requirements');


/* Extended profile - volunteer travel project */
define('PROFILE_FIELD_VOLUNTEER_DURATION_FROM','vp_duration_from_id');
define('PROFILE_FIELD_VOLUNTEER_DURATION_TO','vp_duration_to_id');
define('PROFILE_FIELD_VOLUNTEER_DURATION_LABEL','vp_duration_label');
define('PROFILE_FIELD_VOLUNTEER_PRICE_FROM','vp_price_from_id');
define('PROFILE_FIELD_VOLUNTEER_PRICE_TO','vp_price_to_id');
define('PROFILE_FIELD_VOLUNTEER_PRICE_LABEL','vp_price_label');
define('PROFILE_FIELD_VOLUNTEER_CURRENCY','vp_currency_id');
define('PROFILE_FIELD_VOLUNTEER_FOUNDED','vp_founded');
define('PROFILE_FIELD_VOLUNTEER_NO_PLACEMENTS','vp_no_placements');
define('PROFILE_FIELD_VOLUNTEER_ORG_TYPE','vp_org_type');
define('PROFILE_FIELD_VOLUNTEER_AWARDS','vp_awards');
define('PROFILE_FIELD_VOLUNTEER_FUNDING','vp_funding');
define('PROFILE_FIELD_VOLUNTEER_SUPPORT','vp_support');
define('PROFILE_FIELD_VOLUNTEER_SAFETY','vp_safety');
define('PROFILE_FIELD_VOLUNTEER_SPECIES','vp_species');
define('PROFILE_FIELD_VOLUNTEER_HABITATS','vp_habitats');


/* Extended profile - teaching jobs / courses */
define('PROFILE_FIELD_TEACHING_DURATION_FROM','tp_duration_from_id');
define('PROFILE_FIELD_TEACHING_DURATION_TO','tp_duration_to_id');
define('PROFILE_FIELD_TEACHING_DURATION_LABEL','tp_duration_label');
define('PROFILE_FIELD_TEACHING_NO_TEACHERS','tp_no_teachers');
define('PROFILE_FIELD_TEACHING_CLASS_SIZE','tp_class_size');
define('PROFILE_FIELD_TEACHING_DURATION','tp_duration');
define('PROFILE_FIELD_TEACHING_SALARY','tp_salary');
define('PROFILE_FIELD_TEACHING_BENEFITS','tp_benefits');
define('PROFILE_FIELD_TEACHING_QUALIFICATIONS','tp_qualifications');
define('PROFILE_FIELD_TEACHING_REQUIREMENTS','tp_requirements');
define('PROFILE_FIELD_TEACHING_HOW_TO_APPLY','tp_how_to_apply');


/* Common Placement form field id's */
define('PROFILE_FIELD_PLACEMENT_TITLE','title');
define('PROFILE_FIELD_PLACEMENT_COMP_ID','company_id');
define('PROFILE_FIELD_PLACEMENT_DESC_SHORT','desc_short');
define('PROFILE_FIELD_PLACEMENT_PROFILE_TYPE_ID','profile_type');
define('PROFILE_FIELD_PLACEMENT_LOCATION','location');
define('PROFILE_FIELD_PLACEMENT_DESC_LONG','desc_long');
define('PROFILE_FIELD_PLACEMENT_URL','url');
define('PROFILE_FIELD_PLACEMENT_EMAIL','email');
define('PROFILE_FIELD_PLACEMENT_APPLY_URL','apply_url');
define('PROFILE_FIELD_PLACEMENT_KEYWORD_EXCLUDE','keyword_exclude');
define('PROFILE_FIELD_PLACEMENT_ACTIVE','ad_active');


// general placement fields
define('PROFILE_FIELD_PLACEMENT_DURATION_FROM','duration_from_id');
define('PROFILE_FIELD_PLACEMENT_DURATION_TO','duration_to_id');
define('PROFILE_FIELD_PLACEMENT_DURATION_LABEL','duration_label'); // validation key only
define('PROFILE_FIELD_PLACEMENT_START_DATES_TXT','start_dates');
define('PROFILE_FIELD_PLACEMENT_BENEFITS','benefits');
define('PROFILE_FIELD_PLACEMENT_REQUIREMENTS','requirements');
define('PROFILE_FIELD_PLACEMENT_PRICE_LABEL','price_label'); // validation key only
define('PROFILE_FIELD_PLACEMENT_PRICE_FROM','price_from_id');
define('PROFILE_FIELD_PLACEMENT_PRICE_TO','price_to_id');
define('PROFILE_FIELD_PLACEMENT_CURRENCY','currency_id');


// placement tour profile
define('PROFILE_FIELD_PLACEMENT_TOUR_CODE','code');
define('PROFILE_FIELD_PLACEMENT_ITINERY','itinery');
define('PROFILE_FIELD_PLACEMENT_TOUR_PRICE','tour_price');
define('PROFILE_FIELD_PLACEMENT_START_DATES','dates');
define('PROFILE_FIELD_PLACEMENT_TOUR_REQUIREMENTS','tour_requirements');
define('PROFILE_FIELD_PLACEMENT_TOUR_TRAVEL','tour_travel_transport');
define('PROFILE_FIELD_PLACEMENT_TOUR_MEALS','tour_meals');
define('PROFILE_FIELD_PLACEMENT_TOUR_ACCOM','tour_accom');
define('PROFILE_FIELD_PLACEMENT_TOUR_DURATION_FROM','tour_duration_from_id');
define('PROFILE_FIELD_PLACEMENT_TOUR_DURATION_TO','tour_duration_to_id');
define('PROFILE_FIELD_PLACEMENT_TOUR_DURATION_LABEL','tour_duration_label');
define('PROFILE_FIELD_PLACEMENT_TOUR_PRICE_FROM','tour_price_from_id');
define('PROFILE_FIELD_PLACEMENT_TOUR_PRICE_TO','tour_price_to_id');
define('PROFILE_FIELD_PLACEMENT_TOUR_PRICE_LABEL','tour_price_label');
define('PROFILE_FIELD_PLACEMENT_TOUR_CURRENCY','tour_currency_id');
define('PROFILE_FIELD_PLACEMENT_GROUP_SIZE','group_size_id');




// placement job profile
define('PROFILE_FIELD_PLACEMENT_JOB_REFERENCE','reference');
define('PROFILE_FIELD_PLACEMENT_JOB_DURATION_LABEL','job_duration_label');
define('PROFILE_FIELD_PLACEMENT_JOB_DURATION_FROM','job_duration_from_id');
define('PROFILE_FIELD_PLACEMENT_JOB_DURATION_TO','job_duration_to_id');
define('PROFILE_FIELD_PLACEMENT_JOB_START_DT','job_start_date'); // actually these 2date fields are divided into month / year
define('PROFILE_FIELD_PLACEMENT_JOB_CLOSING_DATE','close_date');
define('PROFILE_FIELD_PLACEMENT_JOB_START_DT_MULTIPLE','start_dt_multiple'); 
define('PROFILE_FIELD_PLACEMENT_JOB_CONTRACT_TYPE','contract_type');
define('PROFILE_FIELD_PLACEMENT_JOB_SALARY','job_salary');
define('PROFILE_FIELD_PLACEMENT_JOB_BENEFITS','job_benefits');
define('PROFILE_FIELD_PLACEMENT_JOB_EXPERIENCE','experience');
define('PROFILE_FIELD_PLACEMENT_JOB_OPTIONS','job_options');



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
