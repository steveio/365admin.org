
DROP VIEW keyword_idx_view_0;
DROP VIEW keyword_idx_view_1;
DROP VIEW keyword_idx_view_2;
DROP VIEW keyword_idx_view_3;
DROP VIEW keyword_idx_view_4;
DROP VIEW keyword_idx_view_5;
DROP VIEW keyword_idx_view_6;
DROP VIEW keyword_idx_view_7;


DROP VIEW placement_view_0;
DROP VIEW placement_view_1;
DROP VIEW placement_view_2;
DROP VIEW placement_view_3;
DROP VIEW placement_view_4;
DROP VIEW placement_view_6;
DROP VIEW placement_view_7;


DROP VIEW company_view_0;
DROP VIEW company_view_1;
DROP VIEW company_view_2;
DROP VIEW company_view_3;
DROP VIEW company_view_4;
DROP VIEW company_view_5;
DROP VIEW company_view_6;
DROP VIEW company_view_7;


--
-- Name: company_view_0; Type: VIEW; Schema: public; Owner: postgres
--

CREATE VIEW company_view_0 AS
    SELECT c.id, c.title, c.url, c.url_name, c.email, c.tel, c.fax, c.desc_short, c.desc_long, c.logo_url, c.img_url1, c.img_url2, c.img_url3, c.img_url4, c.logo_banner_url, c.address, c.job_info, c.apply_url, c."location", c.sub_type, c.sc_gender, c.staff_gender, c.prod_type, c.costs, c.keywords, c.duration, c.hits, c.added, c.last_updated, c.last_indexed,c.last_indexed_solr, c.active, c.homepage, c.job_credits, c.status, c.video, c.enq_opt, c.prof_opt, c.profile_type, c.state_id, c.country_id, c.img_status, c.logo_refresh_fl FROM company c WHERE (c.id IN (SELECT DISTINCT c.id FROM company c, comp_cat_map m WHERE (((m.company_id = c.id) AND ((m.category_id = 0) OR (m.category_id = 2))) OR (((m.company_id = c.id) AND ((m.category_id = 4) OR (m.category_id = 6))) AND (c.prod_type >= 1))) ORDER BY c.id));


ALTER TABLE public.company_view_0 OWNER TO postgres;

--
-- Name: company_view_1; Type: VIEW; Schema: public; Owner: postgres
--

CREATE VIEW company_view_1 AS
    SELECT c.id, c.title, c.url, c.url_name, c.email, c.tel, c.fax, c.desc_short, c.desc_long, c.logo_url, c.img_url1, c.img_url2, c.img_url3, c.img_url4, c.logo_banner_url, c.address, c.job_info, c.apply_url, c."location", c.sub_type, c.sc_gender, c.staff_gender, c.prod_type, c.costs, c.keywords, c.duration, c.hits, c.added, c.last_updated, c.last_indexed, c.last_indexed_solr, c.active, c.homepage, c.job_credits, c.status, c.video, c.enq_opt, c.prof_opt, c.profile_type, c.state_id, c.country_id, c.img_status, c.logo_refresh_fl FROM company c, comp_cat_map m WHERE ((m.category_id = 7) AND (m.company_id = c.id));


ALTER TABLE public.company_view_1 OWNER TO postgres;

--
-- Name: company_view_2; Type: VIEW; Schema: public; Owner: postgres
--

CREATE VIEW company_view_2 AS
    SELECT c.id, c.title, c.url, c.url_name, c.email, c.tel, c.fax, c.desc_short, c.desc_long, c.logo_url, c.img_url1, c.img_url2, c.img_url3, c.img_url4, c.logo_banner_url, c.address, c.job_info, c.apply_url, c."location", c.sub_type, c.sc_gender, c.staff_gender, c.prod_type, c.costs, c.keywords, c.duration, c.hits, c.added, c.last_updated, c.last_indexed, c.last_indexed_solr, c.active, c.homepage, c.job_credits, c.status, c.video, c.enq_opt, c.prof_opt, c.profile_type, c.state_id, c.country_id FROM company c WHERE (c.id IN (SELECT DISTINCT c.id FROM company c, comp_cat_map m WHERE ((m.company_id = c.id) AND (m.category_id = 6)) ORDER BY c.id));


ALTER TABLE public.company_view_2 OWNER TO postgres;

--
-- Name: company_view_3; Type: VIEW; Schema: public; Owner: postgres
--

CREATE VIEW company_view_3 AS
    SELECT c.id, c.title, c.url, c.url_name, c.email, c.tel, c.fax, c.desc_short, c.desc_long, c.logo_url, c.img_url1, c.img_url2, c.img_url3, c.img_url4, c.logo_banner_url, c.address, c.job_info, c.apply_url, c."location", c.sub_type, c.sc_gender, c.staff_gender, c.prod_type, c.costs, c.keywords, c.duration, c.hits, c.added, c.last_updated, c.last_indexed, c.last_indexed_solr, c.active, c.homepage, c.job_credits, c.status, c.video, c.enq_opt, c.prof_opt, c.profile_type, c.state_id, c.country_id FROM company c, comp_cat_map m WHERE ((m.category_id = 3) AND (m.company_id = c.id));


ALTER TABLE public.company_view_3 OWNER TO postgres;

--
-- Name: company_view_4; Type: VIEW; Schema: public; Owner: postgres
--

CREATE VIEW company_view_4 AS
    SELECT c.id, c.title, c.url, c.url_name, c.email, c.tel, c.fax, c.desc_short, c.desc_long, c.logo_url, c.img_url1, c.img_url2, c.img_url3, c.img_url4, c.logo_banner_url, c.address, c.job_info, c.apply_url, c."location", c.sub_type, c.sc_gender, c.staff_gender, c.prod_type, c.costs, c.keywords, c.duration, c.hits, c.added, c.last_updated, c.last_indexed, c.last_indexed_solr, c.active, c.homepage, c.job_credits, c.status, c.video, c.enq_opt, c.prof_opt, c.profile_type, c.state_id, c.country_id FROM company c, comp_cat_map m WHERE ((m.category_id = 4) AND (m.company_id = c.id));


ALTER TABLE public.company_view_4 OWNER TO postgres;

--
-- Name: company_view_5; Type: VIEW; Schema: public; Owner: postgres
--

CREATE VIEW company_view_5 AS
    SELECT c.id, c.title, c.url, c.url_name, c.email, c.tel, c.fax, c.desc_short, c.desc_long, c.logo_url, c.img_url1, c.img_url2, c.img_url3, c.img_url4, c.logo_banner_url, c.address, c.job_info, c.apply_url, c."location", c.sub_type, c.sc_gender, c.staff_gender, c.prod_type, c.costs, c.keywords, c.duration, c.hits, c.added, c.last_updated, c.last_indexed, c.last_indexed_solr, c.active, c.homepage, c.job_credits, c.video, c.enq_opt, c.prof_opt, c.profile_type, c.state_id, c.country_id FROM company c, comp_cat_map m WHERE ((m.category_id = 2) AND (m.company_id = c.id)) UNION SELECT c.id, c.title, c.url, c.url_name, c.email, c.tel, c.fax, c.desc_short, c.desc_long, c.logo_url, c.img_url1, c.img_url2, c.img_url3, c.img_url4, c.logo_banner_url, c.address, c.job_info, c.apply_url, c."location", c.sub_type, c.sc_gender, c.staff_gender, c.prod_type, c.costs, c.keywords, c.duration, c.hits, c.added, c.last_updated, c.last_indexed, c.last_indexed_solr, c.active, c.homepage, c.job_credits, c.video, c.enq_opt, c.prof_opt, c.profile_type, c.state_id, c.country_id FROM company c, comp_cat_map m WHERE ((m.category_id = 6) AND (m.company_id = c.id));


ALTER TABLE public.company_view_5 OWNER TO postgres;

--
-- Name: company_view_6; Type: VIEW; Schema: public; Owner: postgres
--

CREATE VIEW company_view_6 AS
    SELECT c.id, c.title, c.url, c.url_name, c.email, c.tel, c.fax, c.desc_short, c.desc_long, c.logo_url, c.img_url1, c.img_url2, c.img_url3, c.img_url4, c.logo_banner_url, c.address, c.job_info, c.apply_url, c."location", c.sub_type, c.sc_gender, c.staff_gender, c.prod_type, c.costs, c.keywords, c.duration, c.hits, c.added, c.last_updated, c.last_indexed, c.last_indexed_solr, c.active, c.homepage, c.job_credits, c.status, c.video, c.enq_opt, c.prof_opt, c.profile_type, c.state_id, c.country_id FROM company c, comp_cat_map m WHERE ((m.category_id = 8) AND (m.company_id = c.id));


ALTER TABLE public.company_view_6 OWNER TO postgres;

--
-- Name: company_view_7; Type: VIEW; Schema: public; Owner: postgres
--

CREATE VIEW company_view_7 AS
    SELECT c.id, c.title, c.url, c.url_name, c.email, c.tel, c.fax, c.desc_short, c.desc_long, c.logo_url, c.img_url1, c.img_url2, c.img_url3, c.img_url4, c.logo_banner_url, c.address, c.job_info, c.apply_url, c."location", c.sub_type, c.sc_gender, c.staff_gender, c.prod_type, c.costs, c.keywords, c.duration, c.hits, c.added, c.last_updated, c.last_indexed, c.last_indexed_solr, c.active, c.homepage, c.job_credits, c.video, c.enq_opt, c.prof_opt, c.profile_type, c.state_id, c.country_id FROM company c, comp_cat_map m WHERE ((m.category_id = 7) AND (m.company_id = c.id));


ALTER TABLE public.company_view_7 OWNER TO postgres;


--
-- Name: placement_view_1; Type: VIEW; Schema: public; Owner: postgres
--

CREATE VIEW placement_view_1 AS
    SELECT p.id, p."type", p.title, p.desc_short, p.desc_long, p.company_id, p."location", p.url, p.email, p.img_url1, p.img_url2, p.img_url3, p.img_url4, p.video1, p.ad_duration, p.ad_active, p.added, p.last_updated, p.last_indexed, p.last_indexed_solr, p.url_name, p.apply_url, p.keyword_exclude, p.added_by FROM profile_hdr p, prod_cat_map m WHERE ((m.category_id = 7) AND (m.prod_id = p.id));


ALTER TABLE public.placement_view_1 OWNER TO postgres;

--
-- Name: placement_view_0; Type: VIEW; Schema: public; Owner: postgres
--

CREATE VIEW placement_view_0 AS
    SELECT p.id, p."type", p.title, p.desc_short, p.desc_long, p.company_id, p."location", p.url, p.email, p.img_url1, p.img_url2, p.img_url3, p.img_url4, p.video1, p.ad_duration, p.ad_active, p.added, p.last_updated, p.last_indexed, p.last_indexed_solr, p.url_name, p.apply_url, p.keyword_exclude, p.added_by FROM profile_hdr p WHERE (p.id IN (SELECT DISTINCT p2.id FROM profile_hdr p2, prod_cat_map m WHERE (((((m.category_id = 0) OR (m.category_id = 2)) OR (m.category_id = 4)) OR (m.category_id = 6)) AND (m.prod_id = p2.id)) ORDER BY p2.id));


ALTER TABLE public.placement_view_0 OWNER TO postgres;

--
-- Name: placement_view_2; Type: VIEW; Schema: public; Owner: postgres
--

CREATE VIEW placement_view_2 AS
    SELECT p.id, p."type", p.title, p.desc_short, p.desc_long, p.company_id, p."location", p.url, p.email, p.img_url1, p.img_url2, p.img_url3, p.img_url4, p.video1, p.ad_duration, p.ad_active, p.added, p.last_updated, p.last_indexed, p.last_indexed_solr, p.url_name, p.apply_url, p.keyword_exclude, p.added_by FROM profile_hdr p WHERE (p.id IN (SELECT DISTINCT ph.id FROM profile_hdr ph, prod_cat_map m WHERE ((m.category_id = 6) AND (m.prod_id = ph.id)) ORDER BY ph.id));


ALTER TABLE public.placement_view_2 OWNER TO postgres;

--
-- Name: placement_view_3; Type: VIEW; Schema: public; Owner: postgres
--

CREATE VIEW placement_view_3 AS
    SELECT p.id, p."type", p.title, p.desc_short, p.desc_long, p.company_id, p."location", p.url, p.email, p.img_url1, p.img_url2, p.img_url3, p.img_url4, p.video1, p.ad_duration, p.ad_active, p.added, p.last_updated, p.last_indexed, p.last_indexed_solr, p.url_name, p.apply_url, p.keyword_exclude, p.added_by FROM profile_hdr p, prod_cat_map m WHERE ((m.category_id = 3) AND (m.prod_id = p.id));


ALTER TABLE public.placement_view_3 OWNER TO postgres;

--
-- Name: placement_view_4; Type: VIEW; Schema: public; Owner: postgres
--

CREATE VIEW placement_view_4 AS
    SELECT p.id, p."type", p.title, p.desc_short, p.desc_long, p.company_id, p."location", p.url, p.email, p.img_url1, p.img_url2, p.img_url3, p.img_url4, p.video1, p.ad_duration, p.ad_active, p.added, p.last_updated, p.last_indexed, p.last_indexed_solr, p.url_name, p.apply_url, p.keyword_exclude, p.added_by FROM profile_hdr p, prod_cat_map m WHERE ((m.category_id = 4) AND (m.prod_id = p.id));


ALTER TABLE public.placement_view_4 OWNER TO postgres;

--
-- Name: placement_view_6; Type: VIEW; Schema: public; Owner: postgres
--

CREATE VIEW placement_view_6 AS
    SELECT p.id, p."type", p.title, p.desc_short, p.desc_long, p.company_id, p."location", p.url, p.email, p.img_url1, p.img_url2, p.img_url3, p.img_url4, p.video1, p.ad_duration, p.ad_active, p.added, p.last_updated, p.last_indexed, p.last_indexed_solr, p.url_name, p.apply_url, p.keyword_exclude, p.added_by FROM profile_hdr p, prod_cat_map m WHERE ((m.category_id = 8) AND (m.prod_id = p.id));


ALTER TABLE public.placement_view_6 OWNER TO postgres;

--
-- Name: placement_view_7; Type: VIEW; Schema: public; Owner: postgres
--

CREATE VIEW placement_view_7 AS
    SELECT p.id, p."type", p.title, p.desc_short, p.desc_long, p.company_id, p."location", p.url, p.email, p.img_url1, p.img_url2, p.img_url3, p.img_url4, p.video1, p.ad_duration, p.ad_active, p.added, p.last_updated, p.last_indexed, p.last_indexed_solr, p.url_name, p.apply_url, p.keyword_exclude, p.added_by FROM profile_hdr p, prod_cat_map m WHERE ((m.category_id = 7) AND (m.prod_id = p.id));


ALTER TABLE public.placement_view_7 OWNER TO postgres;



--
-- Name: keyword_idx_view_0; Type: VIEW; Schema: public; Owner: postgres
--

CREATE VIEW keyword_idx_view_0 AS
    SELECT k.id, k."type", k.word, k.count FROM keyword_idx_2 k, company_view_1 c WHERE ((k."type" = 1) AND (k.id = c.id)) UNION SELECT k.id, k."type", k.word, k.count FROM keyword_idx_2 k, company_view_1 c, placement_view_1 p WHERE (((k."type" = 2) AND (k.id = p.id)) AND (p.company_id = c.id));


ALTER TABLE public.keyword_idx_view_0 OWNER TO postgres;

--
-- Name: keyword_idx_view_1; Type: VIEW; Schema: public; Owner: postgres
--

CREATE VIEW keyword_idx_view_1 AS
    SELECT k.id, k."type", k.word, k.count FROM keyword_idx_2 k, company_view_1 c WHERE ((k."type" = 1) AND (k.id = c.id)) UNION SELECT k.id, k."type", k.word, k.count FROM keyword_idx_2 k, company_view_1 c, placement_view_1 p WHERE (((k."type" = 2) AND (k.id = p.id)) AND (p.company_id = c.id));


ALTER TABLE public.keyword_idx_view_1 OWNER TO postgres;

--
-- Name: keyword_idx_view_2; Type: VIEW; Schema: public; Owner: postgres
--

CREATE VIEW keyword_idx_view_2 AS
    SELECT k.id, k."type", k.word, k.count FROM keyword_idx_2 k, company_view_2 c WHERE ((k."type" = 1) AND (k.id = c.id)) UNION SELECT k.id, k."type", k.word, k.count FROM keyword_idx_2 k, company_view_2 c, profile_hdr p WHERE (((k."type" = 2) AND (k.id = p.id)) AND (p.company_id = c.id));


ALTER TABLE public.keyword_idx_view_2 OWNER TO postgres;

--
-- Name: keyword_idx_view_3; Type: VIEW; Schema: public; Owner: postgres
--

CREATE VIEW keyword_idx_view_3 AS
    SELECT k.id, k."type", k.word, k.count FROM keyword_idx_2 k, company_view_3 c WHERE ((k."type" = 1) AND (k.id = c.id)) UNION SELECT k.id, k."type", k.word, k.count FROM keyword_idx_2 k, company_view_3 c, profile_hdr p WHERE (((k."type" = 2) AND (k.id = p.id)) AND (p.company_id = c.id));


ALTER TABLE public.keyword_idx_view_3 OWNER TO postgres;

--
-- Name: keyword_idx_view_4; Type: VIEW; Schema: public; Owner: postgres
--

CREATE VIEW keyword_idx_view_4 AS
    SELECT k.id, k."type", k.word, k.count FROM keyword_idx_2 k, company_view_4 c WHERE ((k."type" = 1) AND (k.id = c.id)) UNION SELECT k.id, k."type", k.word, k.count FROM keyword_idx_2 k, company_view_4 c, profile_hdr p WHERE (((k."type" = 2) AND (k.id = p.id)) AND (p.company_id = c.id));


ALTER TABLE public.keyword_idx_view_4 OWNER TO postgres;

--
-- Name: keyword_idx_view_5; Type: VIEW; Schema: public; Owner: postgres
--

CREATE VIEW keyword_idx_view_5 AS
    SELECT k.id, k."type", k.word, k.count FROM keyword_idx_2 k, company_view_5 c WHERE ((k."type" = 1) AND (k.id = c.id)) UNION SELECT k.id, k."type", k.word, k.count FROM keyword_idx_2 k, company_view_5 c, profile_hdr p WHERE (((k."type" = 2) AND (k.id = p.id)) AND (p.company_id = c.id));


ALTER TABLE public.keyword_idx_view_5 OWNER TO postgres;

--
-- Name: keyword_idx_view_6; Type: VIEW; Schema: public; Owner: postgres
--

CREATE VIEW keyword_idx_view_6 AS
    SELECT k.id, k."type", k.word, k.count FROM keyword_idx_2 k, company_view_6 c, profile_hdr p WHERE (((k."type" = 2) AND (k.id = p.id)) AND (p.company_id = c.id));


ALTER TABLE public.keyword_idx_view_6 OWNER TO postgres;

--
-- Name: keyword_idx_view_7; Type: VIEW; Schema: public; Owner: postgres
--

CREATE VIEW keyword_idx_view_7 AS
    SELECT k.id, k."type", k.word, k.count FROM keyword_idx_2 k, company_view_7 c WHERE ((k."type" = 1) AND (k.id = c.id)) UNION SELECT k.id, k."type", k.word, k.count FROM keyword_idx_2 k, company_view_7 c, profile_hdr p WHERE (((k."type" = 2) AND (k.id = p.id)) AND (p.company_id = c.id));


ALTER TABLE public.keyword_idx_view_7 OWNER TO postgres;

