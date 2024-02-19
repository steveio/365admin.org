<?php

/**
 * Abstract Content Assembler 
 * 
 * Interface for fetching page content (article | profile | activity | destination | search result) & provision template rendering 
 * 
 * Provides a base class for fetching associated data common to all content type eg reviews, related content 
 *
 */

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

// default results template for an unpublished URL 
define("CONTENT_DEFAULT_RESULT_TEMPLATE", "0");


abstract class AbstractContentAssembler {

    protected $link_to; /* string (eg PLACEMENT || COMPANY  || ARTICLE) used to associate related attributes */
    protected $link_id; /* int id of profile associated content is linked to */
    protected $link_label; /* string label for linked content type */

    protected $oTemplateList;
    protected $strTemplatePath;

    protected $oReviewTemplate;

    protected $aRelatedArticle;
    protected $aRelatedProfile;

    protected $oRequestRouter; // reference to RequestRouter 

    public function __Construct() 
    {
        $this->oTemplateList = new TemplateList();
        $this->oTemplateList->GetFromDB();
        
        $this->aRelatedArticle = array();
        $this->aRelatedProfile = array();

    }

    public function SetRequestRouter($oRequestRouter)
    {
        $this->oRequestRouter = $oRequestRouter;
    }

    public function GetRequestRouter()
    {
        return $this->oRequestRouter;        
    }

    public function SetTemplatePath($templatePath)
    {
        $this->strTemplatePath = $templatePath;
    }

    abstract public function GetById($id);

    abstract public function GetByPath($path);

    
    public function GetLinkTo() {
        return $this->link_to;
    }
    
    public function SetLinkTo($sLinkTo) {
        $this->link_to = $sLinkTo;
    }
    
    public function GetLinkId() {
        return $this->link_id;
    }
    
    public function SetLinkId($sLinkId) {
        $this->link_id = $sLinkId;
    }

    public function GetReviews($link_id, $link_to, $link_label)
    {
        global $oHeader;
        
        $oCssInclude = new CssInclude();
        $oCssInclude->SetHref('/css/jquery.rateyo.min.css');
        $oCssInclude->SetMedia('screen');
        $oHeader->SetCssInclude("CSS_GENERIC", $oCssInclude);
        
        $oJsInclude = new JsInclude();
        $oJsInclude->SetSrc("/includes/js/jquery.rateyo.min.js");
        $oHeader->SetJsInclude($oJsInclude);
        
        $oJsInclude = new JsInclude();
        $oJsInclude->SetSrc("/includes/js/review.js");
        $oJsInclude->SetReferrerPolicy("origin");
        $oHeader->SetJsInclude($oJsInclude);

        $oHeader->Reload();

        $oReviews = new Review();
        $aReview = $oReviews->Get($link_id,$link_to,1);

        if (!$aReview)
        {
            $aReview = array();
            $oReviewTemplate = new Template();
            $this->oReviewTemplate = $oReviewTemplate;
        }
        
        $bHasReviewRating = false;
        $iReviewRating = 0;
        if (is_array($aReview) && count($aReview) >= 1)
        {
            $bHasReviewRating = true;
            foreach($aReview as $oReview)
            {
                $iReviewRating += $oReview->GetRating();
            }
            $iReviewRating = floor($iReviewRating / count($aReview));
        }
        $oReviewTemplate = new Template();
        $oReviewTemplate->Set('LINK_TO', $link_to);
        $oReviewTemplate->Set('LINK_ID', $link_id);
        $oReviewTemplate->Set('LINK_NAME', " : ".$link_label);
        $oReviewTemplate->Set('REVIEWS',$aReview);
        $oReviewTemplate->Set('COUNT',count($aReview));
        
        $oReviewTemplate->Set('REVIEWRATING',$iReviewRating);
        $oReviewTemplate->Set('HASREVIEWRATING',$bHasReviewRating);
        $oReviewTemplate->Set('HAS_REVIEW',true);

        $oReviewTemplate->LoadTemplate("review.php");
        
        $this->oReviewTemplate = $oReviewTemplate;
    }

    /*
     * 
     * @param profile_type : CONTENT_COMPANY | CONTENT_PLACEMENT
     * @note - source content id is oid for profiles, id for articles
     */
    public function GetRelatedProfile($solr_id, $profile_type, $limit = 25)
    {
        global $solr_config;
        
        $oSolrMoreLikeSearch = new SolrMoreLikeSearch($solr_config);
        $oSolrMoreLikeSearch->setRows($limit);
        $oSolrMoreLikeSearch->getRelatedProfile($solr_id, $profile_type);

        $aTmp = $oSolrMoreLikeSearch->getId();

        $aRelatedProfile = array();
        if (is_array($aTmp) && count($aTmp) >= 1) {
            $aRelatedId = array();
            foreach($aTmp as $idx => $a) {
                $aRelatedId[] = $a['profile_id'];
            }
            if ($profile_type == CONTENT_PLACEMENT)
            {
                //$this->aRelatedProfile = PlacementProfile::Get("ID_LIST_SEARCH_RESULT",$aRelatedId, $filter_from_search = false);
                $this->aRelatedProfile = PlacementProfile::GetRelatedById($aRelatedId, "related_id");
            } else {
                $this->aRelatedProfile = CompanyProfile::Get("ID", $aRelatedId, false);
            }
        }
    }

    public function GetKeywords($solr_id, $limit = 25)
    {
        global $solr_config;
        
        $oSolrMoreLikeSearch = new SolrMoreLikeSearch($solr_config);
        $aFilterQuery = array();
        $oSolrMoreLikeSearch->setRows(25);
        
        return $oSolrMoreLikeSearch->getKeywords($solr_id);
    }

    public function GetRelatedArticle($solr_id, $limit = 25)
    {
        global $solr_config;
        
        $oSolrMoreLikeSearch = new SolrMoreLikeSearch($solr_config);
        $aFilterQuery = array();
        $aFilterQuery['-title'] = "365";
        $aFilterQuery['-desc_short'] = "365";
        
        $oSolrMoreLikeSearch->setRows($limit);
        
        $aRelatedArticle = $oSolrMoreLikeSearch->getRelatedArticle($solr_id,$aFilterQuery, $limit);

        if (is_array($aRelatedArticle))
        {
            $this->aRelatedArticle = $aRelatedArticle;
        }
    }
    
}