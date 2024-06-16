<?


/*
 * Header represents a page XHTML header
 */




class Header extends Layout {

   private $sTitle;
   private $sDesc;
   private $sKeywords;
   private $sUrl;
   private $sVerifyTag;
   private $sJSONLD;

   private $logo_url;
   
   
   private $aJsInclude; /* an array of JS include objects */
   private $aCssInclude; /* an array of CSS include objects */

   private $sJsOnload; /* a string containing JS onload funcs */

   private $aNav; /* an array to hold top / main navigation */

   private $sCurrentTemplate; /* holds template path supplied by initial caller, to enable reload */

   private $aBanner; /* array of ad banner defs */ 
   private $aFlashBanner; /* array flash banners */
   
   private $aBreadCrumb; /* navigation breadcrumb array(url => label) */
   
   public function __Construct() {

	  parent::__Construct();

	  $this->aJsInclude = array();
	  $this->aCssInclude = array();
	  $this->aBanner = array();
	  $this->aFlashBanner = array();
	  $this->aBreadCrumb = array();

   }

   public function SetTitle($sTitle) {
		$this->sTitle = $sTitle;
   }

   public function GetTitle() {
		return $this->sTitle;
   }

   public function GetLogoUrl() {
		return $this->logo_url;
   }

   public function SetLogoUrl($url) {
		$this->logo_url = $url;
   }
   
   public function SetDesc($sDesc) {
		$this->sDesc = $sDesc;
   }

   public function GetDesc() {
		return $this->sDesc;
   }

   public function SetJSONLD($sJSONLD) {
       $this->sJSONLD = $sJSONLD;
   }
   
   public function GetJSONLD() {
       return $this->sJSONLD;
   }
   
   public function SetUrl($sUrl) {
       $this->sUrl = $sUrl;
   }
   
   public function GetUrl() {
       return $this->sUrl;
   }
   
   public function SetKeywords($sKeywords) {
		$this->sKeywords = $sKeywords;
   }

   public function GetKeywords() {
		return $this->sKeywords;
   }

   public function SetVerifyTag($sTag) {
		$this->sVerifyTag = $sVerifyTag;
   }

   public function GetVerifyTag() {
		return $this->sVerifyTag;
   }
   
   public function SetBanners($aBanner) {
   		$this->aBanner = $aBanner;
   }
   
   public function GetBanners() {
   		return $this->aBanner;
   }

   public function SetFlashBanners($aFlashBanner) {
   		$this->aFlashBanner = $aFlashBanner;
   }
   
   public function GetFlashBanners() {
   		return $this->aFlashBanner;
   }
   
   public function GetJsInclude() {
		$out = "";
		foreach($this->aJsInclude as $oInclude) {
			$out .= $oInclude->Render()."\n";
		}
		return $out;
   }

   public function SetJsInclude($oInclude) {
		$this->aJsInclude[] = $oInclude;
   }

	public function SetJsOnload($js_string) {
		$this->sJsOnload .= $js_string."\n\n";
	}

	public function GetJsOnload() {
		return $this->sJsOnload;
	}

   public function GetCSSInclude($sBrowserCode) {
	  $out = "";
	  if (is_array($this->aCssInclude[$sBrowserCode])) {
		foreach($this->aCssInclude[$sBrowserCode] as $oCss) {
			$out .= $oCss->Render()."\n";
		}
	  }
      return $out;
   }

   public function SetCssInclude($sBrowserCode, $oInclude) {
      $this->aCssInclude[$sBrowserCode][] = $oInclude;
   }

   public function SetNav($key, $oNav) {
		$this->aNav[$key] = $oNav;
   }

   public function GetNav($key) {
		return $this->aNav[$key];
   }

   public function SetBreadCrumb($aBreadCrumb) {
		$this->aBreadCrumb = $aBreadCrumb;
   }

   public function GetBreadCrumb() {
		return $this->aBreadCrumb;
   }
   
   
   public function LoadTemplate($sFilename) {

		if (strlen($sFilename) > 1) {
			$this->SetCurrentTemplate($sFilename);	
		}

		$this->oTemplate->SetFromArray(array(
										"TITLE" => $this->GetTitle(),
										"LOGO_URL" => $this->GetLogoUrl(),
										"DESCRIPTION" => $this->GetDesc(),
										"KEYWORDS" => $this->GetKeywords(),
		                                "JSONLD" => $this->GetJSONLD(),
		                                "URL" => $this->GetUrl(),
										"JS_INCLUDE" => $this->GetJsInclude(),
										"JS_ONLOAD" => $this->GetJsOnload(),
										"CSS_GENERIC" => $this->GetCSSInclude("CSS_GENERIC"),
										"CSS_FONTS" => $this->GetCSSInclude("CSS_FONTS"),
		                                "TOP_NAV" => $this->GetNav("TOP_NAV")
										));

		$this->oTemplate->LoadTemplate($this->GetCurrentTemplate());
	}
	
	/* reloads template w/ latest header data */
	public function Reload() {
		$this->LoadTemplate($this->GetCurrentTemplate());
	}

	private function SetCurrentTemplate($template) {
		$this->sCurrentTemplate = $template;
	}

	private function GetCurrentTemplate() {
		return $this->sCurrentTemplate;
	}
}



class CssInclude {

	private $rel;
	private $type;
	private $href;
	private $media;

	function __Construct() {

		$this->rel = "stylesheet";
		$this->type = "text/css";
		$this->href = "";
		$this->media = "screen";

	}

	public function SetSrc($url)
	{
	    $this->hred = $url;
	}

	public function SetHref($href) {
		$this->href = $href;
	}

	public function GetRel() {
		return $this->rel;
	}

	public function GetType() {
		return $this->type;
	}

	public function GetHref() {
		return $this->href;
	}

	public function GetMedia() {
		return $this->media;
	}

	public function SetMedia($media) {
		$this->media = $media;
	}

	public function Render() {
		return "<link rel=\"".$this->GetRel()."\" type=\"".$this->GetType()."\" href=\"".$this->GetHref()."\" media=\"".$this->GetMedia()."\" />";
	}

}


class JsInclude {

	private $type;
	private $src;
	private $referrerPolicy;

	function __Construct() {
		$this->src = "";
		$this->type = "text/javascript";
		$this->referrerPolicy = "";
	}

	public function SetSrc($src) {
		$this->src = $src;
	}

	public function GetSrc() {
		return $this->src;
	}

	public function GetType() {
		return $this->type;
	}

	public function SetReferrerPolicy($str) {
		$this->referrerPolicy = $str;
	}

	public function GetReferrerPolicy() {
		return $this->referrerPolicy;
	}

	public function Render() {

		$strReferrerPolicy = ($this->referrerPolicy == "") ? "" : "referrerpolicy=\"".$this->referrerPolicy."\"";

		return "<script type=\"".$this->GetType()."\" src=\"".$this->GetSrc()."\" ".$strReferrerPolicy."></script>";
	}

}



?>
