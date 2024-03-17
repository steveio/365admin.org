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


    public function GetReport($aRequest)
    {
        global $db;

        /*
        $sql = "SELECT  
                   m.img_id,
                   m.link_to,
                   m.link_id,
                   CASE 
                    WHEN m.link_to = 'ARTICLE' THEN (SELECT a.title FROM article a WHERE m.link_id = a.id )
                    WHEN m.link_to = 'COMPANY' THEN (SELECT c.title FROM company c WHERE m.link_id = c.id )
                    WHEN m.link_to = 'PLACEMENT' THEN (SELECT p.title FROM profile_hdr p WHERE m.link_id = p.id )
                   END as link_name,
                   CASE 
                    WHEN m.type = 0 THEN 'IMAGE'
                    WHEN m.type = 1 THEN 'LOGO'
                    WHEN m.type = 2 THEN 'PROMO'
                   END as image_type,
                   '/www/vhosts/oneworld365.org/htdocs/img/000/'||substring(i.id::text,1,2)||'/'||i.id||i.ext as filepath,
                   'http://www.oneworld365.org/img/000/'||substring(i.id::text,1,2)||'/'||i.id||i.ext as filename,
                   'http://www.oneworld365.org/img/000/'||substring(i.id::text,1,2)||'/'||i.id||'_mf'||i.ext as filename_mf,
                   i.*
                FROM  
                   image_map m 
                   LEFT JOIN image i ON m.img_id = i.id
                ORDER BY m.img_id DESC
                LIMIT 100";
         */

        
         $sql = "
SELECT * FROM (
	SELECT  
	m.img_id,
	m.link_to,
	m.link_id,
	a.title,
	(SELECT am.section_uri FROM article_map am WHERE a.id = am.article_id LIMIT 1) as url,
	CASE 
	WHEN m.type = 0 THEN 'IMAGE'
	WHEN m.type = 1 THEN 'LOGO'
	WHEN m.type = 2 THEN 'PROMO'
	END as image_type,
	'/img/000/'||substring(i.id::text,1,2)||'/' as filepath,
	i.*
	FROM  
	image_map m 
	LEFT JOIN image i ON m.img_id = i.id
	LEFT JOIN article a ON m.link_id = a.id
	WHERE m.link_to = 'ARTICLE'
	ORDER BY m.img_id DESC
	LIMIT 100
) q1
UNION
SELECT * FROM (
	SELECT  
	m.img_id,
	m.link_to,
	m.link_id,
	c.title,
	'/company/'||c.url_name as url,
	CASE 
	WHEN m.type = 0 THEN 'IMAGE'
	WHEN m.type = 1 THEN 'LOGO'
	WHEN m.type = 2 THEN 'PROMO'
	END as image_type,
	'/img/000/'||substring(i.id::text,1,2)||'/' as filepath,
	i.*
	FROM  
	image_map m 
	LEFT JOIN image i ON m.img_id = i.id
	LEFT JOIN company c ON m.link_id = c.id
	WHERE m.link_to = 'COMPANY'
	ORDER BY m.img_id DESC
	LIMIT 100
) q2
UNION
SELECT * FROM (
	SELECT  
	m.img_id,
	m.link_to,
	m.link_id,
	c.title,
	'/company/'||c.url_name||'/'||p.url_name as url,
	CASE 
	WHEN m.type = 0 THEN 'IMAGE'
	WHEN m.type = 1 THEN 'LOGO'
	WHEN m.type = 2 THEN 'PROMO'
	END as image_type,
	'/img/000/'||substring(i.id::text,1,2)||'/' as filepath,
	i.*
	FROM  
	image_map m 
	LEFT JOIN image i ON m.img_id = i.id
    LEFT JOIN profile_hdr p ON m.link_id = p.id
	LEFT JOIN company c ON p.company_id = c.id
	WHERE m.link_to = 'PLACEMENT'
	ORDER BY m.img_id DESC
	LIMIT 100
) q3
ORDER BY img_id DESC
";
             
         $db->query($sql);

         return $aRows = $db->getRows();
    }

}
