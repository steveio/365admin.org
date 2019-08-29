--
-- Placement Restore SQL Script
-- Creates & populates a set of restore TMP tables 
-- pg_dump can then be run against these to generate SQL INSERT statements
--
-- Assumes a valid full DB backup has been obtained/restored
--
-- Replace company_id's 8688,13537 according to comp being restored
--
--
CREATE TABLE profile_hdr_restore AS SELECT * FROM profile_hdr;
--
TRUNCATE TABLE profile_hdr_restore;
--
INSERT INTO profile_hdr_restore (SELECT * FROM profile_hdr WHERE company_id IN (8688,13537));
--
--
CREATE TABLE profile_general_restore AS SELECT * FROM profile_general;
--
TRUNCATE TABLE profile_general_restore;
--
INSERT INTO profile_general_restore (
   SELECT * FROM profile_general WHERE p_hdr_id IN (
      SELECT id FROM profile_hdr WHERE company_id IN (8688,13537)
   )
);
--
--
CREATE TABLE profile_tour_restore AS SELECT * FROM profile_tour;
--
TRUNCATE TABLE profile_tour_restore;
--
INSERT INTO profile_tour_restore (
   SELECT * FROM profile_tour WHERE p_hdr_id IN (
      SELECT id FROM profile_hdr WHERE company_id IN (8688,13537)
   )
);
--
--
-- Now get the dependencies / mappings
--
--
CREATE TABLE prod_cat_map_restore AS SELECT * FROM prod_cat_map;
--
TRUNCATE TABLE prod_cat_map_restore;
--
INSERT INTO prod_cat_map_restore
(
   SELECT * FROM prod_cat_map WHERE prod_id IN (
	SELECT id FROM profile_hdr WHERE company_id IN (8688,13537)
   )
);
--
--
CREATE TABLE prod_act_map_restore AS SELECT * FROM prod_act_map;
--
TRUNCATE TABLE prod_act_map_restore;
--
INSERT INTO prod_act_map_restore
(
   SELECT * FROM prod_act_map WHERE prod_id IN (
	SELECT id FROM profile_hdr WHERE company_id IN (8688,13537)
   )
);
--
--
CREATE TABLE prod_country_map_restore AS SELECT * FROM prod_country_map;
--
TRUNCATE TABLE prod_country_map_restore;
--
INSERT INTO prod_country_map_restore
(
   SELECT * FROM prod_country_map WHERE prod_id IN (
	SELECT id FROM profile_hdr WHERE company_id IN (8688,13537)
   )
);
--
--
CREATE TABLE image_map_restore AS SELECT * FROM image_map;
--
TRUNCATE TABLE image_map_restore;
--
INSERT INTO image_map_restore
(
   SELECT * FROM image_map WHERE link_to = 'PLACEMENT' AND link_id IN (
	SELECT id FROM profile_hdr WHERE company_id IN (8688,13537)
   )
);
--
--
CREATE TABLE prod_opt_map_restore AS SELECT * FROM prod_opt_map;
--
TRUNCATE TABLE prod_opt_map_restore;
--
INSERT INTO prod_opt_map_restore
(
   SELECT * FROM prod_opt_map WHERE prod_id IN (
	SELECT id FROM profile_hdr WHERE company_id IN (8688,13537)
   )
);
--
--
-- Now use pg_dump from cmd line to backup "_restore" tables as SQL INSERT statements...
-- eg.
-- pg_dump -column-inserts --data-only --table= -D -a -t <table> <database>
--
-- /usr/bin/sudo -u postgres pg_dump --column-inserts --data-only --table=profile_hdr_restore oneworld365_2010 > ./profile_hdr_insert.sql
-- /usr/bin/sudo -u postgres pg_dump --column-inserts --data-only --table=profile_general_restore oneworld365_2010 > ./profile_general_insert.sql
-- /usr/bin/sudo -u postgres pg_dump --column-inserts --data-only --table=prod_cat_map_restore oneworld365_2010 > ./prod_cat_map_insert.sql
-- /usr/bin/sudo -u postgres pg_dump --column-inserts --data-only --table=prod_act_map_restore oneworld365_2010 > ./prod_act_map_insert.sql
-- /usr/bin/sudo -u postgres pg_dump --column-inserts --data-only --table=prod_country_map_restore oneworld365_2010 > ./prod_country_map_insert.sql
-- /usr/bin/sudo -u postgres pg_dump --column-inserts --data-only --table=image_map_restore oneworld365_2010 > ./image_map_insert.sql
--
-- Before restoring TMP *_restore" tables in *_insert.sql" files must be renamed
-- eg.
-- sed -i 's/_restore//g' ./profile_hdr_insert.sql
--
-- Placements should be re-indexed after being restored -
--
UPDATE profile_hdr SET last_updated = now()::timestamp WHERE id IN (
   SELECT id FROM profile_hdr WHERE company_id IN (8688,13537)
);
--
-- [root@s15348388 restore]# /usr/bin/php /www/vhosts/oneworld365.org/htdocs/indexer_batch.php DELTA > /dev/null 2>&1
--
-- After a successful restore cleanup by running these commands...
--
--DROP TABLE profile_hdr_restore;
--
--DROP TABLE prod_cat_map_restore;
--
--DROP TABLE prod_act_map_restore;
--
--DROP TABLE prod_country_map_restore;
--
--
--
--
