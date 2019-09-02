<?php



class File {

	
	public static function GetExt($sPath) {
		$s = strrchr($sPath, '.');
		if (strpos($s,"?") == -1) {
			return $s;
		} else {
			return substr($s,0,strpos($s,"?"));
		}
	}
	
	public static function GetRemoteFile($sUrl) {
		$sUrl = preg_replace('/ /','%20',$sUrl);
		return @file_get_contents($sUrl);
	}
	
	public static function Write($sFile,$sPath,$mode = "w") {
		
		if (DEBUG) Logger::Msg(get_class()."::".__FUNCTION__."() path = ".$sPath);
		
		if (!$rFile = fopen($sPath,$mode)) return false;
		
		if (!fwrite($rFile,$sFile)) return false;
		
		fclose($rFile);
		
		return true;
		
	}

	public static function Delete($sPath) {
		
		if (DEBUG) Logger::Msg(get_class()."::".__FUNCTION__."() path = ".$sPath);
		
		if (file_exists($sPath)) {
			unlink($sPath);	
		}
		
	}

	
	/*
	 * Handles file uploads
	 * 
	 *  @param array permitted file extensions
	 *  @param array permitted mime types
	 *  @param int size in bytes
	 *  @param string path to temp upload dir
	 *  @param string filename (must be unique)
	 *  @param array a container for error messages
	 * 
	 *  @return bool 
	 * 
	 */
	public static function Upload($sField,$aExt,$aMimeType,$iMaxSize = 1572864,$sTmpPath = "/tmp",$sFileName,&$aResponse) {
		
		if (DEBUG) Logger::Msg(get_class()."::".__FUNCTION__."()");
		
		if (DEBUG) Logger::Msg($_FILES[$sField]);
		
		$aResponse['isError'] = false;
		
		if((!empty($_FILES[$sField])) && ($_FILES[$sField]['error'] == 0)) {

		  $filename = basename($_FILES[$sField]['name']);
		  $ext = substr($filename, strrpos($filename, '.') + 1);
		  
		  $aResponse['file']['name'] = $filename; 
		  $aResponse['file']['ext'] = $ext;
		  $aResponse['file']['size'] = $_FILES[$sField]["size"];
		  $aResponse['file']['type'] = $_FILES[$sField]["type"];
		  
		  
		  if ((in_array($ext,$aExt)) && 
		  	(in_array($_FILES[$sField]["type"],$aMimeType)) && 
		    ($_FILES[$sField]["size"] < $iMaxSize)) {

		      $sPath = $sTmpPath."/".$sFileName;
		      
		      if (!file_exists($sPath)) {

		        if ((move_uploaded_file($_FILES[$sField]['tmp_name'],$sPath))) {
		        	return true;
		        } else {
		           $aResponse['msg']['error'] = "Error: A problem occurred during file upload!";
		           $aResponse['isError'] = true;
		        }
		      }
		  } else {
		  	 $aResponse['msg']['size'] = "Error: The uploaded file must be less than ".($iSize / 1024 /1024)."mb";
		  	 $aResponse['isError'] = true;
		  }
		} else {
			$aResponse['msg']['upload'] = "No file was uploaded.";
			$aResponse['isError'] = true;
		}		
	}
	
}



?>
