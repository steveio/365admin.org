<?

class Category implements TemplateInterface {

	private $id;
	private $name;
	private $url_name;
	private $description;
	private $desc_short;
	private $img_url;
	private $sort_order;
	
	private $aCategory;

	public function __construct(&$db = NULL)
	{
		$this->_Category($db);
	}

	public function _Category(&$db = NULL) {
	
		if (DEBUG) Logger::Msg(get_class($this)."::".__FUNCTION__."()");
		
		$this->db = $db;
	}
	
	public function GetId() {
		return $this->id;
	}
	
	public function GetTitle() {
		return $this->name;
	}

	public function GetName() {
		return stripslashes($this->name);
	}
	
	public function GetUrlName() {
		return $this->url_name;
	}
	
	public function GetDescription() {
		return $this->description;
	}
	
	public function GetDescShort() {
		return stripslashes($this->desc_short);
	}
	
	public function GetImgUrl() {
		return $this->img_url;
	}
	
	public function GetUrl() {
		global $_CONFIG;
		return $_CONFIG['url']."/".$this->url_name;
	}
	
	
	public function GetAll() {
		
		if (DEBUG) Logger::Msg(get_class($this)."::".__FUNCTION__."()");
		
		global $db;
		
		$sql = "SELECT c.id,c.name,c.url_name FROM category c ORDER BY sort_order DESC;";
		$db->query($sql);
		return $db->getRows();
	}
	
	public function GetCategoriesByWebsite($iSiteId,$sReturn = "ROWS") {
		
		if (DEBUG) Logger::Msg(get_class($this)."::".__FUNCTION__."()");
		
		global $db;
	
		$sql = "SELECT 
					c.id
					,c.name
					,c.url_name
				FROM 
					category c
				ORDER by c.id asc";
	
		$db->query($sql);
		
		if ($sReturn == "ROWS") {
			$aResult = $db->getRows();
			foreach ($aResult as $aRow) {
				$aNew[$aRow['id']]['id'] =  $aRow['id'];
				$aNew[$aRow['id']]['name'] =  $aRow['name'];
				$aNew[$aRow['id']]['url_name'] =  $aRow['url_name'];
			}
			return $this->aCategory = $aNew;
		}
		if ($sReturn == "OBJECTS") {
			$this->SetCategory($this->Cast($db->getObjects()));
			return $this->GetCategory();  
		}
	}

	public function GetCategoriesById($id,$type) {
		
		if (DEBUG) Logger::Msg(get_class($this)."::".__FUNCTION__."()");
		
		global $db;
		
		if ($type == "company_id") {
			$sql = "SELECT a.id,a.name FROM comp_cat_map c, category a WHERE c.company_id = ".$id." AND c.category_id=a.id ";
		} elseif ($type == "placement_id") {
			$sql = "SELECT a.id,a.name FROM prod_cat_map c, category a WHERE c.prod_id = ".$id." AND c.category_id=a.id ";
		}
		$db->query($sql);
		return $db->getRows();
	}


	public function GetCategoryLinkList($mode = "post",$aSelected = array(),$slash = true,$all =false) {
		
		if (DEBUG) Logger::Msg(get_class($this)."::".__FUNCTION__."()");
		
		global $_CONFIG;
		
		if ($all) {
			$aCategories = $this->GetAll();
		} else {
			$aCategories = $this->GetCategoriesByWebsite($_CONFIG['site_id']);
		}
		
		$idx = 0;
		unset($ct_text);
		$ct_text = '';
		// get category text links
		foreach($aCategories as $c) {
			
			if ($slash == true) {
				$delimeter = ($idx < count($aCategories) -1) ? " / " : " " ;
			} else {
				$delimeter = '';
			}
			if ($mode == "input") {
				$checked = (in_array($c['id'],$aSelected)) ? "checked" : "";
				$ct_text .= "<li class='select_list'><input class='inputCheckBox' type='checkbox' name='cat_".$c['id']."' $checked /> ".$c['name']." ".$delimeter ."</li>";
			} else {
				$checked = ($_POST['cat_'.$c['id']] == "on") ? "checked" : "";
				$ct_text .= $c['name'];
			}
			
			$idx++;
		}
		return $ct_text;
	}

	public function GetSelected($link_to,$link_id) {
		
		if (DEBUG) Logger::Msg(get_class($this)."::".__FUNCTION__."()");
		
		global $_CONFIG, $db;
		
		switch ($link_to) {
			case "website" : 
				$tbl = "website_category_map";	
				$key = "website_id";
		}
		
		$sql = "SELECT c.id FROM category c,".$tbl." m WHERE m.".$key." = ".$link_id." AND m.category_id = c.id ORDER BY c.name ASC;";
		$db->query($sql);
		$aResult = $db->getRows();
		$aRes = array();
		for($i=0;$i<count($aResult);$i++) {
			$aRes[] = (int) $aResult[$i]['id'];
		}
		return $aRes;
	}
	
	// return comma seperated keywords for all categories in id list
	public function GetMetaKeywords($aCategoryId) {
		
		$aCategory = $this->GetByIdArray($aCategoryId);

		$idx = 1;
		foreach($aCategory as $oCategory) {
			$comma = ($idx++ == count($aCategoryId) -1) ? "," : ""; 
			$keyword_str .= preg_replace("/[-\/]/",",",$oCategory->GetDescription()) .$comma;
		}
		
		return $keyword_str;
		
		// randomize the keywords to add uniqueness
		//$a = explode(",",$keyword_str);
		//shuffle($a);
		//return $keyword_str_sorted = implode(",",$a);
		
	}

	
	public function GetByIdArray($aId) {
		
		if (!is_array($aId)) return FALSE;
		
		$a = array();
		
		foreach($aId as $id) {
			$o = new Category();
			$o->SetFromObject($this->GetById($id));
			$a[$id] = $o;
		}
		
		return $a;
	}
	
	
	public function GetById($iId) {
		
		if (DEBUG) Logger::Msg(get_class($this)."::".__FUNCTION__."()");
		
		global $db;
		
		if (!is_numeric($iId)) return false;
		
		$db->query("SELECT id,name,url_name FROM category WHERE id = ".$iId.";");
		
		$oRes = $db->getObject();
		$oRes->name = stripslashes($oRes->name);
		$oRes->description = stripslashes($oRes->description);
		
		return $oRes;
				
	}
	
	public function Update($iId,$sName,$sUrlName,$sDesc,$sImgUrl) {

		if (DEBUG) Logger::Msg(get_class($this)."::".__FUNCTION__."()");
		
		global $db;		
		
		if (!is_numeric($iId)) return false;
		
		/* activity exists */
		$iExistingId = $db->getFirstCell("SELECT 1 FROM category WHERE id = ".$iId.";");		
		if (!is_numeric($iExistingId)) return false;
		
		/* update url_name */ 
		$sExistingName = $db->getFirstCell("SELECT name FROM category WHERE id = ".$iId.";");
		if ($sName != stripslashes($sExistingName)) { /* generate a new unique url namespace identifier */
			$oNs = new NameService();
			$sUrlName = $oNs->GetUrlName($sName,'category','name');			
		}
		
		$sSql = "UPDATE category SET name = '".addslashes($sName)."',url_name='".$sUrlName."' WHERE id = '".$iId."'";
		
		return $db->query($sSql);


	}
	
	public function Add($sName,$sDescription) {
		
		if (DEBUG) Logger::Msg(get_class($this)."::".__FUNCTION__."()");
		
		global $db;
		
		$iId = $db->getFirstCell("SELECT max(id)+1 FROM activity;");
				
		// generate unique url namespace identifier
		$oNs = new NameService();
		$sUrlName = $oNs->GetUrlName($sName,'category','name');
				
		return $db->query("INSERT INTO category (id,name,url_name) VALUES (".$iId.",'".addslashes(ucfirst(strtolower($sName)))."','".$sUrlName."');");
			
	}

	public function Delete($iId) {
		
		if (DEBUG) Logger::Msg(get_class($this)."::".__FUNCTION__."()");
		
		if (!is_numeric($iId)) return false;
		
		global $db;
		
		$db->query("DELETE FROM website_category_map WHERE activity_id = ".$iId);
		$db->query("DELETE FROM comp_cat_map WHERE activity_id = ".$iId);
		$db->query("DELETE FROM prod_cat_map WHERE activity_id = ".$iId);
		$db->query("DELETE FROM category WHERE id = ".$iId);
		
		return true;
		
	}
	
	public function GetDDList($sName = "category_id",$iSelectedValue = null) {

		if (DEBUG) Logger::Msg(get_class($this)."::".__FUNCTION__."()");

		$aCategories = $this->GetAll();
		
		$sStr = "<select name='".$sName."'  class='ddlist'>";
		
		$sStr .= "<option value='null'>select</option>";
		
		foreach ($aCategories as $aCategory) {	
			$checked = ($iSelectedValue == $aCategory['id']) ? "selected" : ""; 
			$sStr .= "<option value='".$aCategory['id']."' ".$checked.">".$aCategory['name']."</option>";
		}

		$sStr .= "</select>";
		
		return $sStr;
		
	}
	
	public function SetFromObject($o) {
	
		if (!is_object($o)) return FALSE; 
		foreach($o as $k => $v) {
			$this->$k = $v;
		}
		return TRUE;
	}
	
	public function GetCategory() {
		return $this->aCategory;
	}
	
	public function SetCategory($aCategory) {
		$this->aCategory = is_array($aCategory) ? $aCategory : array();
	}

	private function Cast($in) {
		$out = array();
		if(is_array($in)) {
			foreach($in as $o) {
				$c = new Category();
				$c->SetFromObject($o);
				$out[] = $c;	
			}
		}
		return $out;
	}
	
	public function GetCategoryPanelHTML() {

		if (DEBUG) Logger::Msg(get_class($this)."::".__FUNCTION__."()");
		
		global $db,$_CONFIG;
		
		// display the category select list
		$db->query("SELECT c.id,c.name,c.url_name FROM category c ORDER BY c.id, name asc");
		$this->SetCategory($db->getObjects());
				
	}	
	
   public function LoadTemplate($sFilename) {
		$this->oTemplate = new Template();
		$this->oTemplate->SetFromArray(array(
										"CATEGORY_OBJECT" => $this,
										"CATEGORY_ARRAY" => $this->GetCategory(),
			
										));
		$this->oTemplate->LoadTemplate($sFilename);
	}

	public function Render() {
		return $this->oTemplate->Render();
	}

}



?>
