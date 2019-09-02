<?php



class EditStep extends GenericStep {
	
	public function __construct( ){
		
		parent::__construct();
		
	}
	
	public function Process() { 
		
		print __CLASS__."->".__FUNCTION__;
		
	}
	

}


?>