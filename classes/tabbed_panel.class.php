<?



/*
 * Classes to Render a Tabbed Panel UI Control
 *
 * Static tabs are defined in XML config file 
 * /conf/tabbed_panels.xml
 * These provide tab configuration templates for search results, company page eg
 *
 * Tabs can be added or removed from specific uri's vi tab mapping DB rules 
 * Additional Tabs to be added are defined in DB in tab table
 *
 * Implements template interface to allow custom styling
 *
 */


/* codes from tab map table */
define("TABBED_PANEL_ADD",1);
define("TABBED_PANEL_DEL",-1);




class TabbedPanel implements TemplateInterface {

   private $sId;
   private $sTabMode; /* onlick tabs use href links OR javascript? */  
   private $aTabs;
   private $iCols; /* column width of panel ie 2 / 3 / 4 */
   private $sTitle;   
   private $sDesc;
   private $aContent;
   private $sLinkPrefix; /* allows calling page to inject base url/path into tab links */
   private $sCookieName; 
   
   private $aMappingRules; /* rules to add/remove tabs supplied by caller */
   private $aTabsAdded;  /* array of tab id's added via mapping rules */
   private $aTabsRemoved;  /* array of tab id's removed via mapping rules */
   
   public function __Contrust() {

   	  $this->sTabMode = "JS"; /* default: javascript onclick tabs w/ show/hide panels */
	  $this->aTabs = array();
	  $this->aContent = array();

	  $this->aMappingRules = array();
	  $this->aTabsAdded = array();
	  $this->aTabsRemoved = array();
	  
   }


	public function SetId($sId) {
		$this->sId = $sId;
	}

	public function GetId() {
		return $this->sId;
	}

	public function SetTabMode($mode) {
		$this->sTabMode = $mode;
	}

	public function GetTabMode() {
		return $this->sTabMode;
	}
	
	public function GetTabCount() {
		return count($this->aTabs);
	}
	
	public function LoadFromXmlFile($file) {
		
		$xml = simplexml_load_file($file) or die ("Unable to load XML file!");
		
		foreach($xml as $tpanel) {
			if ($tpanel->id == $this->GetId()) {				
				$this->SetTitle((string)$tpanel->title);
				$this->SetDesc((string)$tpanel->desc);
				$this->SetTabMode((string)$tpanel->tabmode);
				
				$i = 1;
				foreach($tpanel->tabs->tab as $xmltab) {
					$oTab = new Tab();

					$oTab->SetFromArray(array(
										"Id" => (string) "TAB".sprintf("%02d", $i),
										"Idx" => (string) sprintf("%02d", $i),
										"Title" => (string) $xmltab->title,
										"Desc" => (string) $xmltab->desc,
										"Link" => (string) $xmltab->link,
										"Code" => (string) $xmltab->code,
										"Active" => (((int) $xmltab->active) == 1) ? TRUE : FALSE,
										));
					$this->SetTab($oTab);
					$i++;
				}
			}
		}
   }

   public function GetCols() {
		$a = array(1=>"one",2=>"two",3=>"three",4=>"four","five");
		return $a[$this->iCols];
   }

   public function SetCols($iCols) {
      $this->iCols = $iCols;
   }
   
   public function GetMappingRules() {
		return $this->aMappingRules;
   }
   
   public function GetTabsAdded() {
		return $this->aTabsAdded; 
   }

   public function GetTabsRemoved() {
		return $this->aTabsRemoved; 
   }
   

   /*
    * Dynamically adjust tabbed panel based on supplied mapping rules
    *
    * Allows runtime adding/removing of tabs based on rules defined in db
    * $aMapping is assumed to be built by calling TabMapping::GetByUri($uri);
    *
   */
   public function ProcessTabMappings($aMapping) {

   	if (DEBUG) Logger::Msg($aMapping);
   
	if (!is_array($aMapping) || count($aMapping) < 1) {
		return FALSE;
	}
	
	/* store mappings in an instance variable */
	$this->aMappingRules = $aMapping;


	foreach($aMapping as $oMapping) {

		/* add an additional tab to the panel */
		if (($oMapping->GetMode() == TABBED_PANEL_ADD) && is_object($oMapping->GetTab())) {
			$this->SetTabAndIndex($oMapping->GetTab());

		} elseif ($oMapping->GetMode() == TABBED_PANEL_DEL) {
			/* delete a tab from the panel */			
			$this->DeleteTabByCode($oMapping->GetCode());
		}
	}

   }


   public function SetTab($oTab) {
      $this->aTabs[] = $oTab;
   }

   /* add a new tab and set tab id, idx fields */

   public function SetTabAndIndex($oTab) {
	
	$id = $this->GetTabCount() + 1;
	$oTab->SetId("TAB".sprintf("%02d", $id));
	$oTab->SetIdx(sprintf("%02d", $id));
	$oTab->SetLink($this->sLinkPrefix.$oTab->GetLink());
	$this->aTabs[] = $oTab;
	$this->aTabsAdded[] = $oTab->GetId(); 

   }


   public function GetTabs() {
      return $this->aTabs;
   }

   public function GetTabById($id) {
      foreach($this->aTabs as &$oTab) {
         if ($oTab->GetId() == $id) return $oTab;
      }
   }
   
   
   public function GetActiveByCode($code) {
      foreach($this->aTabs as &$oTab) {
         if ($oTab->GetCode() == $code) return $oTab;
      }
   }
   
   public function SetActiveTabById($sTabId) {
	  $found = FALSE;
      foreach($this->aTabs as &$oTab) {
         if ($oTab->GetId() == $sTabId) {
            $oTab->SetActive(TRUE);
            $found = TRUE;
         } else {
           $oTab->SetActive(FALSE);
         }
      } 
      if (!$found) $this->aTabs[0]->SetActive(TRUE);
   }
   
   public function SetActiveTabByCode($sTabCode) {
      foreach($this->aTabs as &$oTab) {
         if ($oTab->GetCode() == $sTabCode) {
            $oTab->SetActive(TRUE);
         } else {
           $oTab->SetActive(FALSE);
         }
      }
   }   

   public function GetTabIdxByCode($code) {
      foreach($this->GetTabs() as $oTab) {
        if ($oTab->GetCode() == $code) {
			return $oTab->GetIdx();
		}
	  }
   }
   
  
   
   public function GetActiveTab() {
      foreach($this->GetTabs() as $oTab) {
		if ($oTab->GetActive()) {
			return $oTab->GetId();
		}
	  }
   }

   public function GetLinkPrefix() {
		return $this->sLinkPrefix;
   }
   
   /* allows injection of a request specific url prefix */
   public function SetLinkPrefix($prefix) {
	$this->sLinkPrefix = $prefix;
	foreach($this->aTabs as &$oTab) {
		$oTab->SetLink($prefix.$oTab->GetLink());
	}
   }
   
   public function GetCookieName() {
   	return $this->sCookieName;
   }
   
   public function SetCookieName($cookie_name) {
   	$this->sCookieName = $cookie_name;
   }
   
   public function DeleteTabById($sTabId) {
		foreach($this->aTabs as $k => $oTab) {
	         if ($oTab->GetId() == $sTabId) {
	         	unset($this->aTabs[$k]);
	         } 
		}
   }


   public function DeleteTabByCode($sTabCode) {
   		if (DEBUG) Logger::Msg("DeleteTabByCode() ".$sTabCode);

   		//Logger::Msg($this->aTabs);
   		$array_keys = array_keys($this->aTabs);
		foreach($array_keys as $key) {
			 $oTab = $this->aTabs[$key];
	         if ($oTab->GetCode() == $sTabCode) {
	         	unset($this->aTabs[$key]);
	         } 
		}
		
   }
   
   
   /* convenience method to set tab attributes eg link, title or desc at runtime 
    * @param string tab id eg TC01
    * @param string attribute id, must tab method setter name exactly eg "Active" calls $oTab->SetActive()
    * @param mixed value to be set  
    */
   public function SetTabAttribute($sTabId,$sAttribId,$value) {
      foreach($this->aTabs as &$oTab) {
         if ($oTab->GetId() == $sTabId) {
         	$m = "Set".$sAttribId;
            $oTab->$m($value);
         }
      }
   }   
   
   public function SetTitle($sTitle) {
      $this->sTitle = $sTitle;
   }

   public function GetTitle() {
      return $this->sTitle;
   }

	public function SetDesc($sDesc) {
		$this->sDesc = $sDesc;
	}

	public function GetDesc() {
		return $this->sDesc;
	}

   public function SetContentFromHTML($sContentHTML) {
      $this->aContent[] = $sContentHTML;
   }

   /* content must conform to TemplateInterface ie has Render() method */
   public function SetContentFromObject($oContent) {
      $this->aContent[] = $oContent->Render();
   }

   public function GetContent() {
		return $this->aContent;
   }

   public function GetContentHTML() {

		$out = "";

	   foreach($this->GetContent() as $html) {
			$out .= $html;
	   }
      return $out;
   }


	public function LoadTemplate($sFilename) {

		$this->oTemplate = new Template();

		$this->oTemplate->SetFromArray(array(
												"ID" => $this->GetId(),
												"TITLE" => $this->GetTitle(),
												"COLS" => $this->GetCols(),
												"TABS" => $this->GetTabs(),
												"CONTENT" => $this->GetContentHTML()
										));
										
		$this->oTemplate->LoadTemplate($sFilename);

	}



	public function Render() {

		return $this->oTemplate->Render();

	}


	/* return JQuery onclick event handlers to change active tab state */
	public function GetEventJQuery() {

		$oTemplate = new Template();
		$oTemplate->SetFromArray(array(
										"COOKIE_NAME" => $this->GetCookieName(),
										"ACTIVE_TAB" => $this->GetActiveTab(),
										"TABS" => $this->GetTabs()
										));
										
		$oTemplate->LoadTemplate("tabbed_panel_jquery.php");

		return $oTemplate->Render();

	}

}


/* an instance of a tab on a tabbed panel */
class Tab {

	private $bActive;
	private $sId;
	private $sIdx;
	private $sTitle;
	private $sDesc;
	private $sLink;
	private $sCode;

	public function __Construct() {
		$this->bActive = FALSE;
	}

	public function SetFromArray($a) {
		foreach($a as $k => $v) {
			$m = "Set".$k;
			$this->$m($v);
		}
	}

	public function SetId($sId) {
		$this->sId = $sId;
	}

	public function GetId() {
		return $this->sId;
	}

	public function SetIdx($sIdx) {
		$this->sIdx = $sIdx;
	}

	public function GetIdx() {
		return $this->sIdx;
	}
	
	
	public function GetTitle() {
		return $this->sTitle;
	}

	public function SetTitle($sTitle) {
		$this->sTitle = $sTitle;
	}

	public function SetDesc($sDesc) {
		$this->sDesc = $sDesc;
	}

	public function GetDesc() {
		return $this->sDesc;
	}

	public function SetLink($sLink) {
		$this->sLink = $sLink;
	}

	public function GetLink() {
		return $this->sLink;
	}
	
	/* return uri minus host */
	public function GetSectionUri() {
		$link = $this->GetLink();
		if (!preg_match("/^\//",$link)) {
			$a = explode("/",$link);
			array_shift($a); /* http */
			array_shift($a); /* // */
			array_shift($a); /* host */
			return $s = "/".implode("/",$a);
		} else {
			return $link;
		}
	}

	public function SetCode($sCode) {
		$this->sCode = $sCode;
	}

	public function GetCode() {
		return $this->sCode;
	}
	
	
	public function SetActive($bActive) {
		$this->bActive = $bActive;
	}

	public function GetActive() {
		return $this->bActive;
	}

	/* fetch tab details from db */
	public function GetByCode($code) {

		global $db;

		$sql = "SELECT * FROM tab WHERE code = '".$code."'";

                $db->query($sql);

                if ($db->getNumRows() == 1) {
                        $oResult = $db->getObject();
			//Logger::Msg($oResult);
			$this->SetTitle($oResult->title);
			$this->SetLink($oResult->link);
			$this->SetCode($oResult->code);
			return TRUE;
                }
 
	}
}



/* a mapping rule to add or remove a tab from a specific section (uri) */
class TabMapping {

        private $uri; /* uri this rule is applicable for */
        private $tab_code; /* tab code to add or remove */
        private $mode; /* { TABBED_PANEL_ADD || TABBED_PANEL_DEL } - add or remove a tab */
	private $tab; /* if adding holds a reference to a valid tab object */

        public function __Construct() {
		$this->tab = FALSE;
        }

	public function GetMode() {
		return $this->mode;
	}
	
	public function GetCode() {
		return $this->tab_code;
	}

	public function SetTab($oTab) {
		$this->tab = $oTab;
	}

	public function GetTab() {
		return $this->tab;
	}

	public static function GetByUri($uri,$fuzzy = FALSE) {

		global $db;

		$operator = "=";
		$wildcard = "";

		if ($fuzzy) {
			$operator = "LIKE";
			$wildcard = "%";
		}
	
		$sql = "SELECT * FROM tab_map WHERE uri ".$operator." '".$uri.$wildcard."'";
	
                $db->query($sql);

		if ($db->getNumRows() >= 1) {
			$oResult = $db->getObjects();
		} else {
			return FALSE;
		}

		$aMappings = array();

		//Logger::Msg($oResult);

		foreach($oResult as $o) {
			$oTabMap = new TabMapping;
			$oTabMap->SetFromObject($o);
			//Logger::Msg($oTabMap);
			if ($oTabMap->GetMode() == TABBED_PANEL_ADD) {
				/* get tab details from db */
				$oTab = new Tab;
				if($oTab->GetByCode($oTabMap->GetCode())) {
					$oTabMap->SetTab($oTab);
				}
				//Logger::Msg($oTab);
			}
			$aMappings[] = $oTabMap;
		}

		return $aMappings;

	}
	
        public function SetFromObject($o) {

                foreach($o as $k => $v) {
                        $this->$k = $v;
                }
        }


}











?>
