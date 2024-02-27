<?


/*
 * Classes to represent the primary navigation
 *
 */

class Nav implements TemplateInterface {

		private $oTemplate;
		private $aSections;

		public function __Construct() {
		    
			$this->aSections = array();
		}

		public function Setup($filePath = PATH_NAV_CONFIG)
		{

		    $file = $filePath;
		    $xml = simplexml_load_file($file) or die ("Unable to load Navigation XML file!");
		    
		    foreach($xml->xpath('//section') as $section) {
		        
		        $oSection = new NavSection();
		        $oSection->SetTitle((string)$section->title);
		        $oSection->SetDesc((string)$section->desc);
		        $oSection->SetLink((string)$section->link);
		        
		        foreach($section->subsections as $subsections) {
		            foreach($subsections as $subsection) {
		                $oSubSection = new NavSubSection();
		                $oSubSection->SetTitle((string)$subsection->title);
		                $oSubSection->SetLink((string)$subsection->link);
		                $oSubSection->SetClass((string)$subsection->class);
		                
		                // only support for 2 level nav, could be made into recursive func in future
		                foreach($subsection->subsections as $section_subsections) {
		                    foreach($section_subsections as $section_subsection) {
		                        $oLevel2SubSection = new NavSubSection();
		                        $oLevel2SubSection->SetTitle((string)$section_subsection->title);
		                        $oLevel2SubSection->SetLink((string)$section_subsection->link);
		                        $oLevel2SubSection->SetClass((string)$section_subsection->class);
		                        $oSubSection->SetSubSection($oLevel2SubSection);
		                        
		                    }
		                }
		                $oSection->SetSubSection($oSubSection);
		            }
		        }
		        
		        $this->SetSection($oSection);
		    }
		    
		    
		    $this->LoadTemplate("nav_primary.php");

		}

		public function GetSections() {
			return $this->aSections;
		}

		public function SetSection($oSection) {
			$this->aSections[] = $oSection;
		}


        public function LoadTemplate($sFilename) {

                $this->oTemplate = new Template();
                $this->oTemplate->SetFromArray(array("SECTIONS" => $this->GetSections() ));
                $this->oTemplate->LoadTemplate($sFilename);

        }


        public function Render() {
                return $this->oTemplate->Render();
        }
	

}




class NavSection {

	private $aSubSection;
	private $sTitle;
	private $sDesc;
	private $sLink;
	private $bActive; /* is this section selected? */

	public function __Construct() {
		$this->aSubSection = array();
		$this->bActive = FALSE;
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

	public function SetLink($sLink) {
		$this->sLink = $sLink;
	}

	public function GetLink() {
		return $this->sLink;
	}

	public function SetActive() {
		$this->bActive = TRUE;
	}

	public function GetActive() {
		return ($this->bActive) ? "active" : "";
	}

	public function GetSubSections() {
		return $this->aSubSection;
	}

	public function SetSubSection($oSubSection) {
		$this->aSubSection[] = $oSubSection;
	}

}


class NavSubSection {

	private $sTitle;
	private $sLink;
	private $sClass;
	private $aSubSection;
	
	public function __Construct() {
	}

	public function SetTitle($sTitle) {
		$this->sTitle = $sTitle;
	}

	public function GetTitle() {
		return $this->sTitle;
	}

	public function SetLink($sLink) {
		$this->sLink = $sLink;
	}

	public function GetLink() {
		return $this->sLink;
	}

	public function SetClass($sClass) {
		$this->sClass = $sClass;
	}

	public function GetClass() {
		return $this->sClass;
	}

	public function GetSubSections() {
		return $this->aSubSection;
	}
	
	public function SetSubSection($oSubSection) {
		$this->aSubSection[] = $oSubSection;
	}
	
	public function HasSubSections() {
		return (is_array($this->aSubSection) && (count($this->aSubSection)) >= 1) ? TRUE : FALSE;
	}
	
}


?>
