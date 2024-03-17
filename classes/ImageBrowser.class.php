<?php

/**
 * 
 * Image Browser - 
 * 
 * Provides an API to report uploaded images
 * based on content they are linked to
 * to facilitate image re-use
 * 
 * 
 */



class ImageBrowser
{
    protected $report_date;
    protected $idx;

    public function __construct() 
    {
    }

    public function Setup()
    {
        $this->idx = 0;

    }

    public function Process()
    {
        try {
            
            
        } catch(Exception $e) {
            print "\n\n";
            print $e->getMessage();
            print "\n";
            print $e->getTraceAsString();
            print "\n\n";
            die(__FILE__."::".__LINE__);
        }
    }


    public function SetReportDate($sDate)
    {
        $this->report_date = $sDate;
    }

    public function GetReport($aRequest)
    {
        global $db;

    }

}
