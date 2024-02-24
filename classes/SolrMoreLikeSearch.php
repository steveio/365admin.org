<?php



class SolrMoreLikeSearch extends SolrSearch {


	function __construct($solr_config) {

		parent::__construct($solr_config);

	}

	function getKeywords($id, $profile_type) {
	    
	    
	    // Solarium_Query_MoreLikeThis

	    // get a select query instance
	    $query = $this->client->createMoreLikeThis();
	    
	    
	    // add a query and morelikethis settings (using fluent interface)
	    $query->setQuery('id:'.$id)
	    ->getMoreLikeThis()
	    ->setFields('desc_short')
	    ->setMinimumDocumentFrequency(1)
	    ->setMinimumWordLength(3)
	    ->setMinimumTermFrequency(1);
	    
	    $query->setFields(array('profile_id'));
	    $query->setStart(0);
	    $query->setRows($this->getRows());
	    $query->createFilterQuery('profile_type')->setQuery('profile_type:'.$profile_type);
	    //$query->createFilterQuery('active')->setQuery('active: 1');
	    $query->setInterestingTerms("list");
	    
	    $aStopWords = array("i", "me", "my", "myself", "we", "our", "ours", "ourselves", "you", "your", "yours", "yourself", "yourselves", "he", "him", "his", "himself", "she", "her", "hers", "herself", "it", "its", "itself", "they", "them", "their", "theirs", "themselves", "what", "which", "who", "whom", "this", "that", "these", "those", "am", "is", "are", "was", "were", "be", "been", "being", "have", "has", "had", "having", "do", "does", "did", "doing", "a", "an", "the", "and", "but", "if", "or", "because", "as", "until", "while", "of", "at", "by", "for", "with", "about", "against", "between", "into", "through", "during", "before", "after", "above", "below", "to", "from", "up", "down", "in", "out", "on", "off", "over", "under", "again", "further", "then", "once", "here", "there", "when", "where", "why", "how", "all", "any", "both", "each", "few", "more", "most", "other", "some", "such", "no", "nor", "not", "only", "own", "same", "so", "than", "too", "very", "s", "t", "can", "will", "just", "don", "should", "now", "nearly", "get");
	    
	    // this executes the query and returns the result#
	    $request = $this->client->createRequest($query);
	    $requestInfo = (string)$request;
	    
	    try {
	        // this executes the query and returns the result
	        $resultset = $this->client->select($query);
	        
	        $aAllKeywords = $resultset->getInterestingTerms();

	        return $aKeywords = array_diff($aAllKeywords, $aStopWords);
	        
	    } catch(Exception $e) {
	        throw $e;
	    }
	    
	    
	    $aResult = array();
	    
	    if ($resultset->getNumFound() >= 1) {
	        foreach ($resultset as $document) {
	            $this->aId[] = $document->profile_id;
	        }
	    }
	}

	function getCompanyByArticle($id) {


		// Solarium_Query_MoreLikeThis

	    
		// get a select query instance
		$query = $this->client->createMoreLikeThis();


		// add a query and morelikethis settings (using fluent interface)
		$query->setQuery('id:'.$id)
		->getMoreLikeThis()
		->setFields('text')
		->setMinimumDocumentFrequency(1)
		->setMinimumTermFrequency(2);

		$query->setFields(array('profile_id'));
		$query->setStart(0);
		$query->setRows($this->getRows());
		$query->createFilterQuery('profile_type')->setQuery('profile_type:0');
		//$query->createFilterQuery('active')->setQuery('active: 1');
		$query->setInterestingTerms("list");
		

		// this executes the query and returns the result#
		$request = $this->client->createRequest($query);
		$requestInfo = (string)$request;

		try {
			// this executes the query and returns the result
			$resultset = $this->client->select($query);
			
			print_r("<pre>");
			print_r($resultset);
			print_r("</pre>");
			die();

		} catch(Exception $e) {
			throw $e;
		}


		$aResult = array();

		if ($resultset->getNumFound() >= 1) {
			foreach ($resultset as $document) {
				$this->aId[] = $document->profile_id;
			}
		}
	}

	function getPlacementsByArticle($id) {


		// Solarium_Query_MoreLikeThis

		// get a select query instance
		$query = $this->client->createMoreLikeThis();


		// add a query and morelikethis settings (using fluent interface)
		$query->setQuery('id:'.$id)
		->getMoreLikeThis()
		->setFields('text')
		->setMinimumDocumentFrequency(1)
		->setMinimumTermFrequency(2);

		$query->setFields(array('profile_id'));
		$query->setStart(0);
		$query->setRows($this->getRows());
		$query->createFilterQuery('profile_type')->setQuery('profile_type:1');
		$query->createFilterQuery('active')->setQuery('active: 1');

		// this executes the query and returns the result#
		$request = $this->client->createRequest($query);
		$requestInfo = (string)$request;

		Logger::DB(2,"API SOLR Query: ".$requestInfo);

		try {
			// this executes the query and returns the result
			$resultset = $this->client->select($query);
		} catch(Exception $e) {
			throw $e;
		}


		$aResult = array();

		if ($resultset->getNumFound() >= 1) {
			foreach ($resultset as $document) {
				$this->aId[] = array('profile_id' => $document->profile_id,'profile_type' => 1);
			}
		}

	}

	function getPlacementsByArticleBalanced($id, $field = 'text') {


		// Solarium_Query_MoreLikeThis

		// get a select query instance
		$query = $this->client->createMoreLikeThis();


		// add a query and morelikethis settings (using fluent interface)
		$query->setQuery('id:'.$id)
		->getMoreLikeThis()
		->setFields($field)
		->setMinimumDocumentFrequency(1)
		->setMinimumTermFrequency(1);

		$query->setFields(array('profile_id','profile_type','company_id'));
		$query->setStart(0);
		$query->setRows($this->getRows());
		$query->createFilterQuery('profile_type')->setQuery('profile_type:1');
		$query->createFilterQuery('active')->setQuery('active: 1');

		// this executes the query and returns the result#
		$request = $this->client->createRequest($query);
		$requestInfo = (string)$request;

		Logger::DB(2,"API SOLR Query: ".$requestInfo);

		try {
			// this executes the query and returns the result
			$resultset = $this->client->select($query);
		} catch(Exception $e) {
			throw $e;
		}

		$aResult = array();

		/*
		if ($resultset->getNumFound() >= 1) {
			foreach ($resultset as $document) {
				$this->aId[] = array('profile_id' => $document->profile_id,'profile_type' => 1);
			}
		}
		*/


		foreach($resultset as $doc) {
			$aResult[$doc->company_id][] = $doc->profile_id;
		}

		// reindex the array so placement keys for each company are a sequential numeric index
		$aIdIndexedNumeric = array();
		$i = 0;
		foreach($aResult as $company_id => $aPlacementId) {
			$aId[$i++] = $aPlacementId;
		}


		$oBalancedDistributor = new BalancedDistributor($aId);
		$oBalancedDistributor->SetFetchSize($this->getRows());
		$oBalancedDistributor->SetStartIdx(0);
		$iTotalResults = $oBalancedDistributor->GetTotalElements();

		$aId = $oBalancedDistributor->Fetch($this->getRows());

		foreach($aId as $id) {
			$this->aId[] = array('profile_id' => $id,'profile_type' => 1);
		}
	}

	// Get Related content
	function getRelatedProfile($solr_id,$profile_type = "1", $arrFilterQuery = array()) {

	    if (!is_numeric($solr_id)) return FALSE;

		// Solarium_Query_MoreLikeThis

		$query = $this->client->createMoreLikeThis();

		// add a query and morelikethis settings (using fluent interface)
		$query->setQuery('id:'.$solr_id)
		->getMoreLikeThis()
		->setFields('text')
		->setMinimumDocumentFrequency(1)
		->setMinimumTermFrequency(2);

		$query->setFields(array('profile_id'));
		$query->setStart(0);
		$query->setRows($this->getRows());
		$query->createFilterQuery('profile_type')->setQuery('profile_type:'.$profile_type);
		$query->createFilterQuery('active')->setQuery('active: 1');

		// exclude placements associated with company being viewed
		//$query->createFilterQuery('company_id')->setQuery('-company_id:'.$company_id);
		if (is_array($arrFilterQuery))
		{
		    foreach($arrFilterQuery as $key => $value)
		    {
		        $query->createFilterQuery($key)->setQuery($key.":".$value);
		    }
		}

		// this executes the query and returns the result#
		$request = $this->client->createRequest($query);
		$requestInfo = (string)$request;

		Logger::DB(2,"API SOLR Query: ".$requestInfo);

		try {
			// this executes the query and returns the result
			$resultset = $this->client->select($query);
		} catch(Exception $e) {
			throw $e;
		}

		$aResult = array();

		if ($resultset->getNumFound() >= 1) {
			foreach ($resultset as $document) {
				$profile_type = PlacementProfile::GetTypeById($document->profile_id);
				$this->aId[] = array('profile_id' => $document->profile_id,'profile_type' => $profile_type);
			}
		}

	}

	/**
	 * Article - find related articles
	 *
	 * @param int article id
	 * @param array $arrFilterQuery
	 * @throws Exception
	 * @return boolean
	 */
	function getRelatedArticle($solr_id, $arrFilterQuery = array()) {

	    if (!is_numeric($solr_id)) return FALSE;

	    // Solarium_Query_MoreLikeThis

	    // get a select query instance
	    $query = $this->client->createMoreLikeThis();


	    // add a query and morelikethis settings (using fluent interface)
	    $query->setQuery('id:'.$solr_id)
	    ->getMoreLikeThis()
	    ->setFields('text')
	    ->setMinimumDocumentFrequency(1)
	    ->setMinimumTermFrequency(2);

	    $query->setFields(array('profile_id', 'title'));
	    $query->setStart(0);
	    $query->setRows($this->getRows());
	    $query->createFilterQuery('profile_type')->setQuery('profile_type: 2');

	    if (is_array($arrFilterQuery))
	    {
	        foreach($arrFilterQuery as $key => $value)
	        {
	            $query->createFilterQuery($key)->setQuery($key.":".$value);
	        }
	    }

	    // this executes the query and returns the result
	    $request = $this->client->createRequest($query);
	    
	    /*
	    print_r("<pre>");
	    print_r($request);
	    print_r("</pre>");
	    die();
	    */
	    
	    $requestInfo = (string)$request;

	    Logger::DB(2,"API SOLR Query: ".$requestInfo);

	    try {
	        // this executes the query and returns the result
	        $resultset = $this->client->select($query);
	    } catch(Exception $e) {
	        throw $e;
	    }

	    $aResult = array();

	    if ($resultset->getNumFound() >= 1) {
	        foreach ($resultset as $document) {
	            $this->aId[] = $document->profile_id;
	        }
	    }
	    if (!is_array($this->aId)) return array();

	    $this->aId = array_unique($this->aId);

	    $arrResult = array();
        foreach($this->aId as $iArticleId)
        {
            $oArticle = new Article();
            $oArticle->SetFetchMode(FETCHMODE__FULL);
            $oArticle->SetFetchAttachedProfile(false);
            $oArticle->SetFetchAttachedArticle(false);
            $oArticle->GetById($iArticleId);

            if (strlen($oArticle->GetDescShort()) < 60) continue;
            if (!array_key_exists($oArticle->GetId(),$arrResult))
                $arrResult[$oArticle->GetId()] = $oArticle;
        }

        return $arrResult;
	}


}



?>
