<?php

/**
 * 
 * Link Checker - 
 * 
 * Script to run monthly via cron checking external and internal links 
 * and reporting on their status to highlight any broken links
 * 
 * 
 * 
 */

define("LINK_ORIGIN_COMPANY", 10);
define("LINK_ORIGIN_COMPANY_URL", 0);
define("LINK_ORIGIN_COMPANY_APPLY", 1);
define("LINK_ORIGIN_PLACEMENT", 11);
define("LINK_ORIGIN_PLACEMENT_URL", 3);
define("LINK_ORIGIN_PLACEMENT_APPLY", 4);


class LinkChecker
{
    protected $report_date;
    protected $delay;
    protected $idx;

    public function __construct() 
    {
        $this->delay = 1; // delay in secs
    }

    public function Setup()
    {
        global $db;
        
        $this->idx = 0;
        
        print_r("Truncate DB table: link_status \n\n");
        
        $db->query("TRUNCATE TABLE link_status;");

    }

    public function Process()
    {
        try {
            
            //$this->Setup();

            $this->GetPlacementLinkStatus();

            $this->GetCompanyLinkStatus();

            //$this->GetArticleLinkStatus();
            
        } catch(Exception $e) {
            print "\n\n";
            print $e->getMessage();
            print "\n";
            print $e->getTraceAsString();
            print "\n\n";
            die(__FILE__."::".__LINE__);
        }
    }

    protected function GetHTTPStatus($url)
    {
        $sCmd = "curl --head --location --max-time 30 --connect-timeout 10  ".$url;
        $aOut = array();
        
        print_r($sCmd."\n");
        
        exec($sCmd,$aOut);
        
        print_r($aOut);
        print_r("\n\n");
        
        return $aOut;
    }

    public function GetLinkHTTPResponseStatus($url)
    {

        $aOut = $this->GetHTTPStatus($url);

        if (substr_count($aOut[0], "301") >= 1 || substr_count($aOut[0], "302") >= 1)
        {
            
            print_r("HTTP STATUS: ".$aOut[0]."\n");

            $bRecursion++;
            
            foreach($aOut as $line)
            {
                if (preg_match("/^location/i", $line))
                {
                    $aBits = explode(" ", $line);
                    $redirectUrl = $aBits[1];
                    
                    print_r("LOCATION: ".$redirectUrl."\n");
                    
                    if ($bRecursion <= 3)
                    {
                        $aOut[0] = $this->GetLinkHTTPResponseStatus($redirectUrl);
                    } else {
                        $aOut[0] .= $aOut[0]." - Redirect recursion error\n";
                    }

                }
            }            
        }

        $bRecursion = 0;
        
        sleep($this->delay); 

        return $aOut[0];
        
    }

    public function GetCompanyLinkStatus()
    {
        global $db;

        $sql = "SELECT c.id, c.title, '/company/'||c.url_name as url_name, c.url, c.apply_url FROM company c ORDER BY id desc";
        
        $db->query($sql);
        
        $aRows = $db->getRows();
        
        foreach($aRows as $aRow)
        {
            print_r("Processing ( ".$this->idx." ): ".$aRow['url_name']."\n");
            
            if ($this->Processed($aRow['url_name'])) continue;

            if ($aRow['url'] != "" && $aRow['url'] != "http://")
            {
                $http_status = $this->GetLinkHTTPResponseStatus($aRow['url']);

                $this->writeRow($aRow['url'], $http_status, $aRow['url_name'], LINK_ORIGIN_COMPANY_URL);
            }

            if ($aRow['apply_url'] != "" && $aRow['apply_url'] != "http://")
            {
                $http_status = $this->GetLinkHTTPResponseStatus($aRow['url']);
                
                $this->writeRow($aRow['url'], $http_status, $aRow['url_name'], LINK_ORIGIN_COMPANY_APPLY);
            }
            
            $this->idx++;

        }
        
    }
    
    public function GetPlacementLinkStatus()
    {
        global $db;
        
        $sql = "SELECT p.id, p.title, '/company/'||c.url_name||'/'||p.url_name as url_name, p.url, p.apply_url  FROM profile_hdr p, company c WHERE p.company_id = c.id ORDER BY p.id desc";
        
        $db->query($sql);
        
        $aRows = $db->getRows();
        
        foreach($aRows as $aRow)
        {
            print_r("Processing: ( ".$this->idx." ) ".$aRow['url_name']."\n");

            if ($this->Processed($aRow['url_name'])) continue;
            
            if ($aRow['url'] != "" && $aRow['url'] != "http://")
            {

                $http_status = $this->GetLinkHTTPResponseStatus($aRow['url']);
                
                $this->writeRow($aRow['url'], $http_status, $aRow['url_name'], LINK_ORIGIN_PLACEMENT_URL);
            }

            if ($aRow['apply_url'] != "" && $aRow['apply_url'] != "http://")
            {
                $http_status = $this->GetLinkHTTPResponseStatus($aRow['apply_url']);

                $this->writeRow($aRow['url'], $http_status, $aRow['url_name'], LINK_ORIGIN_PLACEMENT_APPLY);
            }
            
            $this->idx++;
        }
        
    }
    
    public function GetArticleLinkStatus()
    {
        /*
        global $db;
        
        $sql = "SELECT a.id, a.title, a.full_desc, m.section_uri FROM article a, article_map m WHERE a.id = m.article_id ORDER BY m.section_uri desc";
        
        $db->query($sql);
        
        $aRows = $db->getRows();
        
        foreach($aRows as $aRow)
        {
            $html = $aRow['full_desc'];

            $aUrl = array();
            preg_match_all( '|<a.*?href=[\'"](.*?)[\'"].*?>|i',$html, $aUrl );
            
            if (count($aUrl[0]) >= 1)
            {
                foreach($aUrl[1] as $url)
                {
                    $this->GetLinkHTTPResponseStatus($url);
                }
            }
        }
        */
        
    }

    
    public function writeRow($linkUrl, $httpStatus, $originUrl, $originType)
    {
        global $db;

        $sql  = "INSERT INTO link_status 
                (report_date,url,http_status,origin_url,origin_type)
                VALUES
                ('".$this->GetReportDate()."','".$linkUrl."', '".$httpStatus."', '".$originUrl."', $originType)";
        
        $db->query($sql);

    }
    
    public function GetReportDate()
    {
        return $this->report_date;
    }

    public function SetReportDate($sDate)
    {
        $this->report_date = $sDate;
    }

    public function GetReport($aRequest)
    {
        global $db;

        $sql_constraint = "WHERE 1=1 ";

        if (is_array($aRequest) && count($aRequest) >= 1)
        {
            if (isset($aRequest['company_name']))
            {
                if ($aRequest['company_name'] != "ALL")
                {
                    $sql_constraint .= " AND origin_url like '/company/".$aRequest['company_name']."%'";
                }
            }

            if (isset($aRequest['origin_type']))
            {
                if ($aRequest['origin_type'] != "ALL")
                {
                    switch($aRequest['origin_type'])
                    {
                        case  LINK_ORIGIN_COMPANY:
                            $sql_constraint .= " AND origin_type IN (".LINK_ORIGIN_COMPANY_URL.",".LINK_ORIGIN_COMPANY_APPLY.")";
                            break;
                        case  LINK_ORIGIN_COMPANY_URL:
                            $sql_constraint .= " AND origin_type IN (".LINK_ORIGIN_COMPANY_URL.")";
                            break;
                        case  LINK_ORIGIN_COMPANY_APPLY:
                            $sql_constraint .= " AND origin_type IN (".LINK_ORIGIN_COMPANY_APPLY.")";
                            break;
                        case  LINK_ORIGIN_PLACEMENT:
                            $sql_constraint .= " AND origin_type IN (".LINK_ORIGIN_PLACEMENT_URL.",".LINK_ORIGIN_PLACEMENT_APPLY.")";
                            break;
                        case  LINK_ORIGIN_PLACEMENT_URL:
                            $sql_constraint .= " AND origin_type IN (".LINK_ORIGIN_PLACEMENT_URL.")";
                            break;
                        case  LINK_ORIGIN_PLACEMENT_APPLY:
                            $sql_constraint .= " AND origin_type IN (".LINK_ORIGIN_PLACEMENT_APPLY.")";
                            break;
                    }                    
                }
            }

            if (isset($aRequest['http_status']))
            {
                if ($aRequest['http_status'] != "ALL")
                {
                    switch($aRequest['http_status'])
                    {
                        case  "OK":
                            $sql_constraint .= " AND http_status LIKE '%200%'";
                            break;
                        case  "ERROR":
                            $sql_constraint .= " AND http_status NOT LIKE '%200%'";
                            break;
                    }
                }
            }
            
        }

        $sql = "SELECT * FROM link_status ".$sql_constraint;
        
        $db->query($sql);

        return $db->getRows();
    }
    
    public function GetHTTPStatusCode()
    {
        global $db;
        
        $db->query("SELECT distinct(http_status) as status FROM link_status");
        
        return $db->getRows();
    }
    
    public function GetCompanyName()
    {
        global $db;
        
        $db->query("select distinct(split_part(origin_url, '/', 3)) as company from link_status order by company asc;");
        
        return $db->getRows();
    }
    
    protected function Processed($url_name)
    {
        global $db;
        
        $db->query("SELECT 1 FROM link_status WHERE origin_url = '".$url_name."'");
        
        if ($db->getNumRows() >= 1) return true;
        
        return false;
    }
}