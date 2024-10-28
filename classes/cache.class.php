<?


class Cache {

	/* get a page from the cache, optionally record a miss */
	public static function Get($uri,$iSiteId) {

		$p = CACHE_PATH."/page/".md5($uri).".cache";
		if (file_exists($p)) {
			include($p);
			die();
		} else {
		   if (CACHE_LOG) {

			$f = "/tmp/cache_miss_".$iSiteId.".log";
	                $fh = fopen($f, 'a');
                	if (!$fh) return false;
        	        fwrite($fh, $uri."\n\r");
	                fclose($fh);

		   }

		}
	}

	/* save a cache page */
	public static function Save($f,$d) {

	    if(!file_exists($f)){ //permissions for cache refresh cronjob  
	        touch($f);
	        chmod($f, 0660);
	        chgrp($f, "web_developer");
	    }

		$fh = fopen($f, 'w');
		if (!$fh) return false;
		fwrite($fh, $d);
		fclose($fh);
	}

	
	/* check if page exists in cache index table */ 
	public static function Exists($iSiteId, $sUri) {
		
		global $db;
		
		$db->query("SELECT 1 FROM cache WHERE uri = '".$sUri."' AND sid = ".$iSiteId);
		
		if ($db->getNumRows() == 1) return TRUE;
		
	}
	
	/*
	 * When a resource is updated we must update the cached page 
	 * on each site this page appears.  The cache index table is flushed
	 * and repopulated each night with pages for each site that should be cached  
	 * 
	 * This method returns an array of website id's from the cache index table
	 * for a given uri, these can then be used to trigger a manual cache page update  
	 * 
	 * @param string uri of page to fetch site id's for eg /company/bunac 
	 * @return mixed array of int website id's on success, FALSE on failure
	 * 
	 */
	public static function GetSiteIdsByUri($sUri) {
		
		global $db;

		$db->query("SELECT sid FROM cache WHERE uri = '".$sUri."'");
		
		if ($db->getNumRows() >= 1) {
			return $db->getRowsNum();
		}
		
	}
	
	
	/* generate a cache page 
	 * 
	 * @param string url to request this page via http eg. http://www.oneworld365.org/company/bunac
	 * @param string uri segment after hostname that uniquely identifies this page eg /company/bunac
	 * @param int webite id from db table website
	 * @param bool whether to sleep ie delay for an interval after refreshing page
	 *  
	 */
	public static function Generate($sUrl,$sUri,$iSiteId,$sleep = true) {

		global $db;

		switch($iSiteId) {
			case 0:
				$sPath = "/www/vhosts/oneworld365.org/htdocs/cache/page/";
				break;
			default: 
				return false;
		}
		
		
		$sUrl = $sUrl ."?&nocache=true";
		$sHTML = @file_get_contents($sUrl);
		if (!$sHTML) return false;

		
		$sCachePath = $sPath.md5($sUri).".cache";

		/*
		Logger::Msg($sUrl,'plaintext');
		Logger::Msg($sUri,'plaintext');
		Logger::Msg($iSiteId,'plaintext');
		Logger::Msg($sPath);
		*/
		
		Cache::Save($sCachePath,$sHTML);
		$db->query("UPDATE cache SET active = 'T', last_update = now()::timestamp WHERE uri = '".$sUri."' AND sid = ".$iSiteId);
		if ($sleep) { /* for batch updates */
			usleep(500000); /* throttle delay .5 sec */
		}		
	}

}


?>
