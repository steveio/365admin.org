<?php



class IndexStep extends GenericStep {
	
	public function __construct( ){
		
		parent::__construct();
		
	}
	
	public function Process() { 

	    Http::Redirect("/".ROUTE_LOGIN);

	}
	

}


?>