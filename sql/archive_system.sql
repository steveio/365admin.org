--
-- Archive.sql
--
-- Tables to archive / backup placement profiles from live site
--
--
CREATE TABLE profile_hdr_archive AS SELECT * FROM profile_hdr;
--
--
TRUNCATE TABLE profile_hdr_archive;
--
--
CREATE TABLE profile_general_archive AS SELECT * FROM profile_general;
--
--
TRUNCATE TABLE profile_general_archive;
--
--
CREATE TABLE profile_tour_archive AS SELECT * FROM profile_tour;
--
--
TRUNCATE TABLE profile_tour_archive;
--
--
CREATE TABLE profile_job_archive AS SELECT * FROM profile_job;
--
--
TRUNCATE TABLE profile_job_archive;
--
--
CREATE TABLE prod_cat_map_archive AS SELECT * FROM prod_cat_map;
--
--
TRUNCATE TABLE prod_cat_map_archive;
--
--
CREATE TABLE prod_act_map_archive AS SELECT * FROM prod_act_map;
--
--
TRUNCATE TABLE prod_act_map_archive;
--
--
CREATE TABLE prod_country_map_archive AS SELECT * FROM prod_country_map;
--
TRUNCATE TABLE prod_country_map_archive;
--
--
CREATE TABLE image_map_archive AS SELECT * FROM image_map;
--
--
TRUNCATE TABLE image_map_archive;
--
-- @note - think this table is depreciate on live site
CREATE TABLE prod_opt_map_archive AS SELECT * FROM prod_opt_map;
--
TRUNCATE TABLE prod_opt_map_archive;
--
--
--
