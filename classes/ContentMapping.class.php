<?php 


class ContentMapping {
    
    private $oid;
    private $website_id; // @deprecated
    private $section_uri;
    private $article_id;
    
    private $opts_array; // array of bool values signal 2 template what content to display
    private $fetch_mode;  // article fetch scope
    private $template;
    
    public function __Construct($oid = null, $website_id = null, $section_uri = null) {
        
        $this->oid = $oid;
        $this->website_id = $website_id;
        $this->section_uri = $section_uri;
        
        $this->opts_array = array();
    }
    
    public function GetById() {
        
        global $db;
        
        $sql = "SELECT m.oid,m.website_id,m.section_uri FROM ".DB__ARTICLE_MAP_TBL." m WHERE oid = ".$this->GetId();
        
        $db->query($sql);
        
        if ($db->getNumRows() == 1) {
            $result = $db->getRow();
            $this->website_id = $result['website_id'];
            $this->section_uri = $result['section_uri'];
            $this->article_id = $result['article_id'];
            return TRUE;
        } else {
            return FALSE;
        }
    }
    
    public function GetByPath($strPath, $fuzzy = false) {
        
        global $db;
        
        if (strlen($strPath) < 1) throw new Exception("ERROR: Invalid Path");
        
        if ($fuzzy)
        {
            $operator = "LIKE ";
            $strPath = $strPath."%";
        } else {
            $operator = "= ";
        }
        
        $sql = "SELECT
                    m.oid
                    ,m.article_id
                    ,m.website_id
                    ,m.section_uri
                    ,o.* 
                FROM
                    ".DB__ARTICLE_MAP_TBL." m
                    LEFT OUTER JOIN ".DB__ARTICLE_MAP_OPTS." o ON m.oid = o.article_map_oid
                 WHERE
                    m.section_uri ".$operator." '".$strPath."'";
        
        $db->query($sql);
        
        if (!$fuzzy = false && $db->getNumRows() == 1) {
            $result = $db->getRow();
            $this->oid = $result['oid'];
            $this->article_id = $result['article_id'];
            $this->website_id = $result['website_id'];
            $this->section_uri = $result['section_uri'];

            $this->SetContentPubOpts($result);

            return TRUE;
        } else {
            throw new Exception("ERROR: No Content Mapping found for path: ".$strPath);
        }
        
        // @todo - handle fuzzy multiple matches
    }

    public function SetContentPubOpts($aRow)
    {

        $opts = array();
        $opts[ARTICLE_DISPLAY_OPT_PLACEMENT] = ($aRow['opt_placement'] == NULL) ? "t" : $aRow['opt_placement'];
        $opts[ARTICLE_DISPLAY_OPT_ARTICLE] = ($aRow['opt_article'] == NULL) ? "t" : $aRow['opt_article'];
        $opts[ARTICLE_DISPLAY_OPT_PROFILE] = ($aRow['opt_profile'] == NULL) ? "t" : $aRow['opt_profile'];
        $opts[ARTICLE_DISPLAY_OPT_REVIEW] = ($aRow['opt_review'] == NULL) ? "t" : $aRow['opt_review'];
        $opts[ARTICLE_DISPLAY_OPT_SOCIAL] = ($aRow['opt_social'] == NULL) ? "t" : $aRow['opt_social'];
        $opts[ARTICLE_DISPLAY_OPT_PARENT_TABS] = ($aRow['opt_ptab'] == 't') ? 't' : 'f';
        $opts[ARTICLE_DISPLAY_OPT_FEATURED_PROJECT] = ($aRow['opt_fproject'] == 't') ? 't' : 'f';
        $opts[ARTICLE_DISPLAY_OPT_ADS] = ($aRow['opt_ads'] == NULL) ? "t" : $aRow['opt_ads'];
        $opts[ARTICLE_DISPLAY_OPT_IMG] = ($aRow['opt_img'] == NULL) ? "t" : $aRow['opt_img'];
        $opts[ARTICLE_DISPLAY_OPT_BODY_TEXT_ALIGNMENT_HEADER] = ($aRow['opt_txtalignh'] == 't') ? 't' : 'f';
        $opts[ARTICLE_DISPLAY_OPT_BODY_TEXT_ALIGNMENT_BODY] = ($aRow['opt_txtalignb'] == 't') ? 't' : 'f';
        $opts[ARTICLE_DISPLAY_OPT_BODY_TEXT_ALIGNMENT_FOOTER] = ($aRow['opt_txtalignf'] == 't') ? 't' : 'f';
        $opts[ARTICLE_DISPLAY_OPT_ATTACHED] = ($aRow['opt_attached'] == 't') ? 't' : 'f';
        $opts[ARTICLE_DISPLAY_OPT_PATH] = ($aRow['opt_path'] == 't') ? 't' : 'f';
        $opts[ARTICLE_DISPLAY_OPT_SEARCH_KEYWORD] = stripslashes($aRow['search_keywords']);
        $opts[ARTICLE_DISPLAY_OPT_PTITLE] = stripslashes($aRow['p_title']);
        $opts[ARTICLE_DISPLAY_OPT_OTITLE] = stripslashes($aRow['o_title']);
        $opts[ARTICLE_DISPLAY_OPT_NTITLE] = stripslashes($aRow['n_title']);
        $opts[ARTICLE_DISPLAY_OPT_PINTRO] = stripslashes($aRow['p_intro']);
        $opts[ARTICLE_DISPLAY_OPT_OINTRO] = stripslashes($aRow['o_intro']);
        
        $opts[ARTICLE_DISPLAY_OPT_TEMPLATE_ID] = $aRow['template_id'];

        $this->SetOptionsFromArray($opts);
        
    }

    public function GetId() {
        return $this->oid;
    }

    public function GetArticleId() {
        return $this->article_id;
    }

    public function GetWebsiteId() {
        return $this->website_id;
    }

    public function GetSectionUri() {
        return $this->section_uri;
    }

    public function GetTemplateId() {
        return $this->opts_array[ARTICLE_DISPLAY_OPT_TEMPLATE_ID];
    }

    /*
     * @return mapping location as display label
     *
     */
    public function GetLabel() {

        return $this->GetSectionUri();
        
        
    }
    
    public function GetUrl() {
        global $oBrand;
        return $oBrand->GetWebsiteUrl().$this->GetLabel();
    }
    
    
    public function SetCacheUpdate() {

        global $oBrand;

        Cache::Generate($oBrand->GetWebsiteUrl(),$this->GetSectionUri(),$this->GetWebsiteId(),$sleep = false);
        
    }
    
    
    /* update content mapping url matching specified criterea */
    public static function UpdateUrl($url_from,$url_to) {
        
        global $db;
        
        if ((strlen($url_from) < 1) || (strlen($url_to) < 1)) return FALSE;
        
        $db->query("SELECT * from ".DB__ARTICLE_MAP_TBL." WHERE section_uri = '".$url_from."'");
        if ($db->getNumRows() >= 1) {
            $db->query("UPDATE ".DB__ARTICLE_MAP_TBL." SET section_uri = '".$url_to."' WHERE section_uri = '".$url_from."'");
            return TRUE;
        }
    }
    
    public function GetOptionEnabled($opt_id) {
        
        if (isset($this->opts_array[$opt_id])) {
            return ($this->opts_array[$opt_id] == "t") ? true : false;
        }
    }

    public function GetOptionById($opt_id) {
        
        if (isset($this->opts_array[$opt_id])) {
            return $this->opts_array[$opt_id];
        }
        
    }
    
    public function GetOptions() {
        return $this->opts_array;
    }
    
    public function SetOptionsFromArray($opts_array) {
        
        if (!is_array($opts_array)) return FALSE;
        
        $this->opts_array = $opts_array;
    }
    
    /* set signals to instruct template to toggle display of content */
    public function SetOptions($mid, $opts_array, $aTextFieldOpts) {
        
        if (!is_array($opts_array) || !is_numeric($mid)) return FALSE;
        
        global $db;
        
        $sql = "DELETE FROM ".DB__ARTICLE_MAP_OPTS." WHERE article_map_oid = ".$mid;
        
        $db->query($sql);

        $search_keywords = addslashes($aTextFieldOpts['search_keywords']);
        $p_title = addslashes($aTextFieldOpts['p_title']);
        $o_title = addslashes($aTextFieldOpts['o_title']);
        $n_title = addslashes($aTextFieldOpts['n_title']);
        $p_intro = addslashes($aTextFieldOpts['p_intro']);
        $o_intro = addslashes($aTextFieldOpts['o_intro']);
        
        $opts_array[ARTICLE_DISPLAY_OPT_PATH] = ($opts_array[ARTICLE_DISPLAY_OPT_PATH] == "t") ? "t" : "f";
        $opts_array[ARTICLE_DISPLAY_OPT_ATTACHED] = ($opts_array[ARTICLE_DISPLAY_OPT_ATTACHED] == "t") ? "t" : "f";
        
        
        $sql = "INSERT INTO ".DB__ARTICLE_MAP_OPTS." (	article_map_oid,
												opt_placement,
												opt_article,
                                                opt_profile,
                                                opt_review,
                                                opt_social,
												search_keywords,
												p_title,
												o_title,
												n_title,
												p_intro,
												o_intro,
                                                opt_ads,
                                                opt_img,
                                                template_id,
                                                opt_path,
                                                opt_attached
											 ) VALUES (
												".$mid.",
												'".$opts_array[ARTICLE_DISPLAY_OPT_PLACEMENT]."',
												'".$opts_array[ARTICLE_DISPLAY_OPT_ARTICLE]."',
                                                '".$opts_array[ARTICLE_DISPLAY_OPT_PROFILE]."',
                                                '".$opts_array[ARTICLE_DISPLAY_OPT_REVIEW]."',
                                                '".$opts_array[ARTICLE_DISPLAY_OPT_SOCIAL]."',
												'".$search_keywords."',
												'".$p_title."',
												'".$o_title."',
												'".$n_title."',
												'".$p_intro."',
												'".$o_intro."',
                                                '".$opts_array[ARTICLE_DISPLAY_OPT_ADS]."',
                                                '".$opts_array[ARTICLE_DISPLAY_OPT_IMG]."',
                                                ".$opts_array[ARTICLE_DISPLAY_OPT_TEMPLATE_ID].",
                                                '".$opts_array[ARTICLE_DISPLAY_OPT_PATH]."',
                                                '".$opts_array[ARTICLE_DISPLAY_OPT_ATTACHED]."'
											 );";
        

        $db->query($sql);
        
        if ($db->getAffectedRows() == 1)
        {
            return TRUE;
        }
    }

    public function GetDisplayOptProfile()
    {
        return $this->GetOptionById(ARTICLE_DISPLAY_OPT_PROFILE);
    }

    public function GetDisplayOptArticle()
    {
        return $this->GetOptionById(ARTICLE_DISPLAY_OPT_ARTICLE);
    }
    
    public function GetDisplayOptReview()
    {
        return $this->GetOptionById(ARTICLE_DISPLAY_OPT_REVIEW);
    }

    public function GetDisplayOptSocial()
    {
        return $this->GetOptionById(ARTICLE_DISPLAY_OPT_SOCIAL);
    }

    public function GetDisplayOptAds()
    {
        return $this->GetOptionById(ARTICLE_DISPLAY_OPT_ADS);
    }

    public function GetDisplayOptIntroImage()
    {
        return $this->GetOptionById(ARTICLE_DISPLAY_OPT_IMG);
    }
        
}



?>
