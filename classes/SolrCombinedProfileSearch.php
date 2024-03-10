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

			while($this->fetched < $this->getRowsToFetch())
			{
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
			                $this->aAllPlacementId[] = $doc->profile_id;
			                if (!array_key_exists($doc->company_id, $aCompanyIdProcessed))
			                {
			                    /*
			                     * If there are sufficient listings, show 1 result per brand
			                     * 
			                     */
			                    $this->aPlacementId[] = $doc->profile_id;
			                    $this->fetched++;
			                    $aCompanyIdProcessed[$doc->company_id]++;			                    
			                }
			                break;
			        }

			        if ($this->fetched == $this->getRowsToFetch())
			        {
			            break;
			        }
			    }
			    
			    if ($this->fetched < $this->getRowsToFetch())
			    {
			        unset($this->aPlacementId);
			        $this->aPlacementId = array_slice($this->aAllPlacementId, 0, $this->getRowsToFetch());
			    }			    
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
