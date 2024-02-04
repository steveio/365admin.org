<?php

define("HEADER_HTTP_404", "HTTP/1.0 404 Not Found");
define("HEADER_HTTP_500", "HTTP/1.0 500 Internal Server Error");


class RequestRouter {
    
    protected $aRequestUri; // array URI from $_REQUEST
    protected $strRequestUri; // string URL path eg /blog/article01
    protected $strContentType; // content general type: CONTENT_COMPANY, CONTENT_PLACEMENT, CONTENT_ARTICLE
    protected $strContentSubType; // content sub type
    
    public function __Construct() {} 

    public function Route($aRequestUri)
    {

        try {

            $this->SetRequestUri($aRequestUri);            
            $this->RouteMapStatic();            
            $this->RouteMapMVC();

          
        } catch (Exception $e)
        {
            Logger::DB(1,get_class($this)."::".__FUNCTION__."()",$e->getMessage());
            
            print_r("<pre>");
            print_r($e);
            print_r("</pre>");
            die();
            
        }
        /*    
        } catch (InvalidSessionException $e) {  // invalid session / session expired
            $this->HttpRedirect(HEADER_HTTP_500, "/".ROUTE_ERROR);
        } catch (NotFoundException $e) {  // 404 not found error
            $this->HttpRedirect(HEADER_HTTP_404, "/".ROUTE_ERROR);
        } catch (Exception $e) { // general exception
                        
            if ($this->strRequestUri != "/".ROUTE_ERROR)
            {
                $this->HttpRedirect(HEADER_HTTP_500, "/".ROUTE_ERROR);
            } else {
                die("FATAL ERROR: an exception occured during request mapping for route /error");
            }
        }
        */
    }
    
    public function GetRequestUri()
    {
        return $this->strRequestUri;
    }

    public function GetRequestArray()
    {
        return $this->aRequestUri;
    }

    public function SetRequestUri($aRequestUri)
    {
        if (is_array($aRequestUri) && count($aRequestUri) >= 1 && isset($aRequestUri[1])) {
            
            $this->aRequestUri = $aRequestUri;
            $this->strRequestUri = "/".$aRequestUri[1];

            if (!$this->validateUri($this->strRequestUri))
            {
                throw new Exception("Invalid URL syntax or lenght: ".$this->strRequestUri);
            }

            Logger::DB(3,__CLASS__."->".__FUNCTION__."()","Request URI: ".$this->strRequestUri);

        } else {
            throw new Exception("Invalid route path _REQUEST");
        }

    }

    public function validateUri($str)
    {
        // valid domain specific URL chars & length check
        if (preg_match('/[a-zA-Z0-9_\-\/]+/',$str) || strlen($str) > 256 )
        {
            return true;
        }
    }

    /* static URL alias -> php filename route mapping 
     * 
     * @todo - load static routes from config  
     * 
     */
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
                case "/company" :
                    $this->ProcessCompanyPageRequest();
                    die();
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
        

    /*  Handle MVC routes (mapped to "controllers" in /conf/routes.xml) */
    public function RouteMapMVC()
    {
        global $oSession, $oBrand;

        try {
            
            /**
             * Now attept to match MVC routes
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

    public function GetPageTypeFromUri()
    {
        global $db;
        
        if (RequestRouter::isCategory($this->GetRequestUri()))
        {
            $sql = "SELECT id,name FROM category WHERE url_name = '".$this->GetRequestUri()."'";
            $db->query($sql);
            if ($db->getNumRows() == 1) {
                $this->strContentType = CONTENT_TYPE_CATEGORY;
                $aRes = $db->getRow();
                $this->ProcessCategoryPageRequest($aRes);
                return true;
            }
        } elseif (RequestRouter::isActivity($this->GetRequestUri())) {
            
            $sql = "SELECT id,name FROM activity WHERE url_name = '".$this->GetRequestUri()."'";
            $db->query($sql);
            if ($db->getNumRows() == 1) {
                $this->strContentType = CONTENT_TYPE_ACTIVITY;
                $aRes = $db->getRow();
                $this->ProcessActivityPageRequest($aRes);
                return true;
            }
            
        } else {
            $this->ProcessArticlePageRequest();
        }
    }

    public static function isCategory($strUrlName)
    {
        global $db;
        
        $sql = "SELECT 1 FROM category WHERE url_name = '".$strUrlName."'";
        $db->query($sql);
        
        return ($db->getNumRows() == 1) ? true : false;
    }
    
    public static function isActivity($strUrlName)
    {
        global $db;
        
        $sql = "SELECT 1 FROM activity WHERE url_name = '".$strUrlName."'";
        $db->query($sql);
        
        return ($db->getNumRows() == 1) ? true : false;
    }

    protected function ProcessActivityPageRequest($aRes)
    {
        global $_CONFIG;
        
        $_REQUEST['cat'] = "activity";
        $_REQUEST['cat_name'] = $aRes['name'];
        $_REQUEST['id'] = $aRes['id'];
        $_REQUEST['clean_url'] = $_CONFIG['url']."/".$this->aRequestUri[1];
        
        $_REQUEST['page_meta_description'] = trim($_CONFIG['txt_pattern_generic']) ." ". $aRes['name']. ". " .$aRes['description'];
        
        return $this->ProcessArticlePageRequest();
    }
    
    protected function ProcessCategoryPageRequest($aRes)
    {
        global $db, $_CONFIG;
        
        $_REQUEST['cat'] = "category";
        $_REQUEST['cat_name'] = $aRes['name'];
        $_REQUEST['id'] = $aRes['id'];
        $_REQUEST['clean_url'] = $_CONFIG['url'].$this->aRequestUri[1];
        
        return $this->ProcessArticlePageRequest();
    }

    protected function ProcessCompanyPageRequest()
    {
        global $db, $_CONFIG, $oHeader;

        // Placement request /company/<comp-name>/<placement-name>
        if ((isset( $this->aRequestUri[3]) && 
                    $this->aRequestUri[3] != "") 
                    && ($this->aRequestUri[3] != "edit") 
                    && ($this->aRequestUri[2] != "a-z")) 
        {
            $this->PorcessPlacementPageRequest();
        }

        // Company AZ request /company/a-z/<letter>
        if ((isset($this->aRequestUri[2]) && $this->aRequestUri[2] != "") && ($this->aRequestUri[2] == "a-z")) 
        {
            $this->ProcessCompanyAZPageRequest();
        }

        if ($this->aRequestUri[3] == "edit") { // redirect to admin.oneworld365.org/company/<name>/edit
            $url = preg_replace("/www/","admin",$_CONFIG['url']);
            header("Location: ".$url.$_SERVER['REQUEST_URI']);
            die();
        }

        // view company profile
        $oCompanyProfileController = new CompanyProfileController();
        $oCompanyProfileController->SetMVCMode(CompanyProfileController::MODE_VIEW);
        $oCompanyProfileController->SetCompanyUrlName($this->aRequestUri[2]);
        $oCompanyProfileController->ViewProfile();

        die();
    }

    protected function ProcessCompanyAZPageRequest()
    {
        if ($this->aRequestUri[2] == "a-z" && $this->aRequestUri[3] != "") {
            $this->isLowerCaseLetter($this->aRequestUri[3]);
            $_REQUEST['letter'] = $this->aRequestUri[3];
        } else {
            $_REQUEST['letter'] = "a";
        }
        require_once("./company_list.php");
    }

    protected function ProcessPlacementPageRequest()
    {
        global $db, $_CONFIG;

        // view all placements
        if ($this->aRequestUri[3] == "placements") {
            
        } else { // view a single placement
            
            
        }
    }

    protected function ProcessArticlePageRequest()
    {
        global $_CONFIG, $oBrand;

        
        if (!isset($this->strContentType) )
        {
            $this->strContentType = CONTENT_ARTICLE;
        }


        try {

            $oArticleAssembler = new ArticleContentAssembler();
            
            // 1.  Extract Article Path from URI (Published Articles)
            if (count($this->GetRequestArray()) > 2)
            {

                $oArticle = $oArticleAssembler->GetArticleByPath($this->GetRequestUri(), $oBrand->GetSiteId());
                
            } else {
                
                // 2.  Extract Article ID from $_REQUEST (UnPublished Articles)
                $id = isset($_REQUEST['id']) ? $_REQUEST['id'] : null;
                if(!is_numeric($id)) throw new Exception("ERROR: Invalid Article ID");
            
                $oTemplateList = new TemplateList();
                $oTemplateList->GetFromDB();
                $templatePath = $oTemplateList->GetFilenameById(ARTICLE_TEMPLATE_ARTICLE_DEFAULT);

                $oArticleAssembler->SetTemplatePath($templatePath);
                $oArticle = $oArticleAssembler->GetArticleById($id);
                
            }
            
        } catch (Exception $e) {
            $aResponse['msg'] = "ERROR: ".$e->getMessage();
            $aResponse['status'] = "danger";
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