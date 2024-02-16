<?php

define("HEADER_HTTP_404", "HTTP/1.0 404 Not Found");
define("HEADER_HTTP_500", "HTTP/1.0 500 Internal Server Error");


/*
 * RequestRouter -
 * 
 * Handles static and dynamic URL route mappings
 * 
 * 
 * 
 */


class RequestRouter {
    
    protected $oMVCController; // instance of MVC controller
    protected $aRequestUri; // array URI from $_REQUEST
    protected $strRequestUri; // string URL path eg /blog/article01
    protected $strContentType; // content general type: CONTENT_COMPANY, CONTENT_PLACEMENT, CONTENT_ARTICLE
    
    protected $aStaticRoute = array(); // key/value array of static (url -> php script) route mappings
    protected $aStaticCallback = array();  // key/value array of callback route (url -> $class->method() ) mappings

    public function __Construct() {} 

    public function Route($aRequestUri)
    {
        global $oSession;

        try {            
            
            $this->SetRequestUri($aRequestUri);
            $this->RouteMapMVC();
            $this->RouteMapStatic();

        } catch (NotFoundException $e) {  // 404 not found error

            print_r("<pre>");
            print_r($e);
            print_r("</pre>");
            die();

            if (is_object($oSession))
            {
                $oMessage = new Message(MESSAGE_TYPE_ERROR, MESSAGE_TYPE_VALIDATION_ERROR, $e->getMessage());
                $oSession->SetMessage($oMessage);
            }

            $this->HttpRedirect(HEADER_HTTP_404, "/".ROUTE_ERROR);
            die();
        } catch (Exception $e)
        {
            Logger::DB(1,get_class($this)."::".__FUNCTION__."()",$e->getMessage());
            Logger::DB(1,get_class($this)."::".__FUNCTION__."()",$e->getTraceAsString());
            
            
            print_r($e->getMessage());
            die(__FILE__."::".__LINE__);
            
            if ($this->strRequestUri == "/".ROUTE_ERROR)
            {
                $this->HttpRedirect(HEADER_HTTP_500, "/back_soon.php");
                die();
            } else {

                if (is_object($oSession))
                {
                    $oMessage = new Message(MESSAGE_TYPE_ERROR, MESSAGE_TYPE_ERROR, $e->getMessage());
                    $oSession->SetMessage($oMessage);
                }

                $this->HttpRedirect(HEADER_HTTP_500, "/".ROUTE_ERROR);
            }
            
        }
        /*    
        } catch (InvalidSessionException $e) {  // invalid session / session expired
            $this->HttpRedirect(HEADER_HTTP_500, "/".ROUTE_ERROR);
        } catch (Exception $e) { // general exception
                        
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
            $this->strRequestUri = "/".$aRequestUri[1]; // @note - static routes map only consider 1st URL segment eg /blog

            if (!$this->validateUri($this->strRequestUri))
            {
                throw new Exception("Invalid URL syntax or lenght: ".$this->strRequestUri);
            }

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

    public function LoadStaticRoutes($xml_file_path)
    {

        if (!file_exists($xml_file_path)) {
            throw new Exception(ERROR_INVALID_XML_FILE_PATH . $xml_file_path);
        }
            
        $oXml = simplexml_load_file($xml_file_path);
            
        if (!is_object($oXml) || count($oXml->route) < 1) throw new Exception(ERROR_INVALID_XML_ROUTE_DEFS);
            
        foreach($oXml->route as $oXmlElement) {
            $url = (string) $oXmlElement->url;
            if (isset($oXmlElement->filename))
            {
                $filename = (string) $oXmlElement->filename;
                $this->aStaticRoute[$url] = $filename;
            } else if (isset($oXmlElement->callback))
            {
                $callback = (string) $oXmlElement->callback;
                $this->aStaticCallback[$url] = $callback;
            }
        }
                
    }

    /* static URL alias -> php filename route mapping  
     */
    public function RouteMapStatic()
    {
        global $db, $oAuth, $oSession, $oAuth, $oBrand, $oHeader, $oFooter;
        
        $script = "";
        $callback = "";

        try {

            // load static routes from configuration 
            $this->LoadStaticRoutes(PATH_TO_STATIC_ROUTE_MAP);

            
            // url -> php script mappings
            if (array_key_exists($this->GetRequestUri(), $this->aStaticRoute))
            {
                $script = $this->aStaticRoute[$this->GetRequestUri()];
            }

            // url -> $class->method() mappings
            if (array_key_exists($this->GetRequestUri(), $this->aStaticCallback))
            {
                $callback = $this->aStaticCallback[$this->GetRequestUri()];
            }

            /*
            print_r("<pre>");
            print_r($this);
            print_r($script);
            print_r($callback);
            print_r("</pre>");
            die();
            */

            if (strlen($script) >= 1) // matched static url -> php script route
            {
                $this->processInclude($script);
            }

            if (strlen($callback) >= 1) // matched static url -> $class->method() callback 
            {
                $this->processCallback($callback);
            }
            
            // unamatched route, try to categorise request type
            $this->GetPageTypeFromUri();

        } catch (Exception $e) {
            throw $e;
        }
    }

    // include dependency script, then call die();
    private function processInclude($script)
    {   
        global $db, $oBrand, $oAuth, $oHeader, $oFooter;

        // handle static route -> PHP script mappings
        if (isset($script) && strlen($script) >=1 && file_exists($script))
        {
            require_once($script);
            die();
        } else {
            throw new Exception("Static Route Map: php script ".$script." does not exist (Request URI: ".$this->GetRequestUri().")");
        }
    }

    // execute callback string in format $class->method(), then continue processing
    private function processCallback($callback)
    {
        global $db, $oBrand, $oAuth, $oHeader, $oFooter;

        if (isset($callback) && strlen($callback) >=1)
        {
            $aBits = explode("->",$callback);
            $aBits[0] = str_replace("$", "", $aBits[0]);
            $aBits[1] = str_replace("()", "", $aBits[1]);
            
            $class = $aBits[0];
            $method = $aBits[1];
            
            if ($class == "this" && is_callable(array('RequestRouter',$method)))
            {
                $this->$method();
            } else if (is_callable(array($class,$method))) {
                $obj = new $class;
                $obj->$method();
            }
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
            $this->oMVCController = $oSession->GetMVCController();

            if (is_object($this->oMVCController))            
                $this->oMVCController->Reset();

                if (!$this->oMVCController) {
                    $this->oMVCController = new MVCController();
                    $this->oMVCController->SetRouteFromXmlFile(PATH_TO_MVC_ROUTE_MAP,$oBrand->GetSiteId());                
                    $oSession->SetMVCController($this->oMVCController);
            }
            
            $this->oMVCController->SetExceptionOnNotFound(FALSE);

            $this->oMVCController->SetRequestUri($this->GetRequestUri());
            $this->oMVCController->Process();
            
            $oSession->Save();

            if ($this->oMVCController->GetPassThrough())
            {
                return true;
            } else {
            
                if (is_numeric($this->oMVCController->GetCurrentRouteId())) // route matched, nothing further to do
                {
                    die();
                }

            }

        } catch (Exception $e)
        {
            throw $e;
        }
    }

    public function GetPageTypeFromUri()
    {
        
        switch(true)
        {
            case $this->isCategory($this->GetRequestUri()) :
                break;
            case $this->isActivity($this->GetRequestUri()) :
                break;
            default:
                $this->ProcessArticlePageRequest();
        }
    }

    public function isCategory($strUrlName)
    {
        global $db, $_CONFIG;
        
        $sql = "SELECT id,name FROM category WHERE url_name = '".$this->GetRequestUri()."'";
        $db->query($sql);
        if ($db->getNumRows() == 1) {
            $this->strContentType = CONTENT_TYPE_CATEGORY;
            $aRes = $db->getRow();

            $_REQUEST['cat'] = "category";
            $_REQUEST['cat_name'] = $aRes['name'];
            $_REQUEST['id'] = $aRes['id'];
            $_REQUEST['clean_url'] = $_CONFIG['url'].$this->aRequestUri[1];

            return true;
        }

    }
    
    public function isActivity($strUrlName)
    {
        global $db, $_CONFIG;
        
        $sql = "SELECT id,name FROM activity WHERE url_name = '".$this->GetRequestUri()."'";
        $db->query($sql);
        if ($db->getNumRows() == 1) {
            $this->strContentType = CONTENT_TYPE_ACTIVITY;
            $aRes = $db->getRow();

            $_REQUEST['cat'] = "activity";
            $_REQUEST['cat_name'] = $aRes['name'];
            $_REQUEST['id'] = $aRes['id'];
            $_REQUEST['clean_url'] = $_CONFIG['url']."/".$this->aRequestUri[1];
            
            $_REQUEST['page_meta_description'] = trim($_CONFIG['txt_pattern_generic']) ." ". $aRes['name']. ". " .$aRes['description'];

            return true;
        }
    }


    protected function ProcessCompanyPageRequest()
    {
        // Placement request /company/<comp-name>/<placement-name>
        if ((isset( $this->aRequestUri[3]) && 
                    $this->aRequestUri[3] != "") 
                    && ($this->aRequestUri[3] != "edit") 
                    && ($this->aRequestUri[2] != "a-z")) 
        {
            return $this->ProcessPlacementPageRequest();
        }

        // Company AZ request /company/a-z/<letter>
        if ((isset($this->aRequestUri[2]) && $this->aRequestUri[2] != "") && ($this->aRequestUri[2] == "a-z")) 
        {
            $oContentAssembler = new CompanyProfileContentAssembler();
            $oContentAssembler->SetRequestRouter($this);
            $oContentAssembler->ProcessCompanyAZPageRequest();
            die();
        }

        // view/add/edit/delete company profile
        $oCompanyProfileController = new CompanyProfileController();
        $oCompanyProfileController->Process();
        die();
    }

    protected function ProcessPlacementPageRequest()
    {

        // placement list - view all placement for company /company/<comp-name>/placements
        if ($this->aRequestUri[3] == "placements") {
            $oContentAssembler = new PlacementProfileContentAssembler();
            $oContentAssembler->SetRequestRouter($this);
            $oContentAssembler->ProcessPlacementList();
        }

        $this->oMVCController->SetCurrentRouteId(10);
        $oProfileController = $this->oMVCController->GetRouteById(10);
        
        // view/add/edit/delete placement
        $oProfileController->Process();
        
        die();
    }

    /*
     * Route mapped via routes_static.xml
     * 
     */
    protected function ProcessDestinationPageRequest()
    {
        global $db, $_CONFIG;

        if (strtolower($this->aRequestUri[1]) == "travel")
            $this->aRequestUri[1] = "country";

        $sql = "SELECT id,name,url_name FROM ".$this->aRequestUri[1]." WHERE url_name = '".$this->aRequestUri[2]."'";
        $db->query($sql);
        if ($db->getNumRows() == 1) {
            $aRes = $db->getRow();

            $_REQUEST['cat'] = $this->aRequestUri[1];
            $_REQUEST['cat_name'] = $aRes['name'];
            $_REQUEST['cat_url_name'] = $aRes['url_name'];
            $_REQUEST['sub_cat'] = isset($this->aRequestUri[3]) ? $this->aRequestUri[3] : "";
            $_REQUEST['id'] = $aRes['id'];
            $_REQUEST['clean_url'] = $_CONFIG['url']."/".$this->aRequestUri[1]."/".$this->aRequestUri[2];
            if (strlen($this->aRequestUri[3]) > 1) { /* append /<country>/[travel-tour||volunteer] etc  */
                $_REQUEST['clean_url'] = $_REQUEST['clean_url']."/".$this->aRequestUri[3];
            }
            $_REQUEST['page_title'] = $_CONFIG['txt_pattern_generic'] . " in " . trim($aRes['name']);
            $_REQUEST['page_meta_description'] = $_CONFIG['page_description']." ".$_CONFIG['site_title']." listings for '".$_CONFIG['txt_pattern_generic'] ." in ". trim($aRes['name'])."'. ".$_CONFIG['txt_pattern_generic'] . " in " . trim($aRes['name']).". " . $aRes['name']." ".trim($_CONFIG['txt_pattern_generic']) .".  Find " . $_CONFIG['txt_pattern_generic'] ." in ". $aRes['name'] .".";

            $this->strRequestPageType = CONTENT_TYPE_DESTINATION;

            return $this->ProcessArticlePageRequest();

        } else {
            throw new NotFoundException("Destination page '".$this->aRequestUri[1]."' / ".$this->aRequestUri[2]. "' not found");
        }
    }

    protected function ProcessArticlePageRequest()
    {
        global $_CONFIG, $oBrand;

        if (!isset($this->strContentType) )
        {
            $this->strContentType = CONTENT_TYPE_ARTICLE;
        }

        try {
            
            $oContentAssembler = new ArticleContentAssembler();
            $oContentAssembler->SetRequestRouter($this);
            
            // 1.  Extract Article Path from URI (Published Articles)
            if (count($this->GetRequestArray()) > 2)
            {

                $oArticle = $oContentAssembler->GetByPath($this->GetRequestUri(), $oBrand->GetSiteId());

            } else {
                
                // 2.  Extract Article ID from $_REQUEST (UnPublished Articles)
                $id = isset($_REQUEST['id']) ? $_REQUEST['id'] : null;

                if(!is_numeric($id)) throw new NotFoundException("Page not found : ".$this->GetRequestUri());
            
                $oTemplateList = new TemplateList();
                $oTemplateList->GetFromDB();
                $templatePath = $oTemplateList->GetFilenameById(ARTICLE_TEMPLATE_ARTICLE_DEFAULT);

                $oContentAssembler->SetTemplatePath($templatePath);
                $oArticle = $oContentAssembler->GetArticleById($id);
                
            }

        } catch (NotFoundException $e) {
            throw $e;
        } catch (Exception $e) {
            throw $e;
        }

    }


    /*
    * Handles pages that display result sets:
    * 			/search/<search-phrase>
    * 
    * having no associated (mapped) article
    *  
    */
    protected function ProcessSearchResultPageRequest()
    {
        
        $_REQUEST['page_title'] = $this->aRequestUri[2];
        $_REQUEST['cat_name'] = $this->aRequestUri[2];
        $_REQUEST['cat'] = "search";
        
        
        $oContentAssembler = new SearchResultContentAssembler();
        $oContentAssembler->SetRequestRouter($this);        
        $oContentAssembler->GetByPath($this->GetRequestUri());
        
        die(__FILE__."::".__LINE__);
    }

    /**
     * Check if all URI segments eg /<seg-1>/<seg-2> map to valid namespace ( Category | Activity | Country | Continent 0 identifiers
     * @return boolean
     */
    public function isNamespaceMatchedURL()
    {
        $aRequestUri = $this->aRequestUri;
        array_shift($aRequestUri);
        
        foreach($aRequestUri as $strKeyword)
        {
            if (strlen($strKeyword) < 1) continue;
            
            // in order of match probability
            $arrIdentifier = array("activity","country","continent","category");
            
            $bValid = false;
            
            foreach($arrIdentifier as $strIdentifierKeyword)
            {
                try {
                    
                    $result = NameService::lookupNameSpaceIdentifier($strIdentifierKeyword,$strKeyword);
                    
                    if (!isset($result['id']) || !is_numeric($result['id']))
                    {
                        throw new ErrorException("Unmatched URI segment");
                    } else {
                        $bValid = true;
                    }
                    
                } catch (Exception $e) {}
            }
            if (!$bValid) return false;
        }
        return true;
    }

    public function HttpRedirect($strHttpHeader, $redirectUrl )
    {
        ob_end_clean();
        header($strHttpHeader);
        header("Location: ".$redirectUrl);
        die();
    }
}