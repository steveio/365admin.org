<?php


class Request {
	
	public static function GetUri($return = "STRING") {
		
		$uri = preg_replace("/[^a-zA-Z0-9_\-\/\?\&=\.+]/","",$_SERVER['REQUEST_URI']);
		
		if ($return == "STRING") {
			return $uri; 
		} elseif ($return == "ARRAY") {
			$a = explode("?",$uri);
			return explode("/",urldecode($a[0]));
		}
		
	}
	
	public static function GetHostName($subdomain = false) {
	    if ($subdomain)
	    {
	        return $_SERVER['HTTP_HOST'];
	    } else {
    		$a = explode(".",$_SERVER['HTTP_HOST']);
    		return $hostname = $a[1].".".$a[2];
	    }
	}
	

	
	
}
