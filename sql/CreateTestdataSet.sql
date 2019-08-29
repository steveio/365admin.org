--
-- CreateTestDataSet.sql 
-- Create a stripped down testdata set from a production DB clone
--
-- TestDB instance: oneworld365_20120123
--
-- Companies in testdata set -
--
--		BUNAC			4
--		Camp Tall Timbers	2723
--		Active assistance	7612
--		Trek america		14002
--		Greenforce		3143
--		Camp America NZ		14512
--		Camp Augusta		8330
--		Bhejane Nature Training	8816
--
-- /usr/bin/pg_dump -U postgres -C -s oneworld365_20120123 | gzip -f > oneworld365_testdb_schema.gz
-- /usr/bin/pg_dump -U postgres -Fp --data-only -a oneworld365_20120123 | gzip -f > oneworld365_testdb_data.gz
--
--

DELETE FROM keyword_idx_2 WHERE type = 1 AND id NOT IN (4,2723,7612,14002,3143,14512,8330,8816);

DELETE FROM comp_cat_map WHERE company_id NOT IN (4,2723,7612,14002,3143,14512,8330,8816);

DELETE FROM comp_act_map WHERE company_id NOT IN (4,2723,7612,14002,3143,14512,8330,8816);

DELETE FROM comp_country_map WHERE company_id NOT IN (4,2723,7612,14002,3143,14512,8330,8816);

DELETE FROM company WHERE id NOT IN (4,2723,7612,14002,3143,14512,8330,8816);

-- placements

DELETE FROM prod_cat_map WHERE prod_id NOT IN (
	SELECT id FROM profile_hdr WHERE company_id IN (4,2723,7612,14002,3143,14512,8330,8816)
);

DELETE FROM prod_act_map WHERE prod_id NOT IN (
	SELECT id FROM profile_hdr WHERE company_id IN (4,2723,7612,14002,3143,14512,8330,8816)
);

DELETE FROM prod_country_map WHERE prod_id NOT IN (
	SELECT id FROM profile_hdr WHERE company_id IN (4,2723,7612,14002,3143,14512,8330,8816)
);

DELETE FROM keyword_idx_2 WHERE type = 2 AND id NOT IN (
	SELECT id FROM profile_hdr WHERE company_id IN (4,2723,7612,14002,3143,14512,8330,8816)
);

DELETE FROM profile_hdr WHERE company_id NOT IN (4,2723,7612,14002,3143,14512,8330,8816);

DELETE FROM placement WHERE company_id NOT IN (4,2723,7612,14002,3143,14512,8330,8816);

DELETE FROM profile_tour WHERE p_hdr_id NOT IN (
	SELECT id FROM profile_hdr
);

DELETE FROM profile_general WHERE p_hdr_id NOT IN (
	SELECT id FROM profile_hdr
);

TRUNCATE TABLE keyword_idx_1;

TRUNCATE TABLE enquiry;

TRUNCATE TABLE email_to_friend;

DROP TABLE image_map2;

DROP TABLE img_map;

DROP TABLE flickr_map;

DROP TABLE flickr_photo;

DROP TABLE flickr_set;

DROP TABLE duration;

DROP TABLE char_test;

DROP TABLE camp_attended; 

DROP TABLE camp_keyword;

DROP TABLE camp_new;

DROP TABLE camp_review;

DROP TABLE testimonial;

TRUNCATE TABLE cache;

TRUNCATE TABLE cache_miss;

TRUNCATE TABLE proj_freq_matrix;

VACUUM FULL;
