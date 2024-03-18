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
    
        if (is_numeric($aRequest['company_id']))
        {
            return $this->GetReportByCompanyId($aRequest['company_id']);
        } elseif (is_numeric($aRequest['placement_id']))
        {
            return $this->GetReportByPlacementId($aRequest['placement_id']);
        } elseif (strlen($aRequest['article_keyword']) > 1)
        {
            return $this->GetReportByArticleKeyword($aRequest['article_keyword']);
        }
        


        $limit = $aRequest['page_size'];

        if (is_numeric($aRequest['page']))
        {
            $offset = $aRequest['page'] -1;
        } else {
            $offset = 0;
        }

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
                	JOIN image i ON m.img_id = i.id
                	JOIN article a ON m.link_id = a.id
                	WHERE m.link_to = 'ARTICLE'
                	ORDER BY a.id DESC
                	LIMIT ".$limit." OFFSET ".$offset."
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
                	JOIN image i ON m.img_id = i.id
                	JOIN company c ON m.link_id = c.id
                	WHERE m.link_to = 'COMPANY'
                	ORDER BY c.id DESC
                	LIMIT ".$limit." OFFSET ".$offset."
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
                	JOIN image i ON m.img_id = i.id
                    JOIN profile_hdr p ON m.link_id = p.id
                	JOIN company c ON p.company_id = c.id
                	WHERE m.link_to = 'PLACEMENT'
                	ORDER BY p.id DESC
                	LIMIT ".$limit." OFFSET ".$offset."
                ) q3
                ORDER BY img_id DESC
                ";
             
         $db->query($sql);

         return $aRows = $db->getRows();
    }

    public function GetReportByCompanyId($company_id)
    {
        global $db;

        $sql = "
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
                	JOIN image i ON m.img_id = i.id
                	JOIN company c ON m.link_id = c.id
                	WHERE m.link_to = 'COMPANY'
                    AND c.id = ".$company_id."
                ) q2
                UNION
                SELECT * FROM (
                	SELECT  
                	m.img_id,
                	m.link_to,
                	m.link_id,
                	c.title ||' : '||p.title as title,
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
                	JOIN image i ON m.img_id = i.id
                    JOIN profile_hdr p ON m.link_id = p.id
                	JOIN company c ON p.company_id = c.id
                	WHERE m.link_to = 'PLACEMENT'
                    AND c.id = ".$company_id."
                ) q3
                ORDER BY title ASC
            	";


        $db->query($sql);
        
        return $aRows = $db->getRows();    
    }

    public function GetReportByPlacementId($placement_id)
    {
        global $db;
        
        $sql = "
                	SELECT
                	m.img_id,
                	m.link_to,
                	m.link_id,
                	c.title ||' : '||p.title as title,
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
                	JOIN image i ON m.img_id = i.id
                    JOIN profile_hdr p ON m.link_id = p.id
                	JOIN company c ON p.company_id = c.id
                	WHERE m.link_to = 'PLACEMENT'
                    AND p.id = ".$placement_id."
                    ORDER BY title ASC
            	";

        $db->query($sql);
        
        return $aRows = $db->getRows();
    }

    public function GetReportByArticleKeyword($strKeyword)
    {
        global $db;

        $sql = "
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
                JOIN image i ON m.img_id = i.id
                JOIN article a ON m.link_id = a.id
                WHERE m.link_to = 'ARTICLE'
                AND LOWER(a.title) LIKE '%".strtolower($strKeyword)."%'
                ORDER BY a.id DESC";

        $db->query($sql);
        
        return $aRows = $db->getRows();
        
    }
    
    public function GetCompanyName()
    {
        global $db;
        
        $sql = "SELECT
                c.id,
                c.title,
                c.url_name
                FROM
                image_map m
                JOIN image i ON m.img_id = i.id
                JOIN company c ON m.link_id = c.id
                WHERE m.link_to = 'COMPANY'
                GROUP BY c.id, c.title, c.url_name
                ORDER BY c.title ASC";
        
        $db->query($sql);
        
        return $db->getRows();
    }

    public function GetPlacementName()
    {
        global $db;
        
        $sql = "SELECT
                p.id,
                c.title ||' : '||p.title as title,
                '/company/'||c.url_name||'/'||p.url_name as url_name
                FROM
                image_map m
                JOIN image i ON m.img_id = i.id
                JOIN profile_hdr p ON m.link_id = p.id
                JOIN company c ON p.company_id = c.id
                WHERE m.link_to = 'PLACEMENT'
                GROUP BY p.id, p.title, p.url_name, c.title, c.url_name
                ORDER BY c.title, p.title ASC";
        
        $db->query($sql);
        
        return $db->getRows();
    }

    public function GetCount()
    {
        global $db;
        
        $sql = "SELECT
                count(*)
                FROM
                image_map m
                JOIN image i ON m.img_id = i.id";

        return $db->getFirstCell($sql);
        
    }
    
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
    
}
