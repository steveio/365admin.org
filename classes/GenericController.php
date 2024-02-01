<?php

/*
 * GenericController.php
 * 
 * extends AbstractController
 * 
 * Handles domain specific processing / functionality that is common to all routes
 * 
 * Will generally only be instantiated via a derived controller class
 *  
 * For example -
 * 		setup page header / footer objects
 * 		logging
 * 		security 
 * 
 */




class GenericController extends AbstractController {
	
	protected function __construct( ){
		
		parent::__construct();
		
	}
	
	protected function Process() {}

	protected function PreProcess() {}

	protected function PostProcess() {}		
	
}