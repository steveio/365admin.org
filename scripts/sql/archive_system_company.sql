--
-- Archive.sql
--
-- Tables to archive / backup placement profiles from live site
--
--
CREATE TABLE company_archive AS SELECT * FROM company;
--
--
TRUNCATE TABLE company_archive;
--
--
CREATE TABLE comp_cat_map_archive AS SELECT * FROM comp_cat_map;
--
--
TRUNCATE TABLE comp_cat_map_archive;
--
--
CREATE TABLE comp_act_map_archive AS SELECT * FROM comp_act_map;
--
--
TRUNCATE TABLE comp_act_map_archive;
--
--
CREATE TABLE comp_country_map_archive AS SELECT * FROM comp_country_map;
--
--
TRUNCATE TABLE comp_country_map_archive;
--
--
CREATE TABLE profile_seasonaljobs_archive AS SELECT * FROM profile_seasonaljobs;
--
--
TRUNCATE TABLE profile_seasonaljobs_archive;
--
--
CREATE TABLE profile_summercamp_archive AS SELECT * FROM profile_summercamp;
--
--
TRUNCATE TABLE profile_summercamp_archive;
--
--
CREATE TABLE profile_teaching_archive AS SELECT * FROM profile_teaching;
--
--
TRUNCATE TABLE profile_teaching_archive;
--
--
CREATE TABLE profile_volunteer_project_archive AS SELECT * FROM profile_volunteer_project;
--
--
TRUNCATE TABLE profile_volunteer_project_archive;
--
--


