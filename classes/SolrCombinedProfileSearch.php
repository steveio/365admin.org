<?php

/*
 * SOLR Search for both Company & Placement Profiles
 * 
 * Extends SolrSearch to provide  
 * result set processing - balanced distributor 
 * is invoked on returned ID's to fetch an even  
 * spread of profiles across range of advertisers
 * 
 * 
 */


class SolrCombinedProfileSearch extends SolrSearch {
	
    protected $_aBalancedPlacementId;
    protected $_aProfile;
    
	public function __construct($solr_config) {
		
		parent::__construct($solr_config);
		
	}

	public function processResult()
	{
	    
	    $this->arrId = array();
	    $this->aPlacementId = array();
	    $this->aCompanyId = array();
	    $this->aPlacement = array();
	    $this->aCompany = array();
	    
	    if ($this->getNumFound() >= 1) {
	        
	        $this->resultset = $this->getResultSet();
	        
	        $this->fetched = 0;
	        
	        $aCompanyIdProcessed = array();
	        
	        foreach($this->resultset as $doc)
	        {
	            
	            $this->arrId[$doc->profile_type."_".$doc->profile_id] = $doc->score;
	            
	            switch($doc->profile_type)
	            {
	                case 0:
	                    $this->aCompanyId[] = $doc->profile_id;
	                    $this->fetched++;
	                    break;
	                case 1:
	                    $this->aPlacementId[] = $doc->profile_id;
	                    $this->fetched++;
	                    break;
	            }
	            
	            if ($this->fetched == $this->getRowsToFetch())
	            {
	                break;
	            }
	            
	        }
	        
	        /*
	         print "Rows to Fetch: ".$this->getRowsToFetch();
	         print ",Fetched: ".$this->fetched;
	         print ",Total Placement: ".count($this->aPlacementId);
	         print ",Total Company: ".count($this->aCompanyId);
	         print_r("<pre>");
	         print_r($aCompanyIdProcessed);
	         print_r($this->aAllPlacementId);
	         print_r($aPlacementId);
	         print_r($this->resultset);
	         print_r("</pre>");
	         die();
	         */
	        
	    }
	    
	    if (is_array($this->aPlacementId) && count($this->aPlacementId) >= 1)
	    {
	        $this->aPlacement = PlacementProfile::Get("ID_LIST_SEARCH_RESULT",$this->aPlacementId, FETCHMODE__SUMMARY);
	    }
	    
	    if (is_array($this->aCompanyId) && count($this->aCompanyId) >= 1)
	    {
	        $this->aCompany = CompanyProfile::Get("ID_SORTED",$this->aCompanyId, FETCHMODE__SUMMARY);
	    }
	    
	    $this->_aProfile = array();
	    
	    foreach($this->arrId as $key => $score)
	    {
	        $bits = explode("_", $key);
	        $profile_type = $bits[0];
	        $profile_id = $bits[1];
	        
	        if ($profile_type == 0)
	        {
	            if (array_key_exists($profile_id, $this->aCompany))
	            {
	                $this->_aProfile[] = $this->aCompany[$profile_id];
	            }
	        } elseif ($profile_type == 1)
	        {
	            if (array_key_exists($profile_id, $this->aPlacement))
	            {
	                $this->_aProfile[] = $this->aPlacement[$profile_id];
	            }
	        }
	    }
	    
	    /*
	     print_r("<pre>");
	     print_r("Limit: ".$this->getRowsToFetch()."<br />");
	     print_r("Fetched: ".$this->fetched."<br />");
	     print_r("CompanyId: ".count($this->aCompanyId)."<br />");
	     print_r("PlacementId: ".count($this->aPlacementId)."<br />");
	     print_r("aCompany: ".count($aCompany)."<br />");
	     print_r("aPlacement: ".count($aPlacement)."<br />");
	     print_r($this->aPlacementId);
	     print_r($this->_aProfile);
	     print_r("</pre>");
	     die(__FILE__."::".__LINE__);
	     */
	    
	    $this->setFacetFieldResult();
	    $this->setFacetQueryResult();
	    
	}

	
	public function processResultBrandBalance() 
	{

	    $this->arrId = array();
	    $this->aPlacementId = array();
	    $this->aCompanyId = array();
	    $this->aPlacement = array();
	    $this->aCompany = array();

	    $this->iRowsToParse = ($this->getRowsToFetch() * 2); // read ahead double page-size to fetch spread of brand 

		if ($this->getNumFound() >= 1) {

			$this->resultset = $this->getResultSet();

			$this->fetched = 0;
			$this->company = 0;
			$this->placement = 0;

		    $aCompanyIdProcessed = array();

		    foreach($this->resultset as $doc)
		    {
		        // index of all result doc ids keyed on profile_type
		        $this->arrId[$doc->profile_type."_".$doc->profile_id] = $doc->score;

		        // array of seperate company & placement ids
		        switch($doc->profile_type)
		        {
		            case 0:
		                $this->aCompanyId[] = $doc->profile_id;
		                $this->fetched++;
		                $this->company++;
		                break;
		            case 1:
		                // placement id (grouped by company_id) 
		                $this->aPlacementId[$doc->company_id][] = $doc->profile_id;
		                $this->fetched++;
		                $this->placement++;
		                break;
		        }

		        if ($this->fetched == $this->iRowsToParse)
		        {
		            break;
		        }
		    }		    

		    $aCompanyId = array_keys($this->aPlacementId);

		    $aPlacementId = array();
		    $idx = 0;
		    $fetched = 0;

		    /*
		     * Iterate through each brand's set of placement ids,
		     * returning one from each, until 
		     *  - fetch limit reached 
		     *  - or all returned placement ids processed 
		     */
		    while($fetched < $this->getRowsToFetch())
		    {
		        $comp_id = $aCompanyId[$idx];

		        if (count($this->aPlacementId[$comp_id]) >= 1)
		        {
		            $aPlacementId[] = array_shift($this->aPlacementId[$comp_id]);
		            $fetched++;
		        } else {
		            unset($aCompanyId[$idx]);
		            $aCompanyId = array_values($aCompanyId);
		            if (count(array_keys($aCompanyId)) < 1) break;
		        }

		        if ($idx >= count(array_keys($aCompanyId)) -1)
		        {
		            $idx = 0;
		        } else {
		          $idx++;
		        }
		    }
		}

		/*
	    print "Rows to Fetch: ".$this->getRowsToFetch();
	    print "Rows to Parse: ".$this->iRowsToParse;
	    print ",Fetched: ".$fetched;
	    print ",Total Placement: ".$this->placement." ( From comps: ".count(array_keys($this->aPlacementId)).")";
	    print ",Total Company: ".$this->company;
	    print_r("<pre>");
	    print_r($aPlacementId);
	    print_r($this->_aProfile);
	    print_r("</pre>");
	    die();
	    */
		
		/*
		 * Fetch Company & Placement Profile Objects
		 */
		if (is_array($aPlacementId) && count($aPlacementId) >= 1)
		{
		    $this->aPlacement = PlacementProfile::Get("ID_LIST_SEARCH_RESULT",$aPlacementId, FETCHMODE__SUMMARY);			    
		}

		if (is_array($this->aCompanyId) && count($this->aCompanyId) >= 1)
		{
		    $this->aCompany = CompanyProfile::Get("ID_SORTED",$this->aCompanyId, FETCHMODE__SUMMARY);
		}			
		
		$this->_aProfile = array();
		$fetched = 0;

		/*
		 * Re-index filtered (by brand) result set based on score (relevancy)
		 * 
		 * Combined Company Id + Placement Id should always yield sufficient rows for pagesize
		 * Some profiles may appear across two pages 
		 * 
		 */
		foreach($this->arrId as $key => $score)
		{
		    $bits = explode("_", $key);
		    $profile_type = $bits[0];
		    $profile_id = $bits[1];

		    if ($profile_type == 0)
		    {
		        if (array_key_exists($profile_id, $this->aCompany))
		        {
                    $this->_aProfile[] = $this->aCompany[$profile_id];
                    $fetched++;
		        }
		    } elseif ($profile_type == 1)
		    {
		        if (array_key_exists($profile_id, $this->aPlacement))
		        {
		            $this->_aProfile[] = $this->aPlacement[$profile_id];
		            $fetched++;
		        }
		    }
		    
		    if ($fetched == $this->getRowsToFetch()) break;
		}


		$this->setFacetFieldResult();
		$this->setFacetQueryResult();
	}

	protected function _balancePlacementDistribution()
	{
        /*
         * @deprecated
         * 
	    // reindex the array so placement keys for each company are a sequential numeric index
	    $aIdIndexedNumeric = array();
	    $i = 0;
	    $aId = array();
	    foreach($aResult as $company_id => $aPlacementId) {
	        $aId[$i++] = $aPlacementId;
	    }
	    	    
	    $oBalancedDistributor = new BalancedDistributor($aId);
	    $oBalancedDistributor->SetFetchSize(count($aId));
	    $oBalancedDistributor->SetStartIdx(0);
	    
	    $this->_aBalancedPlacementId = $oBalancedDistributor->Fetch($this->getRows());

	    print_r("<pre>");
	    print_r($this->_aBalancedPlacementId);
	    print_r("</pre>");
	    
	    */
	    
	}

	protected function _getBalancedPlacementId()
	{
	    return $this->_aBalancedPlacementId;
	}

	/**
	 * Return Profiles from result set according to specified type (PROFILE_PLACEMENT, PROFILE_COMPANY)
	 * @param constant int $type
	 * @return array result index => profile id
	 */
	protected function _getId($type)
	{
        $arrId = array();
        foreach($this->resultset as $idx => $doc)
        {
            if ($doc->profile_type == $type)
                $arrId[$idx] = $doc->profile_id;
        }
        return $arrId;
    }
	    
	public function getId($bool = false)
	{
	    return $this->arrId;
	}

	public function getProfile()
	{
	    return $this->_aProfile;
	}	
	
}
