<?php

define("HEADER_HTTP_404", "HTTP/1.0 404 Not Found");
define("HEADER_HTTP_500", "HTTP/1.0 500 Internal Server Error");

define("PATH_TO_MVC_ROUTE_MAP", "/conf/steps.xml");


class RequestRouter {
    
    protected $aRequestUri; // array URI from $_REQUEST
    protected $strRequestUri; // string URL path eg /blog/article01
    
    
    public function __Construct() {} 

    public function Route($aRequestUri)
    {

        try {
            
            $this->SetRequestUri($aRequestUri);
            $this->RouteMapStatic();
            $this->RouteMapMVC();

        } catch (InvalidSessionException $e) {  // invalid session / session expired
            $this->HttpRedirect(HEADER_HTTP_500, "/".ROUTE_ERROR);
        } catch (NotFoundException $e) {  // 404 not found error
            $this->HttpRedirect(HEADER_HTTP_404, "/".ROUTE_ERROR);
        } catch (Exception $e) { // general exception
            Logger::DB(1,get_class($this)."::".__FUNCTION__."()",$e->getMessage());
            if ($this->strRequestUri != "/".ROUTE_ERROR)
            {
                $this->HttpRedirect(HEADER_HTTP_500, "/".ROUTE_ERROR);
            } else {
                die("FATAL ERROR: an exception occured during request mapping for route /error");
            }
        }
    }
    
    public function GetRequestUri()
    {
        return $this->strRequestUri;
    }

    public function SetRequestUri($aRequestUri)
    {
        if (is_array($aRequestUri) && count($aRequestUri) >= 1 && isset($aRequestUri[1])) {
            $this->aRequestUri = $aRequestUri;
            $this->strRequestUri = "/".$aRequestUri[1];

            Logger::DB(3,__CLASS__."->".__FUNCTION__."()","Request URI: ".$this->strRequestUri);

        } else {
            throw new Exception("Invalid route path _REQUEST");
        }

    }

    /* static URL alias -> php filename route mapping */
    public function RouteMapStatic()
    {
        global $db, $oAuth, $oSession, $oAuth, $oBrand, $oHeader, $oFooter;
        
        $script = "";

        try {

            switch ($this->GetRequestUri()) 
            {
                case "/logout" :
                    $script = "logout.php";
                    break;
                case "/user" :
                    $script = "user.php";
                    break;
                case "/approve" :
                    $script = "approve.php";
                    break;
                case "/category-admin" :
                    $script = "category_admin.php";
                    break;
                case "/activity-admin" :
                    $script = "activity_admin.php";
                    break;
                case "/review-report" :
                    $script = "review_report.php";
                    break;
                case "/edit_review" :
                    $script = "edit_review.php";
                    break;
                case "/enquiry-report" :
                    $script = "enquiry_report.php";
                    break;
                case "/article" :
                    $script = "article.php";
                    break;
                case "/article-manager" :
                    $script = "article_mgr.php";
                    break;
                case "/article-editor" :
                    $script = "article_edit.php";
                    break;
                case "/article-publisher" :
                    $script = "article_pub.php";
                    break;
                case "/article-preview" :
                    $script = "article.php";
                    break;
            }

            if (strlen($script) >= 1) // matched static route
            {

                Logger::DB(2,__CLASS__."->".__FUNCTION__."()","Static Route: ".$script);

                if (file_exists($script))
                {
                    require_once($script);
                    die();
                } else {
                    throw new Exception("Static Route Map: php script ".$script." does not exist (Request URI: ".$this->GetRequestUri().")");
                }
            }

        } catch (Exception $e) {
            throw $e;
        }
    }
        

    /*  Handle MVC routes (defained & mapped as "steps" in /conf/steps.xml) */
    public function RouteMapMVC()
    {

        global $oSession, $oBrand;

        try {
            
            /**
             * Now attept to match MVC step routes
             */
            $oController = $oSession->GetMVCController();
            
            if (!$oController) {
                $oController = new MVCController(BASE_PATH);
                $oController->SetRouteFromXmlFile(BASE_PATH.PATH_TO_MVC_ROUTE_MAP,$oBrand->GetSiteId());
                $oSession->SetMVCController($oController);
            }

            $oController->SetRequestUri($this->GetRequestUri());
            $oController->MapRequest();
            $oController->Process();

            $oSession->Save();

        } catch (Exception $e)
        {
            throw $e;
        }
    }

    public function HttpRedirect($strHttpHeader, $redirectUrl )
    {
        ob_end_clean();
        header($strHttpHeader);        
        header("Location: ".$redirectUrl);
        die();   
    }
    
}