
DROP VIEW keyword_idx_view_1;
DROP VIEW keyword_idx_view_2;
DROP VIEW keyword_idx_view_3;
DROP VIEW keyword_idx_view_4;
DROP VIEW keyword_idx_view_5;
DROP VIEW keyword_idx_view_6;
DROP VIEW keyword_idx_view_7;


DROP VIEW company_view_1;
DROP VIEW company_view_2;
DROP VIEW company_view_3;
DROP VIEW company_view_4;
DROP VIEW company_view_5;
DROP VIEW company_view_6;
DROP VIEW company_view_7;


-- a possible new view for oneworld365
--CREATE VIEW company_view_0 AS
--   SELECT c.id, c.title, c.url, c.url_name, c.email, c.tel, c.fax, c.desc_short, c.desc_long, c.logo_url, c.img_url1, c.img_url2, c.img_url3, c.img_url4, c.logo_banner_url, c.address, c.job_info, c.apply_url, c."location", c.sub_type, c.sc_gender, c.staff_gender, c.prod_type, c.costs, c.keywords, c.duration, c.hits, c.added, c.last_updated, c.last_indexed, c.active, c.homepage, c.job_credits, c.status, c.video, c.enq_opt, c.prof_opt, c.profile_type, c.state_id, c.country_id FROM company c, comp_cat_map m WHERE (((m.category_id = 0) OR (m.category_id = 2)) AND (m.company_id = c.id));
--


CREATE VIEW company_view_1 AS
    SELECT c.id, c.title, c.url, c.url_name, c.email, c.tel, c.fax, c.desc_short, c.desc_long, c.logo_url, c.img_url1, c.img_url2, c.img_url3, c.img_url4, c.logo_banner_url, c.address, c.job_info, c.apply_url, c."location", c.sub_type, c.sc_gender, c.staff_gender, c.prod_type, c.costs, c.keywords, c.duration, c.hits, c.added, c.last_updated, c.last_indexed, c.active, c.homepage, c.job_credits, c.status, c.video, c.enq_opt, c.prof_opt, c.profile_type, c.state_id, c.country_id FROM company c WHERE (c.prod_type >= 1);


CREATE VIEW company_view_2 AS
    SELECT c.id, c.title, c.url, c.url_name, c.email, c.tel, c.fax, c.desc_short, c.desc_long, c.logo_url, c.img_url1, c.img_url2, c.img_url3, c.img_url4, c.logo_banner_url, c.address, c.job_info, c.apply_url, c."location", c.sub_type, c.sc_gender, c.staff_gender, c.prod_type, c.costs, c.keywords, c.duration, c.hits, c.added, c.last_updated, c.last_indexed, c.active, c.homepage, c.job_credits, c.status, c.video, c.enq_opt, c.prof_opt, c.profile_type, c.state_id, c.country_id FROM company c WHERE (c.id IN (SELECT DISTINCT c.id FROM company c, comp_cat_map m WHERE ((m.company_id = c.id) AND ((m.category_id = 1) OR (m.category_id = 6))) ORDER BY c.id));


CREATE VIEW company_view_3 AS
    SELECT c.id, c.title, c.url, c.url_name, c.email, c.tel, c.fax, c.desc_short, c.desc_long, c.logo_url, c.img_url1, c.img_url2, c.img_url3, c.img_url4, c.logo_banner_url, c.address, c.job_info, c.apply_url, c."location", c.sub_type, c.sc_gender, c.staff_gender, c.prod_type, c.costs, c.keywords, c.duration, c.hits, c.added, c.last_updated, c.last_indexed, c.active, c.homepage, c.job_credits, c.status, c.video, c.enq_opt, c.prof_opt, c.profile_type, c.state_id, c.country_id FROM company c, comp_cat_map m WHERE ((m.category_id = 3) AND (m.company_id = c.id));


CREATE VIEW company_view_4 AS
    SELECT c.id, c.title, c.url, c.url_name, c.email, c.tel, c.fax, c.desc_short, c.desc_long, c.logo_url, c.img_url1, c.img_url2, c.img_url3, c.img_url4, c.logo_banner_url, c.address, c.job_info, c.apply_url, c."location", c.sub_type, c.sc_gender, c.staff_gender, c.prod_type, c.costs, c.keywords, c.duration, c.hits, c.added, c.last_updated, c.last_indexed, c.active, c.homepage, c.job_credits, c.status, c.video, c.enq_opt, c.prof_opt, c.profile_type, c.state_id, c.country_id FROM company c, comp_cat_map m WHERE ((m.category_id = 4) AND (m.company_id = c.id));


CREATE VIEW company_view_5 AS
    SELECT c.id, c.title, c.url, c.url_name, c.email, c.tel, c.fax, c.desc_short, c.desc_long, c.logo_url, c.img_url1, c.img_url2, c.img_url3, c.img_url4, c.logo_banner_url, c.address, c.job_info, c.apply_url, c."location", c.sub_type, c.sc_gender, c.staff_gender, c.prod_type, c.costs, c.keywords, c.duration, c.hits, c.added, c.last_updated, c.last_indexed, c.active, c.homepage, c.job_credits, c.video, c.enq_opt, c.prof_opt, c.profile_type, c.state_id, c.country_id FROM company c, comp_cat_map m WHERE ((m.category_id = 2) AND (m.company_id = c.id)) UNION SELECT c.id, c.title, c.url, c.url_name, c.email, c.tel, c.fax, c.desc_short, c.desc_long, c.logo_url, c.img_url1, c.img_url2, c.img_url3, c.img_url4, c.logo_banner_url, c.address, c.job_info, c.apply_url, c."location", c.sub_type, c.sc_gender, c.staff_gender, c.prod_type, c.costs, c.keywords, c.duration, c.hits, c.added, c.last_updated, c.last_indexed, c.active, c.homepage, c.job_credits, c.video, c.enq_opt, c.prof_opt, c.profile_type, c.state_id, c.country_id FROM company c, comp_cat_map m WHERE ((m.category_id = 6) AND (m.company_id = c.id));


CREATE VIEW company_view_6 AS
    SELECT c.id, c.title, c.url, c.url_name, c.email, c.tel, c.fax, c.desc_short, c.desc_long, c.logo_url, c.img_url1, c.img_url2, c.img_url3, c.img_url4, c.logo_banner_url, c.address, c.job_info, c.apply_url, c."location", c.sub_type, c.sc_gender, c.staff_gender, c.prod_type, c.costs, c.keywords, c.duration, c.hits, c.added, c.last_updated, c.last_indexed, c.active, c.homepage, c.job_credits, c.status, c.video, c.enq_opt, c.prof_opt, c.profile_type, c.state_id, c.country_id FROM company c, comp_cat_map m WHERE ((m.category_id = 8) AND (m.company_id = c.id));


CREATE VIEW company_view_7 AS
    SELECT c.id, c.title, c.url, c.url_name, c.email, c.tel, c.fax, c.desc_short, c.desc_long, c.logo_url, c.img_url1, c.img_url2, c.img_url3, c.img_url4, c.logo_banner_url, c.address, c.job_info, c.apply_url, c."location", c.sub_type, c.sc_gender, c.staff_gender, c.prod_type, c.costs, c.keywords, c.duration, c.hits, c.added, c.last_updated, c.last_indexed, c.active, c.homepage, c.job_credits, c.video, c.enq_opt, c.prof_opt, c.profile_type, c.state_id, c.country_id FROM company c, comp_cat_map m WHERE ((m.category_id = 7) AND (m.company_id = c.id));





CREATE VIEW keyword_idx_view_1 AS
    SELECT k.id, k."type", k.word, k.count FROM keyword_idx_2 k, company_view_1 c WHERE ((k."type" = 1) AND (k.id = c.id)) UNION SELECT k.id, k."type", k.word, k.count FROM keyword_idx_2 k, company_view_1 c, profile_hdr p WHERE (((k."type" = 2) AND (k.id = p.id)) AND (p.company_id = c.id));


CREATE VIEW keyword_idx_view_2 AS
    SELECT k.id, k."type", k.word, k.count FROM keyword_idx_2 k, company_view_2 c WHERE ((k."type" = 1) AND (k.id = c.id)) UNION SELECT k.id, k."type", k.word, k.count FROM keyword_idx_2 k, company_view_2 c, profile_hdr p WHERE (((k."type" = 2) AND (k.id = p.id)) AND (p.company_id = c.id));


CREATE VIEW keyword_idx_view_3 AS
    SELECT k.id, k."type", k.word, k.count FROM keyword_idx_2 k, company_view_3 c WHERE ((k."type" = 1) AND (k.id = c.id)) UNION SELECT k.id, k."type", k.word, k.count FROM keyword_idx_2 k, company_view_3 c, profile_hdr p WHERE (((k."type" = 2) AND (k.id = p.id)) AND (p.company_id = c.id));


CREATE VIEW keyword_idx_view_4 AS
    SELECT k.id, k."type", k.word, k.count FROM keyword_idx_2 k, company_view_4 c WHERE ((k."type" = 1) AND (k.id = c.id)) UNION SELECT k.id, k."type", k.word, k.count FROM keyword_idx_2 k, company_view_4 c, profile_hdr p WHERE (((k."type" = 2) AND (k.id = p.id)) AND (p.company_id = c.id));


CREATE VIEW keyword_idx_view_5 AS
    SELECT k.id, k."type", k.word, k.count FROM keyword_idx_2 k, company_view_5 c WHERE ((k."type" = 1) AND (k.id = c.id)) UNION SELECT k.id, k."type", k.word, k.count FROM keyword_idx_2 k, company_view_5 c, profile_hdr p WHERE (((k."type" = 2) AND (k.id = p.id)) AND (p.company_id = c.id));


CREATE VIEW keyword_idx_view_6 AS
    SELECT k.id, k."type", k.word, k.count FROM keyword_idx_2 k, company_view_6 c, profile_hdr p WHERE (((k."type" = 2) AND (k.id = p.id)) AND (p.company_id = c.id));


CREATE VIEW keyword_idx_view_7 AS
    SELECT k.id, k."type", k.word, k.count FROM keyword_idx_2 k, company_view_7 c WHERE ((k."type" = 1) AND (k.id = c.id)) UNION SELECT k.id, k."type", k.word, k.count FROM keyword_idx_2 k, company_view_7 c, profile_hdr p WHERE (((k."type" = 2) AND (k.id = p.id)) AND (p.company_id = c.id));


