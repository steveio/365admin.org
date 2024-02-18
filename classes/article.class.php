<?php



define("CONTENT__ARTICLE",0);
define("CONTENT__SECTION",1);

define("CK_EDITOR_INTRO_DT","2012-07-06 10:00:00");

// content type flags to signal what to display in template
define("ARTICLE_DISPLAY_OPT_PLACEMENT",0);
define("ARTICLE_DISPLAY_OPT_ORG",1); // @ deprecated
define("ARTICLE_DISPLAY_OPT_ARTICLE",2);
define("ARTICLE_DISPLAY_OPT_PROFILE",14);
define("ARTICLE_DISPLAY_OPT_REVIEW",15);
define("ARTICLE_DISPLAY_OPT_SOCIAL",16);
define("ARTICLE_DISPLAY_OPT_ADS",17);
define("ARTICLE_DISPLAY_OPT_GADS",18);
define("ARTICLE_DISPLAY_OPT_IMG",19);

// keywords to drive search results
define("ARTICLE_DISPLAY_OPT_SEARCH_KEYWORD",3);
// user define titles for search results, news panels 
define("ARTICLE_DISPLAY_OPT_PTITLE",4);
define("ARTICLE_DISPLAY_OPT_OTITLE",5);
define("ARTICLE_DISPLAY_OPT_NTITLE",6);
define("ARTICLE_DISPLAY_OPT_PARENT_TABS",7);
define("ARTICLE_DISPLAY_OPT_PINTRO",8);
define("ARTICLE_DISPLAY_OPT_OINTRO",9);
// whether to show animated featured project
define("ARTICLE_DISPLAY_OPT_FEATURED_PROJECT",10);

// alignment of article body text { header | middle | footer }
define("ARTICLE_DISPLAY_OPT_BODY_TEXT_ALIGNMENT_HEADER",11);
define("ARTICLE_DISPLAY_OPT_BODY_TEXT_ALIGNMENT_BODY",12);
define("ARTICLE_DISPLAY_OPT_BODY_TEXT_ALIGNMENT_FOOTER",13);

define("ARTICLE_DISPLAY_OPT_TEMPLATE_ID",20);
define("ARTICLE_DISPLAY_OPT_TEMPLATE_PATH",23);
define("ARTICLE_DISPLAY_OPT_PATH",21);
define("ARTICLE_DISPLAY_OPT_ATTACHED",22);

// templates
define("ARTICLE_TEMPLATE_ARTICLE_DEFAULT",0);
define("ARTICLE_TEMPLATE_BLOG_DEFAULT",1);

define("ARTICLE_TEMPLATE_ARTICLE_FILE","article_01.php");
define("ARTICLE_TEMPLATE_BLOG_FILE","blog.php");
define("ARTICLE_TEMPLATE_BLOG_ARTICLE_FILE","blog_article.php");


define ("DB__ARTICLE_TBL","article");
define ("DB__ARTICLE_MAP_TBL","article_map"); /* locations where article is published to */
define ("DB__ARTICLE_PROFILE_MAP_TBL","article_profile_map"); /* profiles associated with article */
define ("DB__ARTICLE_LINK_TBL","article_link"); /* other articles that are associated with this article */
define ("DB__ARTICLE_MAP_OPTS","article_map_opts");


$_CONFIG['site_id'] = 0; // @deprecated multi-website white label config, now defaults to primary site


/*
 * Abstract Base class for all content elements eg. articles, sections
 * 
 * Presently articles and sections are quite similar so most functionality exists in base class
 * 
 * However, we are likely to introduce more divergent content types in future
 *
 * 
 */

class Content  implements TemplateInterface {

	private $type; /* type of content being represented eg article, section */
	private $sTypeLabel;

	private $id;
	private $title;
	private $short_desc;
	private $full_desc;
	private $meta_desc;
	private $meta_keywords;
	private $created_by;
	private $created_date;
	private $published_status;
	private $published_date;
	public  $last_updated;
	public  $last_indexed_solr;
	private $url;

	private $aMapping; /* locations to which this content has been published */
	private $aProfile; /* profiles (company || placements) that have been attached */
	public  $aImage; /* array of image objects that have been attached */ 
	private $aArticle; /* array of attached article id's */
	public 	$oArticleCollection; /* a collection of associated article objects */
	public  $oLinkGroup; /* a collection of links associated with article */

	public $fetch_mode; /* FETCHMODE__FULL || FETCHMODE__SUMMARY */
	public $fetchAttachedArticle = TRUE; 
	public $fetchAttachedProfile = TRUE; 
	public $fetchAttachedImage = TRUE; 
	public $fetch_child_mode; /* FETCHMODE__FULL || FETCHMODE__SUMMARY */
	public $fetch_current_mapping_only; /* fetch just mapping info associated with URL being viewed */
	public $fetch_mapped_profiles; // bool whether to fetch attached profiles
	private $iAttachedArticleFetchLimit;  // int how many attached articles to fetch
	
	private $oTemplate; /* a template instance used to render the content */

	private $bFetchAttachedTo; // bool whether to fetch associations with other articles
	private $aAttachedTo; // array details of articles this article is attached to
	
	public function __Construct() {
		
		$this->aMapping = array();
		$this->aProfile = array();
		$this->aImage = array();
		$this->aArticleId = array();
		$this->oArticleCollection = new ArticleCollection();
		$this->oLinkGroup = new LinkGroup();
		$this->SetFetchMode(FETCHMODE__FULL);
		$this->fetch_child_mode = FETCHMODE__SUMMARY;
		$this->fetch_current_mapping_only = FALSE;
		$this->bFetchAttachedTo = FALSE;
		$this->aAttachedTo = array();
	}

	public function SetFetchMode($mode) {
		$this->fetch_mode = $mode;
	}

	public function GetFetchMode() {
		return $this->fetch_mode;
	}
	
	public function SetChildFetchMode($mode) {
		$this->fetch_child_mode = $mode;
	}
	
	public function GetChildFetchMode() {
		return $this->fetch_child_mode;
	}
	
	public function GetFetchAttachedTo() {
		return $this->bFetchAttachedTo;
	}
	
	public function SetFetchAttachedArticle($bool) {
		$this->fetchAttachedArticle = $bool;
	}

	public function GetFetchAttachedArticle() {
		return $this->fetchAttachedArticle;	
	}

	public function SetFetchAttachedProfile($bool) {
	    $this->fetchAttachedProfile = $bool;
	}
	
	public function GetFetchAttachedProfile() {
	    return $this->fetchAttachedProfile;
	}

	public function SetFetchAttachedImage($bool) {
	    $this->fetchAttachedImage = $bool;
	}
	
	public function GetFetchAttachedImage() {
	    return $this->fetchAttachedImage;
	}

	public function SetFetchProfiles($bool) { 
		$this->fetch_mapped_profiles = $bool;
	}
	
	public function GetFetchProfiles() {
		return $this->fetch_mapped_profiles;
	}
	
	public function GetFetchCurrentMappingOnly() { 
		return $this->fetch_current_mapping_only;
	}
	
	public function SetFetchCurrentMappingOnly($bool) {
		$this->fetch_current_mapping_only = $bool;
	}
	
	public function SetFetchAttachedTo($bVal) {
		$this->bFetchAttachedTo = $bVal;
	}

	public function CountArticleCollection() {
		return $this->oArticleCollection->Count();
	}
	
	
	public function GetArticleCollection() {
		return $this->oArticleCollection;
	}
	
	public function GetType() {
		return $this->iType;
	}	

	public function SetType($iType) {
		$this->iType = $iType;
		if (strlen($this->GetTypeLabel()) < 1) {
			switch($this->iType) {
				case CONTENT__ARTICLE :
					$this->SetTypeLabel("Article");
					break;
				case CONTENT__SECTION :
					$this->SetTypeLabel("Section");
					break;
			}
		}
	}
	
	public function GetTypeLabel() {
		return $this->sTypeLabel;
	}	

	public function SetTypeLabel($sTypeLabel) {
		$this->sTypeLabel = $sTypeLabel;
	}
	
	public function GetId() {
		return $this->id;
	}	

	public function SetId($id) {
		$this->id = $id;
	}

	public function GetTitle() {
		return $this->title;
	}
	
	public function SetTitle($sTitle) {
		$this->title = $sTitle;
	}

	public function GetMetaDesc() {
		return $this->meta_desc;
	}
	
	public function SetMetaDesc($sMetaDesc) {
		$this->meta_desc = $sMetaDesc;
	}
	
	public function GetMetaKeywords() {
		return $this->meta_keywords;
	}
	
	public function SetMetaKeywords($sMetaKeywords) {
		$this->meta_keywords = $sMetaKeywords;
	}
	
  	public function GetDescShort($trunc = 0) {
  		
  		if ($trunc >= 1) {
  			$s = $this->short_desc;
			if (strlen($s) > $trunc) {
				$s = $s." ";
				$s = substr($s,0,$trunc);
				$s = substr($s,0,strrpos($s,' '));
				$s = $s."...";
				$s = strip_tags($s); // in case we left an open <b> tag
			}
			return $s;	
  		} else {
 			return $this->short_desc;
  		}
  	}
	
	public function SetDescShort($sDesc) {
		$this->short_desc = $sDesc;
	}
	
	public function GetDescFull() {
		return $this->full_desc;
	}

	public function SetDescFull($sDesc) {
		$this->full_desc = $sDesc;
	}


        public function GetImgSize() {
                return $this->img_size;
        }

	
        public function SetImgSize($size) {
                $this->img_size = $size;
        }       

        public function GetImgDisplay() {
                return $this->img_display;
        }

        public function SetImgDisplay($flag) {
                $this->img_display = $flag;
        }

        public function GetImgAlign() {
                return $this->img_align;
        }
 
        public function SetImgAlign($align) {
                $this->img_align = $align;
        }

	
	public function GetCreatedBy() {
		return $this->created_by;
	}
	
	public function GetCreatedDate() {
		return $this->created_date;
	}
	
	public function GetPublishedStatus() {
		return $this->published_status;
	}

	public function GetPublishedStatusLabel() {
		
		switch($this->GetPublishedStatus()) {
			case 0 :
				return "DRAFT";
				break;
			case 1 :
				return "PUBLISHED"; 
				break;
		}
	}
	
	public function GetPublishedDate() {
		return $this->published_date;
	}

	public function GetLastUpdated() {
		return $this->last_updated;
	}
	
	public function GetLastIndexedSolr() {
		return $this->last_indexed_solr;
	}
	

	/*
	 * Return relative to the website being viewed
	 * 
	 */
	public function GetUrl() {
		
		if (strlen($this->url) < 1) $this->SetUrl();
		
		return $this->url;				
	}
	
	public function SetUrl() {
		
		global $oBrand;

		if (!is_object($oBrand)) return "";

		/*
		 * if the article is unpublished
		 * or not published to the site being viewed
		 * return a default URL for viewing
		*/
		$defaultUrl = "/article?&id=".$this->GetId();
		
		$aMapping = $this->GetMappingBySiteId($oBrand->GetSiteId());
	
		if (count($aMapping) < 1) {
		    return $this->url = $defaultUrl;
		}

		$oMapping = $aMapping[0]; /* pick the first publish mapping associated with the site being viewed */ 
		
		if (!is_object($oMapping)) {
		    return $this->url = $defaultUrl;
		} else {
		  $this->url = $oMapping->GetUrl();
		}
		
		$this->section_uri = $oMapping->GetSectionUri();
		
		return $this->url;
	}

	public function GetRelativeUrl()
	{
	    return $this->section_uri;
	}

	public function GetSectionUri() {
	    return $this->section_uri;
	}
	
	public function GetAll($aFilter = array(),$fields = '',$fetch = TRUE) {

		if (DEBUG) Logger::Msg(get_class($this)."::".__FUNCTION__."()");

		global $db;
		
		$sFrom = "";
		$sWhere = "";
		
		if (strlen($fields) < 1) {
			$fields = "	a.*
						,m.website_id
						,to_char(a.created_date,'DD/MM/YYYY') as created_date
						,to_char(a.last_updated,'DD/MM/YYYY') as last_updated
						,to_char(a.last_indexed_solr,'DD/MM/YYYY') as last_indexed_solr
						,to_char(a.published_date,'DD/MM/YYYY') as published_date
					";
		}
		
		
		if (count($aFilter) >= 1) {
			foreach($aFilter as $k => $v) {
				switch($k) {
					case "URI" :
						$sFrom .= " ,".DB__ARTICLE_MAP_TBL." m";
						$sWhere .= " AND a.id = m.article_id AND m.section_uri LIKE '".$v."%'"; 
						break;
					case "WEBSITE_ID" :
						$sWhere .= " AND m.website_id = ".$v;
						break;
					case "LAST_INDEXED" :
						$sWhere .= " AND a.last_updated > a.last_indexed_solr ";
						break;
				}
			}
		}
		
		$sql = "SELECT
						$fields 
					FROM 
						".DB__ARTICLE_TBL." a
						".$sFrom."
					WHERE
						1=1 
						".$sWhere."
					ORDER BY 
						a.title ASC;";

		$db->query($sql);
		
		if ($db->getNumRows() < 1) return false;
		
		$aRes = $db->getRows();

		if (!$fetch) return $aRes;
		
		$aArticle = array(); 
		
		foreach($aRes as $a) {
			$oArticle = new Article();	
			$oArticle->SetFromArray($a);
			$oArticle->SetMapping();
			$aArticle[$oArticle->GetId()] = $oArticle;
		}
				
		return $aArticle;

	}
	
	
	/*
	 * Retrieve content by id
	 *  
	 * @param int website id
	 * @param string website section uri (eg /continent/africa, /country/brazil/volunteer) 
	 * @return mixed content object or false if not found
	 */
	public function GetById($id) {
		
		if (DEBUG) Logger::Msg(get_class($this)."::".__FUNCTION__."() id=".$id);

		global $db;
		
		if (!is_numeric($id)) return false;
	
		$field_sql = ($this->GetFetchMode() == FETCHMODE__FULL) ? "a.*" : "a.id, a.title, a.short_desc, a.meta_desc, a.meta_keywords, a.published_status, a.published_date";	

        $sql = "SELECT 
						$field_sql  
						,to_char(a.created_date,'DD/MM/YYYY') as created_date
						,to_char(a.last_updated,'DD/MM/YYYY') as last_updated
						,to_char(a.last_indexed_solr,'DD/MM/YYYY') as last_indexed_solr
						,to_char(a.published_date,'DD/MM/YYYY') as published_date
					FROM 
						".DB__ARTICLE_TBL." a 
					WHERE
						a.id = ".$id." 
					";

		$db->query($sql);
		
		if ($db->getNumRows() != 1) return false;
		
		$aRes = $db->getRow();

		$this->SetFromArray($aRes);
		
		/* get associated publish mappings, attached profiles, attached images */ 
		$this->SetMapping();
		$this->SetAttachedImage();
		if ($this->GetFetchMode() == FETCHMODE__FULL) {
			if ($this->GetFetchAttachedProfile()) {
				$this->SetAttachedProfile();
			}
			if ($this->GetFetchAttachedArticle()) {
				$this->SetAttachedArticle();
			}
			if ($this->GetFetchAttachedTo()) {
				$this->SetAttachedTo();
			}
		}
		
		return true;
		
	}
	
	public function SetAttachedTo() {
		
		
		global $db;
		
		// POstgres array_agg not available in 7.3
		// array_to_string(array_agg(published_url), '<br />') as published_to
		
		$sql = "
					select 
						id,
						title,
						published_url
					from (
							select 
								l.a1 as id, 
								a.title, 
								w.name||m.section_uri as published_url 
							from 
								article_link l, 
								article a, 
								article_map m, 
								website w 
							where 
								l.a2 = ".$this->GetId()." and 
								l.a1 = a.id and 
								a.id = m.article_id and 
								m.website_id = w.id 
							order by 
								l.a1 asc
						) as q1 
					group by id,title, published_url order by id asc;
		";
		
		$db->query($sql);
		
		if ($db->getNumRows() < 1) return false;

		$result = $db->getRows();
		
		//Logger::Msg($result);
		
		$output = array();
				
		foreach($result as $row) {

			if (!array_key_exists($row['id'], $output)) { 
				$output[$row['id']] = $row;
			} else {
				$output[$row['id']]['published_url'] .= "<br />".$row['published_url'];
			}
				
		}
		
		$this->aAttachedTo = $output;

	}
	
	public function GetAttachedTo() {
		return $this->aAttachedTo;
	}
	
	
	/*
	 * Retrieve content objects associated with a website section
	 *  
	 * @param int website id
	 * @param string website section uri (eg /continent/africa, /country/brazil/volunteer) 
	 * @return mixed content object or false if not found
	 */
	public function Get($iWebsiteId,$sSectionUri,$iLimit = -1,$exact = true) {
		
		global $db;
		
		if (!is_numeric($iWebsiteId)) return false;
		if (strlen($sSectionUri) < 1) return false;
	
		$sLimit = ($iLimit >= 1) ? "LIMIT ".$iLimit : "";
	
		if (!$exact) {
			$match = " LIKE ";
			$wildcard = "%";
		} else {
			$match = " = ";
			$wildcard = "";
		}

		$field_sql = ($this->GetFetchMode() == FETCHMODE__FULL) ? "a.*" : "a.id, a.title, a.short_desc, a.published_status, m.section_uri ";

		$sql = "SELECT 
						$field_sql 
						,to_char(a.published_date,'DD/MM/YYYY') as published_date
						,to_char(a.created_date,'DD/MM/YYYY') as created_date
						,to_char(a.last_updated,'DD/MM/YYYY') as last_updated
						,to_char(a.last_indexed_solr,'DD/MM/YYYY') as last_indexed_solr
					FROM 
						".DB__ARTICLE_TBL." a
						,".DB__ARTICLE_MAP_TBL." m 
					WHERE 
						m.website_id = ".$iWebsiteId."
						AND m.section_uri ".$match." '".$sSectionUri."".$wildcard."'
						AND m.article_id = a.id
						ORDER BY a.published_date DESC
						".$sLimit."
						;
					";

		$db->query($sql);

		if ($exact) // exact path, shouuld return single article
		{
		    if ($db->getNumRows() != 1) throw new Exception("ERROR: fetch article failed");
		
    		$aRes = $db->getRow(PGSQL_ASSOC);
    
    		$this->SetFromArray($aRes);
    		
    		/* get associated publish mappings, attached profiles, attached images */ 
    		$this->SetMapping($sSectionUri);

    		if($this->GetFetchAttachedProfile())
    		{
    		    $this->SetAttachedProfile();
    		}
    		if($this->GetFetchAttachedImage())
    		{
    		    $this->SetAttachedImage();
    		}
    		if($this->GetFetchAttachedArticle())
    		{
    		    $this->SetAttachedArticle();
    		}
		} else { // fuzzy path search, multiple results

		    $aResult = $db->getRows(PGSQL_ASSOC);

		    if (!is_array($aResult) || count($aResult) < 1)  throw new Exception("ERROR: fetch article failed");
		    
		    foreach($aResult as $aRow)
		    {
		        $oArticle = new Article();
		        $oArticle->SetFromArray($aRow);
		        $oArticle->SetAttachedImage();

		        $this->oArticleCollection->Add($oArticle);
		    }
		    
		}
		return true;
		
	}

	
	
	public function GetMappingLabel() {
		
		$s = "<ul>";
		
		foreach($this->aMapping as $oMapping) {
			$s .= "<li><a class='p_small' href='http://www.".$oMapping->GetLabel()."' title='View ".$this->GetTypeLabel()."' target='_new'> " .$oMapping->GetLabel()."</a></li>";
		}
		
		$s .= "<ul>";
		
		return $s;
		
	}
	
	public function GetMappingBySiteId($iSiteId) {
	
		$a = array();
		
		foreach($this->aMapping as $oMapping) {
			if ($oMapping->GetWebsiteId() == $iSiteId) $a[] = $oMapping;
		}
		
		return $a;
	}
	
	public function GetMappingBySectionUri($section_uri) {

		foreach($this->aMapping as $oMapping) {
			if ($oMapping->GetSectionUri() == $section_uri) return $oMapping;
		}
		
	}
	
	public function GetMapping() {
		return $this->aMapping;
	}

	public function SetMapping($section_uri = "") {

		global $db; 
		
		if ($this->GetFetchCurrentMappingOnly() && strlen($section_uri) > 1) {
			$sWhere = " AND m.section_uri = '".$section_uri."'";
		}

		$sql = "SELECT 
						m.oid
						,m.website_id
						,m.section_uri
						,o.* 
					FROM 
						".DB__ARTICLE_MAP_TBL." m  
						LEFT OUTER JOIN ".DB__ARTICLE_MAP_OPTS." o ON m.oid = o.article_map_oid  
					WHERE 
						article_id = ".$this->GetId() ."
						$sWhere
						";

		$db->query($sql);

		if ($db->getNumRows() < 1) return false;
		
		$aRes = $db->getRows();

		foreach($aRes as $aRow) {

			$oContentMapping = new ContentMapping($aRow['oid'],$aRow['website_id'],$aRow['section_uri']);
			$oContentMapping->SetContentPubOpts($aRow);
			$this->aMapping[] = $oContentMapping; 
		}
		
		//Logger::Msg($this->aMapping);
	}
	
	
	public function GetNextArticleSeq() {

		if (DEBUG) Logger::Msg(get_class($this)."::".__FUNCTION__."()");
		
		global $db;
		
		return $db->getFirstCell("SELECT nextval('article_seq')");
	}
	
	
	public static function convertCkEditorFont2Html($text,$title) {
	    //$text = preg_replace('/(?<=<div.*?)(?<!=\t*?"?\t*?)(class|style)=".*?"/', "<table>$1</table>", $text);
	    $text = preg_replace('/<p>[ \t\r\n]+<span style="font-size:[ ]?([20|22|24].*?)".*?>(.*?)<\/span><\/p>/si', '<'.$title.'>${2}</'.$title.'>', $text);
	    $text = preg_replace('/<span style="font-size:[ ]?([20|22|24].*?)".*?>(.*?)<\/span>/si', '<'.$title.'>${2}</'.$title.'>', $text);
	    $text = preg_replace('/<span style="font-size:[ ]?(14.*?)".*?>(.*?)<\/span>/si', '${2}', $text);
	    return $text = preg_replace('/<table .*?>/si', '<table>', $text);
	    
	    //return preg_replace('/\<[\/]?(table)([^\>]*)\>/i', '', $text);
	    
	}

	
	/*
	 * INSERT / UPDATE Article
	 * 
	 * Acts as a wrapper for Add() / Update()
	 * 
	 */
	public function Save(&$response) {

		if (!$this->Validate($response)) return false;
		
		if (!is_numeric($this->GetId())) {
			$this->SetId($this->GetNextArticleSeq());
			
			if(!$this->Add($response)) return false;
		} else {
			if (!$this->Update($response)) return false;
		}
	
		/* is the article published? optionally trigger cache update */
		$this->SetMapping();
		$aMapping = $this->GetMapping();
		if (count($aMapping) < 1) return true; /* not published, we are done */ 
		
		foreach($aMapping as $oMapping) {
			$oMapping->SetCacheUpdate(); /* re-generate the cached version of the page */
		}			
		
		return true;
	}
	
	
	public function Validate(&$aResponse) {

		if (DEBUG) Logger::Msg(get_class($this)."::".__FUNCTION__."()");

		if (strlen($this->GetTitle()) < 1) {
			$aResponse['msg'] = "ERROR: Title must be supplied";
		}
		
		if (strlen($this->GetTitle()) > 255) {
			$aResponse['msg'] = "ERROR: Title must be less than 254 characters";
		}

		// disabled to allow creation of unpublished article "stubs"
		//if (strlen($this->GetDescShort()) < 1) {
		//	$aResponse['desc_short'] = "Short Description must be supplied";
		//}
		
		if (strlen($this->GetDescShort()) > 1999) {
			$aResponse['msg'] = "ERROR: Short Description must be less than 254 characters";
		}
		
		
		if (count($aResponse) >= 1) return false;
		
		return true;
	}

	
	public function Add(&$response) {

		if (DEBUG) Logger::Msg(get_class($this)."::".__FUNCTION__."()");

		global $db;
		
		$sql = "INSERT INTO ".DB__ARTICLE_TBL." (
									id
									,title
									,short_desc
									,full_desc
									,meta_desc
									,meta_keywords
									,created_by
									,created_date
									,last_updated
									,last_indexed_solr
									,published_status
									,published_date
								) VALUES (
									".$this->GetId()."
									,'".pg_escape_string($this->GetTitle())."'
									,'".pg_escape_string($this->GetDescShort())."'
									,'".pg_escape_string($this->GetDescFull())."'
									,'".pg_escape_string($this->GetMetaDesc())."'
									,'".pg_escape_string($this->GetMetaKeywords())."'
									,".$this->GetCreatedBy()."
									,now()::timestamp
									,now()::timestamp
									,now() - interval '1 hour'
									,".$this->GetPublishedStatus()."
									,now()::timestamp
								);";

		$db->query($sql);
		
		if (!$db->getAffectedRows() == 1) {
		    print $sql;
			$response['save_error'] = "There was a problem adding the ".$this->GetTypeLabel().".";
			return false;
		}
		
		return true;
		
	}

	
	public function  Update(&$response) {

		if (DEBUG) Logger::Msg(get_class($this)."::".__FUNCTION__."()");

		global $db;

		$sql = "UPDATE ".DB__ARTICLE_TBL."
                                                SET
                                                        title = '".pg_escape_string($this->GetTitle())."'
                                                        ,short_desc = '".pg_escape_string($this->GetDescShort())."'
                                                        ,full_desc = '".pg_escape_string($this->GetDescFull())."'
                                                        ,meta_desc = '".pg_escape_string($this->GetMetaDesc())."'
                                                        ,meta_keywords = '".pg_escape_string($this->GetMetaKeywords())."'
                                                        ,last_updated = now()::timestamp
                                                        ,last_indexed_solr = now() - interval '1 hour'
                                                WHERE id = ".$this->GetId().";
                                        ";

		
		$db->query($sql);
 
		if (!$db->getAffectedRows() == 1) {
			$response['save_error'] = "There was a problem adding the ".$this->GetTypeLabel().".";
			return false;
		}
		
		return true;
	}

	public function GetAttachedImage() {
		return $this->aImage;	
	}

	public function SetAttachedImage() {	
		$this->SetAttachedImages();
	}
	
	public function AddAttachedImage($oImage) {
		$this->aImage[] = $oImage;
	}
	
	public function GetAttachedProfile() {
		return $this->aProfile;
	}
	
	public function AddAttachedProfile($oProfile) {
		$this->aProfile[] = $oProfile;
	}

	// allow caller to inject their own list of profiles, overriding any the publisher added
	public function SetAttachedProfileFromArray($aProfile) {
		$this->aProfile = $aProfile;
	}
	
	public function SetAttachedProfile($fetch = TRUE,$aRes = array()) {
		
		if (DEBUG) Logger::Msg(get_class($this)."::".__FUNCTION__."()");

		global $db;
		
		if ($fetch) {
			$db->query("SELECT m.profile_type, m.profile_id FROM ".DB__ARTICLE_PROFILE_MAP_TBL." m WHERE m.article_id = ".$this->GetId());
			
			if ($db->GetNumRows() < 1) return false;
			
			$aRes = $db->GetRows();
		}
		
		foreach($aRes as $aRow) {
			$oProfile = ProfileFactory::Get($aRow['profile_type']);
			try {
				$aProfile = $oProfile->GetById($aRow['profile_id']);
			} catch (Exception $e) {
				// @todo comp profile deleted, remove this profile mapping
				continue;
			}
			if (!$aProfile) continue;
			$oProfile->SetFromArray($aProfile);
			$oProfile->GetImages($iType = PROFILE_IMAGE);
			if (in_array($aRow['profile_type'], array(PROFILE_PLACEMENT, PROFILE_VOLUNTEER))) {
				$oProfile->SetCompanyLogo();
			}
			$this->aProfile[] = $oProfile; 
		}
		
		return true;
		
	}
	
	
	public function RemoveProfile($aRequest,&$response) {
	
		if (DEBUG) Logger::Msg(get_class($this)."::".__FUNCTION__."()");

		global $db;

		//Logger::Msg("remove_profile");
		//Logger::Msg($aRequest);

		if (!is_numeric($aRequest['profile_id'])) {
			$response['website_id'] = "ERROR : You must select a profile to remove ";
			return false;
		}

		$type = ($aRequest['profile_type'] == 0) ? 0 : 1; /* 0=company profile, 1=placement */
	
		//print $sql = "DELETE FROM ".DB__ARTICLE_PROFILE_MAP_TBL." WHERE article_id = ".$aRequest['id']." AND profile_type=".$type." AND profile_id=".$aRequest['profile_id'];	
		
		$db->query("DELETE FROM ".DB__ARTICLE_PROFILE_MAP_TBL." WHERE article_id = ".$aRequest['id']." AND profile_type=".$type." AND profile_id=".$aRequest['profile_id']);
		
		return true;
		
	}
	
	
	public function AttachProfile($aRequest,&$response) {
		
		if (DEBUG) Logger::Msg(get_class($this)."::".__FUNCTION__."()");

		global $db;

		$aRequest['profile_type'] = PROFILE_COMPANY;
		$aRequest['profile_id'] = $aRequest['company_id'];
		
		if (!is_numeric($aRequest['company_id'])) {
			$response['profle_id'] = "ERROR : Please select a valid profile to attach";
			return false;
		}
		
		if (is_numeric($aRequest['placement_id'])) {
			$aRequest['profile_type'] = PROFILE_PLACEMENT;
			$aRequest['profile_id'] = $aRequest['placement_id'];
		}
		
		$db->query("SELECT 
						article_id 
					FROM 
						".DB__ARTICLE_PROFILE_MAP_TBL." 
					WHERE 
						article_id = ".$this->GetId()." 
						AND profile_type = ".$aRequest['profile_type']."
						AND profile_id = ".$aRequest['profile_id']."
					");
		
		if ($db->getNumRows() == 1) {
			$response['duplicate_check'] = "ERROR : This profile is already attached";
			return false;
		}

		//$aRequest['placement_id']
		
		$db->query("INSERT INTO ".DB__ARTICLE_PROFILE_MAP_TBL." 
						(article_id,profile_type,profile_id) 
					VALUES 
						(".$this->GetId().",".$aRequest['profile_type'].",".$aRequest['profile_id'].");");
		
		if ($db->getAffectedRows() != 1) {
			$response['attach_err'] = "ERROR : A problem occured and profile was not attached";
			return true;
			
		} else {
			$response['msg'] = "SUCCESS : Attached profile";
			return true;
		}
	}
	
	
	

	public function Publish($aRequest,&$response) {
			
	    global $aResponse;

		$iWebsiteId = 0; // default to oneworld365.org, no longer multisite
		$aDisplayOptions = Mapping::GetIdByKey($aRequest,"opt_");
		

		$sSectionUri = trim($aRequest['section_uri']);
		
		if (strlen(trim($sSectionUri)) < 1) {
		    $aResponse['msg'] = "ERROR : You must specify a valid relative uri (eg /activity/animals)";
		    return false;
		}

		if (!preg_match("/^[\/]{1}/",$sSectionUri)) $sSectionUri = "/".$sSectionUri;
				
		$aMapping = array();
		
		$aMapping[] = array($iWebsiteId => $sSectionUri);
				
		if (!$this->Map($aMapping,$bDeleteExisting = true,$response)) 
		{
		    return false;
		}

		
		/* update cached pages for all pages on which this article is being published */
		
		$this->SetMapping();
		$aWebsiteMapping = $this->GetMapping();
		
		if (count($aWebsiteMapping) < 1) return true; /* not published, we are done */ 
		
		foreach($aWebsiteMapping as $oMapping) {
			$oMapping->SetCacheUpdate(); /* re-generate the cached version of the page */
		}

		unset($this->aMapping);
		
		$aResponse['msg'] = "SUCCESS : Published article to ".implode(", ", $aMapping[0]);
		$aResponse['status'] = "success";

		return true;
	}
	
		
	/*
	 * Attach 1 or more articles to this article
	 * 
	 * Allows creating of "section" pages which contain collections of other articles
	 * 
	 */
	public function AttachArticleId($aId) {

		if (DEBUG) Logger::Msg(get_class($this)."::".__FUNCTION__."()");
		
		global $db;
		
		if ((!is_array($aId)) || (count($aId) <1)) return false;
		
		foreach($aId as $id) {
			
			if(!is_numeric($id) || ($id == $this->GetId())) continue;
						
			$db->query("SELECT 1 FROM ".DB__ARTICLE_LINK_TBL." WHERE a1 = ".$this->GetId()." AND a2 = ".$id);

			if ($db->GetNumRows() < 1) {
				$db->query("INSERT INTO ".DB__ARTICLE_LINK_TBL." (a1,a2) VALUES (".$this->GetId().",".$id.");");
			}
		}
		
		return true;		
	}

	
	public function RemoveAttachedArticle($aId) {

		if (DEBUG) Logger::Msg(get_class($this)."::".__FUNCTION__."()");
		
		global $db;
		
		if ((!is_array($aId)) || (count($aId) <1)) return false;

		$error = FALSE;
		
		foreach($aId as $id) {
			
			if(!is_numeric($id)) continue;
						
			$db->query("SELECT 1 FROM ".DB__ARTICLE_LINK_TBL." WHERE a1 = ".$this->GetId()." AND a2 = ".$id);

			if ($db->GetNumRows() == 1) {
				$db->query("DELETE FROM ".DB__ARTICLE_LINK_TBL." WHERE a1 = ".$this->GetId()." AND a2 = ".$id);
			} else {
				$error = TRUE;
			}
		}
		
		if (!$error) return true;
	}
	
	/*
	 * Get attached articles objects and add to article collection  
	 * 
	 */
	public function  SetAttachedArticle($fetch = TRUE) 
	{	
		global $db;

		if ($fetch) $this->SetAttachedArticleId();
		
		foreach($this->GetAttachedArticleId() as $id) {
			$oArticle = new Article();
			$oArticle->SetFetchMode($this->GetChildFetchMode());
			$oArticle->SetFetchAttachedArticle(FALSE);
			$oArticle->SetFetchAttachedProfile(FALSE);
			if ($oArticle->GetById($id)) {
				$this->oArticleCollection->Add($oArticle);
			}
			
		}
		
	}
	
	
	public function  SetAttachedArticleId() 
	{
		global $db;
		
		$limitSql = ($this->GetAttachedArticleFetchLimit() >= 1) ? " LIMIT ".$this->GetAttachedArticleFetchLimit() : "";   
				
		$db->query("SELECT m.a2 as id FROM ".DB__ARTICLE_LINK_TBL." m, ".DB__ARTICLE_TBL." a WHERE m.a1 = ".$this->GetId(). " AND m.a2 = a.id ORDER BY a.published_date DESC ". $limitSql);
	
		if ($db->GetNumRows() >= 1) {
			$result = $db->getRows();
			foreach($result as $row) {
				$this->aArticleId[] = $row['id'];
			}
		}

		$this->iAttachedArticleTotal = count($this->aArticleId);
	}

	public function GetAttachedArticleTotal()
	{
	    return $this->iAttachedArticleTotal;
	}

	public function SetAttachedArticleFetchLimit($iLimit) {
		$this->iAttachedArticleFetchLimit = $iLimit;
	}
	
	private function GetAttachedArticleFetchLimit() {
		return $this->iAttachedArticleFetchLimit;
	}

	public function GetAttachedArticleId() {
		return $this->aArticleId;
	}
	
	public function SetAttachedArticleIdFromArray($aId) {
		$this->aArticleId = $aId;
	}
	
	
	
	public function Delete() {

		if (DEBUG) Logger::Msg(get_class($this)."::".__FUNCTION__."()");
		
		global $db;
		
		if (!is_numeric($this->GetId())) return false;
		
		$this->MapDelete();
		
		$db->query("DELETE FROM ".DB__ARTICLE_TBL." WHERE id = ".$this->GetId());
		
		return true;
		
	}

	
	/*
	 * Associate content with a given website section
	 * 
	 * @todo - rules to be revised to enable 1..n mappings to uri's
	 * 
	 * 	Rules :
	 * 		article 1..n website
	 * 		article 1..n section
	 * 		section 1..1 article
	 * 
	 * @param array "website_id (int)" => "section uri (string)"
	 * @return bool true / false
	 * 
	 */
	public function Map($aMapping,$bDeleteExisting = true,&$aResponse) {

		global $db;

		if ($bDeleteExisting) {
			$this->MapDelete();
		}
		
		$bError = false;
		
		for($i=0;$i<count($aMapping); $i++) {
			foreach($aMapping[$i] as $iWebsiteId => $sSectionUri) {

			    if (!$this->MapExists($iWebsiteId,$sSectionUri)) {
					$db->query("INSERT INTO ".DB__ARTICLE_MAP_TBL." (article_id,website_id,section_uri) VALUES (".$this->GetId().",".$iWebsiteId.",'".$sSectionUri."');");
					
					if ($db->getAffectedRows() != 1)
					{
					   die("affected rows");
					   $bError = true;
					}
			    } else {
					$bError = true;
				}
			}
		}
		
		if ($bError) return false;
		
		return true;
		
	}

	public function MapExists($iWebsiteId = 0,$sSectionUri) {

		if (DEBUG) Logger::Msg(get_class($this)."::".__FUNCTION__."()");
		
		global $db, $aResponse;
		
		$db->query("SELECT a.id, a.title, m.section_uri FROM ".DB__ARTICLE_MAP_TBL." m, ".DB__ARTICLE_TBL." a WHERE m.article_id = a.id AND m.website_id = ".$iWebsiteId." AND m.section_uri = '".$sSectionUri."'");
		
		if ($db->getNumRows() >= 1) {
			$i = 0;
			$aRes = $db->getRows();
			
			$aResponse['msg'] .= "ERROR : ";
			foreach($aRes as $aRow) {
			    $aResponse['msg'] .= "Article <a href='/article_pub.php?&id=".$aRow['id']."' target=\'_new\' >".$aRow['title']." </a> ( ".$aRow['section_uri']." ) is already published to this location. <br />";
				$aResponse['status'] = "warning";
			}
			return true;
		}
	}
	
	public function MapDelete() {

		if (DEBUG) Logger::Msg(get_class($this)."::".__FUNCTION__."()");
		
		global $db;
		
		$db->query("DELETE FROM ".DB__ARTICLE_MAP_TBL." WHERE article_id = ".$this->GetId());
		
	}
	
	public function MapDeleteById($id) {

		if (DEBUG) Logger::Msg(get_class($this)."::".__FUNCTION__."()");
		
		global $db;
		
		$db->query("DELETE FROM ".DB__ARTICLE_MAP_TBL." WHERE oid = ".$id);
		
	}
		
	
	
	/*
	 * Santize input and escape non-db safe chars
         * @deprecated - results in double escaping on php 7
	 * 
	 */
	private function Sanitize() {

		if (DEBUG) Logger::Msg(get_class($this)."::".__FUNCTION__."()");

		foreach($this as $k => $v) {
			if (is_string($v)) {				
				//Validation::Sanitize($this->$k);
				//Validation::AddSlashes($this->$k);
			}
		}

	}
	

	/*
	 * Image Implementation
	 * 
	 * 
	 */
	public function SetAttachedImages($iType = PROFILE_IMAGE) {

		global $db,$_CONFIG;
		
		$db->query("SELECT i.*,m.type FROM image_map m, image i WHERE m.img_id = i.id AND m.link_to = 'ARTICLE' AND m.link_id = ".$this->GetId()." ORDER BY i.id ASC");

		if ($db->getNumRows() >= 1) {
			$aObj = $db->getObjects();
			foreach($aObj as $o) {
				$oImage = new Image($o->id,$o->type,$o->ext,$o->dimensions,$o->width,$o->height,$o->aspect);
				$this->SetImage($oImage);					
			}
		}

		//Logger::Msg($this->aImage);

	}

	private function SetImage($oImage) {
		$this->aImage[] = $oImage;
	}

	public function GetImage($idx = 0) {
		
		if (DEBUG) Logger::Msg(get_class($this)."::".__FUNCTION__."() img_id=".$idx);
		
		if (is_object($this->aImage[$idx])) {
			return $this->aImage[$idx];
		}
	}
	
	public function GetImages() {
		return $this->aImage;
	}
	
	public function GetImageCount() {
		return count($this->aImage);
	}
	
	public function RemoveAttachedImage($img_id) {
		
		global $db;

		if (!is_numeric($img_id)) return false;
		
		$db->query("SELECT 1 FROM image_map WHERE img_id = ".$img_id ." AND link_to = 'ARTICLE' AND link_id = '".$this->GetId()."'");
		
		if ($db->GetNumRows() == 1) {
			$db->query("DELETE FROM image_map WHERE img_id = ".$img_id ." AND link_to = 'ARTICLE' AND link_id = '".$this->GetId()."'");
		}
	}

	
	public function SetAttachedLink($template = "link_list_01.php") {
		
		global $_CONFIG;
		
		$this->oLinkGroup->GetByAssociation("ARTICLE",$this->GetId());
		$this->oLinkGroup->oTemplate->Set("LINK_TO_ID",$this->GetId());
		$this->oLinkGroup->oTemplate->Set("WEBSITE_URL",$_CONFIG['url']);
		$this->oLinkGroup->LoadTemplate($template);
		
	}
	
	public function GetAttachedLink() {
		return $this->oLinkGroup->GetItems();
	}
	
	
	/*
	 * Template Concrete Implementation
	 * 
	 * 
	 */
	public function initTemplate() {
	    $this->oTemplate = new Template();
	}

	
	public function LoadTemplate($sFilename,$aOptions = array()) {

		if (DEBUG) Logger::Msg(get_class($this)."::".__FUNCTION__."()");

		$this->oTemplate = new Template(); 
		
		$aDefaultOptions = array(
				"TITLE" => $this->GetTitle(),
				"DESC_SHORT" => htmlUtils::convertToPlainText($this->GetDescShort()),
		        "DESC_SHORT_160" => htmlUtils::convertToPlainText($this->GetDescShort(160)),
		        "FULL_DESC" => htmlUtils::convertToPlainText($this->GetDescFull()),
				"URL" => $this->GetUrl(),
				"PUBLISHED_DATE" => $this->GetPublishedDate(),
				"IMG_SIZE" => $this->GetImgSize(),
				"IMG_DISPLAY" => $this->GetImgDisplay(),
				"IMG_ALIGN" => $this->GetImgAlign(),
				"ARTICLE_OBJECT" => $this
		);
		
		$aOptions = array_merge($aOptions,$aDefaultOptions);
		
		$this->oTemplate->SetFromArray($aOptions);
		
		/*
		 * Render optional template features 
		 * 	eg.
		 * 		- Main Large Right-Aligned Featured Profile
		 * 		- Profile List
		 * 		- Images
		 * 		- Affiliate Code
		 * 
		 * 
		 */
				
		$this->oTemplate->LoadTemplate($sFilename);
		
	}
	
	public function Render() {

		return $this->oTemplate->Render();
		
	}

	
	/* @todo - migrate to a base methid */
	public function SetFromArray($a,$m = "GET", $escape_chars = TRUE) {
		
		if (DEBUG) Logger::Msg(get_class($this)."::".__FUNCTION__."() mode: ".$m);
		
		//print "SetFromArray() m=".$m.", escape_chars=".$escape_chars."<br />";
		
		foreach($a as $k => $v) {
			if ($escape_chars) {
				if ($m == "GET") {
					$this->$k = (is_string($v)) ? stripslashes($v) : $v;	
				} elseif ($m == "SET") {
					$this->$k = (is_string($v)) ? addslashes($v) : $v;
				}
			} else {
				$this->$k = $v;
			}
		}
	}	
	
}


class Article extends Content {
		
	public function __Construct() {
		
		$this->SetType(CONTENT__ARTICLE);
		$this->SetTypeLabel("Article");
		
		parent::__Construct();
		
	}
}



class Section extends Content {
		
	public function __Construct() {
		
		$this->SetType(CONTENT__SECTION);
		$this->SetTypeLabel("Section");
		
		parent::__Construct();
		
	}
}



?>
