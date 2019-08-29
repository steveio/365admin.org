<?



/*
 * A container object to represent a collection of articles
 *
 */


class ArticleContainer implements TemplateInterface {

   private $sId;
   private $sTitle;
   private $iCols; /* column width of outer container */
   private $iContentCols; /* column width of inner content elements */
   private $aArticle;
   private $sLinkUrl; /* optional more link appended after content */
   private $sLinkLabel; /* more link label */


   public function __Construct() {
      $this->aArticle = array();
	  $this->SetCols(4);
   }

	public function SetId($sId) {
		$this->sId = $sId;
	}

	public function GetId() {
		return $this->sId;
	}

	public function SetTitle($sTitle) {
		$this->sTitle = $sTitle;
	}

	public function GetTitle() {
		return $this->sTitle;
	}

	public function SetLinkLabel($sLabel) {
		$this->sLinkLabel = $sLabel;
	}

	public function GetLinkLabel() {
		return $this->sLinkLabel;
	}

	public function SetLinkUrl($sLink) {
		$this->sLinkUrl = $sLink;
	}

	public function GetLinkUrl() {
		return $this->sLinkUrl;
	}


   public function GetCols() {
		$a = array(1=>"one",2=>"two",3=>"three",4=>"four","five");
		return $a[$this->iCols];
   }

   public function SetCols($iCols) {
      $this->iCols = $iCols;
   }

   public function GetContentCols() {
		$a = array(1=>"one",2=>"two",3=>"three",4=>"four","five");
		return $a[$this->iContentCols];
   }

   public function SetContentCols($iCols) {
      $this->iContentCols = $iCols;
   }


   public function SetArticle($oArticle) {
      $this->aArticle[] = $oArticle;
   }

   public function GetArticle($idx) {
      return $this->aArticle[$idx];
   }

   public function GetArticles() {
      return $this->aArticle;
   }

   public function GetArticleCount() {
      return count($this->aArticle);
   }

	public function LoadTemplate($sFilename) {
		$this->oTemplate = new Template();
		$this->oTemplate->SetFromArray(array(
										"ID" => $this->GetId(),
										"TITLE" => $this->GetTitle(),
										"COLS" => $this->GetCols(),
										"CONTENT_COLS" => $this->GetContentCols(),
										"ARTICLES" => $this->GetArticles(),
										"LINK_LABEL" => $this->GetLinkLabel(),
										"LINK_URL" => $this->GetLinkUrl()
			
										));
		$this->oTemplate->LoadTemplate($sFilename);
	}

	public function Render() {
		return $this->oTemplate->Render();
	}


}





?>