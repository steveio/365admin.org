<?php

/*
 * GenericStep.php
 * 
 * extends AbstractStep
 * 
 * Handles domain specific processing / functionality that is common to all steps
 * 
 * Will generally only be instantiated via a derived step class
 *  
 * For example -
 * 		setup page header / footer objects
 * 		logging
 * 		security 
 * 
 */




class GenericStep extends AbstractStep {
	
	protected function __construct( ){
		
		parent::__construct();
		
	}
		
	
	
	protected function Process() {}

	protected function PreProcess() {}

	protected function PostProcess() {}		
	
}