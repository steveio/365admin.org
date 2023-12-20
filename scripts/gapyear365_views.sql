DROP view keyword_idx_view_0;
DROP view keyword_idx_view_1;
DROP view placement_view_0;
DROP view placement_view_1;
DROP view company_view_0;
DROP view company_view_1;


CREATE VIEW company_view_0 AS
    SELECT c.id, c.title, c.url, c.url_name, c.email, c.tel, c.fax, c.desc_short, c.desc_long, c.logo_url, c.img_url1, c.img_url2, c.img_url3, c.img_url4, c.logo_banner_url, c.address, c.job_info, c.apply_url, c."location", c.sub_type, c.sc_gender, c.staff_gender, c.prod_type, c.costs, c.keywords, c.duration, c.hits, c.added, c.last_updated, c.last_indexed, c.last_indexed_solr, c.active, c.homepage, c.job_credits, c.status, c.video, c.enq_opt, c.prof_opt, c.profile_type, c.state_id, c.country_id, c.img_status, c.logo_refresh_fl 
FROM company c WHERE (c.id IN (SELECT DISTINCT c.id FROM company c, comp_cat_map m WHERE 
(
	(	(m.company_id = c.id) AND ((m.category_id = 0) OR (m.category_id = 2)) )
		 OR 
	(	( (m.company_id = c.id) AND ((m.category_id = 4) OR (m.category_id = 6)) ) AND (c.prod_type >= 1)  )
) 
ORDER BY c.id));


CREATE VIEW company_view_1 AS
    SELECT c.id, c.title, c.url, c.url_name, c.email, c.tel, c.fax, c.desc_short, c.desc_long, c.logo_url, c.img_url1, c.img_url2, c.img_url3, c.img_url4, c.logo_banner_url, c.address, c.job_info, c.apply_url, c."location", c.sub_type, c.sc_gender, c.staff_gender, c.prod_type, c.costs, c.keywords, c.duration, c.hits, c.added, c.last_updated, c.last_indexed, c.last_indexed_solr, c.active, c.homepage, c.job_credits, c.status, c.video, c.enq_opt, c.prof_opt, c.profile_type, c.state_id, c.country_id, c.img_status, c.logo_refresh_fl 
FROM company c WHERE (c.id IN (SELECT DISTINCT c.id FROM company c, comp_cat_map m WHERE 
(
	(m.category_id = 7) AND (m.company_id = c.id)
	OR
	(	( (m.company_id = c.id) AND ((m.category_id = 0) OR (m.category_id = 2)) ) AND (c.prod_type >= 1)  )

)
ORDER BY c.id));


CREATE VIEW placement_view_0 AS
    SELECT p.id, p."type", p.title, p.desc_short, p.desc_long, p.company_id, p."location", p.url, p.email, p.img_url1, p.img_url2, p.img_url3, p.img_url4, p.video1, p.ad_duration, p.ad_active, p.added, p.last_updated, p.last_indexed, p.last_indexed_solr, p.url_name, p.apply_url, p.keyword_exclude, p.added_by 
FROM profile_hdr p 
WHERE 
p.id IN (
SELECT DISTINCT p2.id FROM profile_hdr p2, prod_cat_map m WHERE ( (m.category_id = 0) OR (m.category_id = 2) OR (m.category_id = 4) OR (m.category_id = 6)) AND (m.prod_id = p2.id) ORDER BY p2.id
);


CREATE VIEW placement_view_1 AS
    SELECT p.id, p."type", p.title, p.desc_short, p.desc_long, p.company_id, p."location", p.url, p.email, p.img_url1, p.img_url2, p.img_url3, p.img_url4, p.video1, p.ad_duration, p.ad_active, p.added, p.last_updated, p.last_indexed, p.last_indexed_solr, p.url_name, p.apply_url, p.keyword_exclude, p.added_by 
FROM profile_hdr p 
WHERE 
p.id IN (
SELECT DISTINCT p2.id FROM profile_hdr p2, prod_cat_map m WHERE ( (m.category_id = 7) OR (m.category_id = 0) OR (m.category_id = 2)) AND (m.prod_id = p2.id)  ORDER BY p2.id
);



CREATE VIEW keyword_idx_view_0 AS
    SELECT k.id, k."type", k.word, k.count FROM keyword_idx_2 k, company_view_0 c WHERE ((k."type" = 1) AND (k.id = c.id)) UNION SELECT k.id, k."type", k.word, k.count FROM keyword_idx_2 k, company_view_0 c, placement_view_0 p WHERE (((k."type" = 2) AND (k.id = p.id)) AND (p.company_id = c.id));


CREATE VIEW keyword_idx_view_1 AS
    SELECT k.id, k."type", k.word, k.count FROM keyword_idx_2 k, company_view_1 c WHERE ((k."type" = 1) AND (k.id = c.id)) UNION SELECT k.id, k."type", k.word, k.count FROM keyword_idx_2 k, company_view_1 c, placement_view_1 p WHERE (((k."type" = 2) AND (k.id = p.id)) AND (p.company_id = c.id));


