<?php

/*
 * init.php
 * 
 * Handles generic initialisation and includes 
 * 
 * All routes should have alias URLs (rather than calling php script directly
 *  and pass through index.php.  There may be some cases where legacy links 
 * still refer to php script, so there's some duplication between index.php and init.php 
 * 
 */


/* class auto-loader
 */
function class_autoload($classname) {
        $classpath = PATH_2_STEP_CLASSES . $classname.".php";

    if (!file_exists($classpath)) {
        throw new Exception("Unable to load step class: ".$classname);
    }

        require_once($classpath);

}

spl_autoload_register("class_autoload");


/* global php includes */
require_once(BASE_PATH."/classes/Exceptions.php");
require_once(BASE_PATH."/classes/StepController.php");
require_once(BASE_PATH."/classes/AbstractStep.php");
require_once(BASE_PATH."/classes/GenericStep.php");
require_once(BASE_PATH."/classes/ProfileStep.php");
require_once(BASE_PATH."/classes/Brand.php");
require_once(BASE_PATH."/classes/request.php");
require_once(BASE_PATH."/classes/http.php");
require_once(BASE_PATH."/classes/session.php");
require_once(BASE_PATH."/classes/db_pgsql.class.php");
require_once(BASE_PATH."/classes/cache.class.php");
require_once(BASE_PATH."/classes/logger.php");
require_once(BASE_PATH."/classes/authenticate.class.php");
require_once(BASE_PATH."/classes/login.class.php");
require_once(BASE_PATH."/classes/user.class.php");
require_once(BASE_PATH."/classes/AccountApplication.php");
require_once(BASE_PATH."/classes/error.class.php");
require_once(BASE_PATH."/classes/Message.php");
require_once(BASE_PATH."/classes/template.class.php");
require_once(BASE_PATH."/classes/layout.class.php");
require_once(BASE_PATH."/classes/header.class.php");
require_once(BASE_PATH."/classes/footer.class.php");
require_once(BASE_PATH."/classes/page.class.php");
require_once("/www/vhosts/oneworld365.org/htdocs/classes/pager.class.php");
require_once(BASE_PATH."/classes/ColumnSort.php");
require_once(BASE_PATH."/classes/category.class.php");
require_once(BASE_PATH."/classes/activity.class.php");
require_once(BASE_PATH."/classes/country.class.php");
require_once(BASE_PATH."/classes/continent.class.php");
require_once(BASE_PATH."/classes/Refdata.php");
require_once(BASE_PATH."/classes/website.class.php");
require_once(BASE_PATH."/classes/mapping.class.php");
require_once(BASE_PATH."/classes/logger.php");
require_once(BASE_PATH."/classes/validation.class.php");
require_once(BASE_PATH."/classes/name_service.class.php");
require_once(BASE_PATH."/classes/image.class.php");
require_once(BASE_PATH."/classes/search.class.php");
require_once(BASE_PATH."/classes/stemmer.class.php");
require_once(BASE_PATH."/classes/indexer.class.php");
require_once(BASE_PATH."/classes/feature.class.php");
require_once(BASE_PATH."/classes/error.class.php");
require_once(BASE_PATH."/classes/template.class.php");
require_once(BASE_PATH."/classes/date.class.php");
require_once(BASE_PATH."/classes/select.php");
require_once(BASE_PATH."/classes/option.class.php");
require_once(BASE_PATH."/classes/sql_builder.class.php");
require_once(BASE_PATH."/classes/enquiry.class.php");
require_once(BASE_PATH."/classes/email2friend.class.php");
require_once(BASE_PATH."/classes/listing.class.php");
require_once(BASE_PATH."/classes/file.class.php");
require_once(BASE_PATH."/classes/ip_address.class.php");
require_once(BASE_PATH."/classes/link.class.php");
require_once(BASE_PATH."/classes/article.class.php");
require_once(BASE_PATH."/classes/ContentMapping.class.php");
require_once(BASE_PATH."/classes/ArticleCollection.class.php");
require_once(BASE_PATH."/classes/ArticleAssembler.class.php");
require_once(BASE_PATH."/classes/search_result.class.php");
require_once(BASE_PATH."/classes/file_upload.class.php");
require_once(BASE_PATH."/classes/json.class.php");
require_once(BASE_PATH."/classes/tabbed_panel.class.php");
require_once(BASE_PATH."/classes/SalesEnquiry.php");
require_once(BASE_PATH."/classes/SecurityQuestion.php");


/* @depreciated - to be replaced by Profile* class topology below */
require_once(BASE_PATH."/classes/company.class.php");
require_once(BASE_PATH."/classes/placement.class.php");

/* Profile System */
require_once(BASE_PATH."/classes/ProfileType.php");
require_once(BASE_PATH."/classes/ProfileInterface.php");
require_once(BASE_PATH."/classes/ProfileAbstract.class.php");
require_once(BASE_PATH."/classes/ProfileFactory.class.php");
require_once(BASE_PATH."/classes/ProfilePlacement.class.php");
require_once(BASE_PATH."/classes/ProfileCompany.class.php");
require_once(BASE_PATH."/classes/ProfileGeneral.class.php");
require_once(BASE_PATH."/classes/ProfileTour.class.php");
require_once(BASE_PATH."/classes/ProfileJob.class.php");
require_once(BASE_PATH."/classes/ProfileSummerCamp.php");
require_once(BASE_PATH."/classes/ProfileVolunteerTravelProject.php");
require_once(BASE_PATH."/classes/ProfileSeasonalJobsEmployer.php");
require_once(BASE_PATH."/classes/ProfileTeachingProject.php");
require_once(BASE_PATH."/classes/ArchiveManager.php");

// email classes
require_once(BASE_PATH."/classes/EmailSender.php");
require_once(BASE_PATH."/classes/htmlMimeMail.php");
require_once(BASE_PATH."/classes/RFC822.php");
require_once(BASE_PATH."/classes/smtp.php");
require_once(BASE_PATH."/classes/mimePart.php");


function my_session_start()
{
      if (ini_get('session.use_cookies') && isset($_COOKIE['PHPSESSID'])) {
           $sessid = $_COOKIE['PHPSESSID'];
      } elseif (!ini_get('session.use_only_cookies') && isset($_GET['PHPSESSID'])) {
           $sessid = $_GET['PHPSESSID'];
      }
      
      session_start();

      return true;
}

try {
    /* set no cache headers */
    header( 'Expires: Sat, 26 Jul 1997 05:00:00 GMT' ); 
    header( 'Last-Modified: ' . gmdate( 'D, d M Y H:i:s' ) . ' GMT' ); 
    header( 'Cache-Control: no-store, no-cache, must-revalidate' ); 
    header( 'Cache-Control: post-check=0, pre-check=0', false ); 
    header( 'Pragma: no-cache' ); 
    
    /* enforce UTF-8 rendering */
    header('Content-Type: text/html; charset=utf-8');
    
    
    /* start a new session */
    my_session_start();

    try {
        /* establish database connection */
        $db = new db($dsn,$debug = false);
        
    } catch (Exception $e) {

    }

    $oSession = new Session();
    if ($oSession->Exists()) {
    	$oSession = $oSession->Get();
    } else {
    	$oSession = $oSession->Create();
    }
   
    if (!is_object($oAuth))
    {
        /* setup an instance of session authentication */
        $oAuth = new Authenticate($db,$redirect = TRUE, $redirect_url = "/".ROUTE_LOGIN, COOKIE_NAME);
        $oAuth->ValidSession();
    }

    define('HOSTNAME',"oneworld365.org");
    
    /* set some additional $_CONFIG params so the legacy classes work */
    $_CONFIG['site_id'] = $aBrandConfig[HOSTNAME]['site_id'];
    $_CONFIG['admin_email'] = $aBrandConfig[HOSTNAME]['admin_email'];
    $_CONFIG['website_email'] = $aBrandConfig[HOSTNAME]['website_email'];
    
    if (!is_object($oBrand))
    {
        $oBrand = new Brand($aBrandConfig[HOSTNAME]);
    }

    /* Global Exception Handler */
    function exception_handler($e) {
    	
    	global $oSession;
    	
    	$oSession->SetErrorCode($e->getCode());
    	$oSession->SetErrorData($e->getMessage());
    	$oSession->Save();

    	print_r($e);
    	die();

    	Logger::Msg($e);
    	Http::Redirect("/".ROUTE_ERROR); 
    }
    
    set_exception_handler('exception_handler');

} catch (Exception $e) {
    print_r($e);
    die();
}




?>
