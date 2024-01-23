<?php


/* when retrieving articles, use exact "=" or fuzzy "like" pattern matching */
define("ARTICLE_SEARCH_MODE_FUZZY",0);
define("ARTICLE_SEARCH_MODE_EXACT",1);

/*
 * A collection of articles, with various retrieval methods
 *
 *
 */
class ArticleCollection implements TemplateInterface  {
    
    private $iLimit;
    private $aArticle;
    private $iSearchMode;
    
    public function __Construct() {
        $this->aArticle = array();
        
        $this->SetSearchMode(ARTICLE_SEARCH_MODE_FUZZY);
    }
    
    public function SetLimit($iLimit) {
        $this->iLimit = $iLimit;
    }
    
    private function GetLimit() {
        return $this->iLimit;
    }
    
    public function SetSearchMode($mode) {
        $this->iSearchMode = $mode;
    }
    
    private function GetSearchMode() {
        return $this->iSearchMode;
    }
    
    public function Count() {
        return count($this->aArticle);
    }
    
    public function Get() {
        return $this->aArticle;
    }
    
    public function Add($oArticle) {
        
        if ((is_object($oArticle)) && ($oArticle instanceof Article)) {
            $this->aArticle[] = $oArticle;
        }
    }
    
    /*
     * Get all articles associated with a section uri
     * 	eg.  uri = "/news"
     * 	returns :
     * 		/news/2010/january/camp-america-events
     * 		/news/2010/january/bunac-special-offers
     * 		...
     *  By default articles are ordered by date DESC (ie most recent)
     *
     */
    public function GetBySectionId($website_id,$sSectionUri,$getAttachedObj = true,$bUnPublished = false, $filterDate = false, $dateFrom = '', $dateTo = '') {
        
        global $db;
        
        if (strlen($sSectionUri) < 1) return false;
        
        if (is_numeric($website_id)) {
            $sWhere = "AND m.website_id = ".$website_id;
        } elseif (is_array($website_id) && count($website_id) >= 1) {
            $sWhere = "AND m.website_id IN (".implode(",",$website_id) .")";
        }
        
        if ($this->GetSearchMode() == ARTICLE_SEARCH_MODE_FUZZY) {
            $scope_sql = "AND m.section_uri LIKE '".$sSectionUri."%'";
        } elseif ($this->GetSearchMode() == ARTICLE_SEARCH_MODE_EXACT) {
            $scope_sql = "AND m.section_uri = '".$sSectionUri."'";
        }
        
        $sqlDateConstraint = "";
        
        if ($filterDate == 1)
        {
            $sqlDateConstraint = " AND a.last_updated >= '".$dateFrom ."' AND a.last_updated <= '".$dateTo."' ";
        }
        
        if ($getAttachedObj == false)
        {
            $fields = " a.id
                        ,a.title
						,to_char(a.created_date,'DD/MM/YYYY') as created_date
					    ,to_char(a.last_updated,'DD/MM/YYYY') as last_updated
						,to_char(a.last_indexed_solr,'DD/MM/YYYY') as last_indexed_solr
                        ";
        } else {
            $fields = " a.*
						,to_char(a.created_date,'DD/MM/YYYY') as created_date
					    ,to_char(a.last_updated,'DD/MM/YYYY') as last_updated
						,to_char(a.last_indexed_solr,'DD/MM/YYYY') as last_indexed_solr
                        ";
            
        }
        
        $sql_limit = '';
        if (is_numeric($this->GetLimit()))
        {
            $sql_limit = "LIMIT ".$this->GetLimit();
        }
        
        if ($bUnPublished) {
            
            $sql = "select
        		    ".$fields."
        		    from
        		    ".DB__ARTICLE_TBL." a
        		    where not exists ( select 1 from ".DB__ARTICLE_MAP_TBL." m where a.id = m.article_id)
        		        ".$sqlDateConstraint."
        		        ORDER BY a.last_updated DESC, a.created_date DESC ".$sql_limit.";";
            
        } else {
            
            $sql = "SELECT
                    ".$fields."
					FROM
						".DB__ARTICLE_TBL." a
						,".DB__ARTICLE_MAP_TBL." m
					WHERE
						1=1
						".$sWhere."
				        ".$scope_sql."
						AND m.article_id = a.id
                        ".$sqlDateConstraint."
					ORDER BY
						a.last_updated DESC, a.created_date DESC ".$sql_limit.";";
            
        }
        
        $db->query($sql);
        
        if ($db->getNumRows() < 1) return array();
        
        foreach ($db->getRows() as $a) {
            
            $oArticle = new Article();
            $oArticle->SetFromArray($a);
            $oArticle->SetMapping();
            $oArticle->SetUrl();
            
            if ($getAttachedObj) {
                $oArticle->SetAttachedProfile();
                $oArticle->SetAttachedImage();
                $oArticle->SetAttachedArticle();
            }
            
            $this->aArticle[$oArticle->GetId()] = $oArticle;
            
        }
        
        return $this->aArticle;
    }
    
    public function GetSubSectionArticleDetails($iWebsiteId, $sSectionUri) {
        
        global $db;
        
        //if ((strlen($sSectionUri) < 1) || (!is_numeric($iWebsiteId))) return false;
        
        $sql = "select
				a.id,
				a.title,
				a.short_desc,
				m.section_uri
				from
				article_map m LEFT JOIN article a ON m.article_id = a.id
				where
				m.website_id = ".$iWebsiteId." AND
				m.section_uri like '".$sSectionUri."/%'";
        
        $db->query($sql);
        
        if ($db->getNumRows() < 1) return array();
        
        return $db->getRows();
    }
    
    
    public function LoadTemplate($sFilename,$iWebSiteId = null) {
        
        if (DEBUG) Logger::Msg(get_class($this)."::".__FUNCTION__."()");
        
        $this->oTemplate = new Template();
        
        $this->oTemplate->Set("ARTICLE_ARRAY",$this->aArticle);
        
        $this->oTemplate->LoadTemplate($sFilename);
        
    }
    
    
    
    public function Render() {
        
        return $this->oTemplate->Render();
        
    }
    
}


?>