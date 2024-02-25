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


class LinkChecker
{
    protected $report_date;
    protected $delay;

    public function __construct() 
    {
        $this->delay = 1; // delay in secs
    }

    public function Setup()
    {
        global $db;
        
        print_r("Truncate DB table: link_status \n\n");
        
        $db->query("TRUNCATE TABLE link_status;");

    }

    public function Process()
    {
        try {
            
            $this->Setup();

            $this->GetCompanyLinkStatus();
            $this->GetPlacementLinkStatus();
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
        $sCmd = "curl --head ".$url;
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
            print_r("Processing: ".$aRow['url_name']."\n");

            if ($aRow['url'] != "" && $aRow['url'] != "http://")
            {
                $http_status = $this->GetLinkHTTPResponseStatus($aRow['url']);

                $this->writeRow($aRow['url'], $http_status, $aRow['url_name']);
            }
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
            print_r("Processing: ".$aRow['url_name']."\n");

            if ($aRow['url'] != "" && $aRow['url'] != "http://")
            {

                $http_status = $this->GetLinkHTTPResponseStatus($aRow['url']);
                
                $this->writeRow($aRow['url'], $http_status, $aRow['url_name']);
            }

            if ($aRow['apply_url'] != "" && $aRow['apply_url'] != "http://")
            {
                $http_status = $this->GetLinkHTTPResponseStatus($aRow['apply_url']);

                $this->writeRow($aRow['url'], $http_status, $aRow['url_name']);
            }
            
        }
        
    }
    
    public function GetArticleLinkStatus()
    {
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
        
    }

    
    public function writeRow($linkUrl, $httpStatus, $originUrl)
    {
        global $db;

        $sql  = "INSERT INTO link_status 
                (report_date,url,http_status,origin_url)
                VALUES
                ('".$this->GetReportDate()."','".$linkUrl."', '".$httpStatus."', '".$originUrl."')";
        
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

    public function GetReport()
    {
        global $db;

        $db->query("SELECT * FROM link_status");

        return $db->getRows();
    }
}