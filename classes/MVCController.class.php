<?php

/*
 * MVCController.class.php
 * 
 * A simple front controller implementation
 * 
 * Handles both:
 *   - static (inline) route mapping (URL Alias -> PHP script filename)
 *   - MVC route mapping (URL path -> Controller Class) 
 * 
 *
 * MVC routes use a collection of URL path -> class mappings defined in routes.xml file.
 * Each route must have a corresponding class implementation defined in /controllers
 * 
 * The first uri segment of a request after the host specifier
 * is used to map a request onto a defined route eg 
 * http://www.domain.com/login  would map to LoginRoute where LoginRoute->uri = '/login'    
 *
 * On successfuly mapping a request methods are called to fullfill the request - 
 * RouteClass->PreProcess()
 * RouteClass->Process() 
 * RouteClass->PostProcess()
 * Generally a controller class will only provide an implementation for ->Process()
 *
 */

class MVCController{
	
	protected $sRequestUri;  // string request uri eg /route1, maps to $oRoute->uri-mapping if matched 
	protected $nCurrentRouteId;  // int id of route to process, a pointer into $aRoutes
	protected $aRoutes; // array of route objects

	protected $bExceptionOnNotFound = true;
	
	public function __construct(){

		$this->aRoutes = array();
		$this->aRoutesProcessed = array();
		
	}

	public function SetExceptionOnNotFound($bExceptionOnNotFound)
	{
	    $this->bExceptionOnNotFound = $bExceptionOnNotFound;
	}

	public function Process() {
		
		try {

		    $this->MapRequest();

			$oRoute = $this->GetRouteById($this->GetCurrentRouteId());
			$oRoute->Process();

		} catch (NotFoundException $e) {

			if ($this->bExceptionOnNotFound)
			{
			    throw $e;
			}			
			return false;

		} catch (InvalidSessionException $e) {
			throw new InvalidSessionException($e->getMessage());
		} catch (Exception $e) {
			throw new Exception($e->getMessage());
		}
		
	}
	
	public function MapRequest() {
		
		try {
		    $this->SetCurrentRouteId(null);
		    $oRoute = $this->GetRouteByUriMapping($this->GetRequestUri());		    
		    if (is_object($oRoute))
		    {
		        $this->SetCurrentRouteId( $oRoute->GetId() );
		    }
			
		} catch (NotFoundException $e) {
            throw $e;
		} catch (Exception $e) { // general exception
            throw $e;
		}
		
	}
	
	
	public function SetRequestUri($sRequestUri) {
		$this->sRequestUri = $sRequestUri;
	}
	
	public function GetRequestUri() {
		return $this->sRequestUri;
	}
	
	public function SetCurrentRouteId($id) {
		$this->nCurrentRouteId = $id;
	}
	
	public function GetCurrentRouteId() {
		return $this->nCurrentRouteId;
	}	
	
	public function GetCurrentRoute() {
		return $this->GetRouteById($this->GetCurrentRouteId());
	}

	public function SetRouteFromXmlFile($xml_file_path, $brand_id) {
		
		if (!file_exists($xml_file_path)) {
			throw new Exception(ERROR_INVALID_XML_FILE_PATH . $xml_file_path);
		}
		
		$oXml = simplexml_load_file($xml_file_path);
		
		if (!is_object($oXml) || count($oXml->route) < 1) throw new Exception(ERROR_INVALID_XML_ROUTE_DEFS);
		
		foreach($oXml->route as $oXmlElement) {
			
			try {

					$class_ext_found = FALSE;
					
					if (isset($oXmlElement->brandextension)) { // look for a brand specific controller class 
						
						foreach($oXmlElement->brandextension->brand as $oBrandXmlNode) {
							if ($brand_id == (int)$oBrandXmlNode->attributes()->id[0]) {
								$classname = (string) $oBrandXmlNode->classname;
								$class_ext_found = TRUE;
							}
						}
						
					}
						
					if (!$class_ext_found) { // use generic controller class
						$classname = (string) $oXmlElement->classname;
					}

					$oRoute = new $classname();
					$oRoute->SetFromXml($oXmlElement);														
					$this->aRoutes[$oRoute->GetId()] = $oRoute;
					
				} catch (Exception $e) {
					throw new Exception($e->getMessage());
				}
			
			} // end foreach route
	}
	
	public function SetRoutes($aRoutes) {
		if (is_array($aRoutes)) $this->aRoutes = $aRoutes;
	}
	
	public function GetRoutes() {
		return $this->aRoutes;
	}
	
	public function GetRouteById($route_id) {
	    
		foreach($this->GetRoutes() as $oRoute) {
			if ($oRoute->GetId() == $route_id) return $oRoute;
		}

		throw new NotFoundException(ERROR_404_ROUTE_NOT_FOUND." id: ".$route_id);
	}
	
	public function GetRouteByName($route_name) {
		foreach($this->GetRoutes() as $oRoute) {
			if ($oRoute->GetName() == $route_name) return $oRoute;
		}
		
		throw new NotFoundException(ERROR_404_ROUTE_NOT_FOUND." ".$route_name);
	}

	public function GetRouteByUriMapping($uri) {
		foreach($this->GetRoutes() as $oRoute) {
			if ($oRoute->GetUriMapping() == $uri) return $oRoute;
		}
		
		throw new NotFoundException(ERROR_404_ROUTE_NOT_FOUND." ".$uri);
	}
	
		
}

?>