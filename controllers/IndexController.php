<?php



class IndexController extends GenericController {
	
	public function __construct( ){
		
		parent::__construct();
		
	}
	
	public function Process() { 

	    Http::Redirect("/".ROUTE_LOGIN);

	}
	

}


?>