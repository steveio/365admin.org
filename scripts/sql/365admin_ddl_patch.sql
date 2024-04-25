--
--
-- 365 Admin System
-- General Migrations and DDL manipulation Patch Script
--
--
--
--
--NEW listing option - for new signups until they've been admin approved
-- 
INSERT INTO listing_option
(id,site_id,code,label,type,detail)
VALUES
(50,0,'NEW','New Listing',-1,0);
--
--
INSERT INTO listing_option
(id,site_id,code,label,type,detail)
VALUES
(51,1,'NEW','New Listing',-1,0);
--
--
INSERT INTO listing_option
(id,site_id,code,label,type,detail)
VALUES
(52,2,'NEW','New Listing',-1,0);
--
--
INSERT INTO listing_option
(id,site_id,code,label,type,detail)
VALUES
(53,3,'NEW','New Listing',-1,0);
--
--
INSERT INTO listing_option
(id,site_id,code,label,type,detail)
VALUES
(54,4,'NEW','New Listing',-1,0);
--
--
INSERT INTO listing_rate (listing_id,price,currency) VALUES (50,0,'GBP');
INSERT INTO listing_rate (listing_id,price,currency) VALUES (51,0,'GBP');
INSERT INTO listing_rate (listing_id,price,currency) VALUES (52,0,'GBP');
INSERT INTO listing_rate (listing_id,price,currency) VALUES (53,0,'GBP');
INSERT INTO listing_rate (listing_id,price,currency) VALUES (54,0,'GBP');
--
--
--  Add duration_from, duration_to, price_from, price_to to profile_general
--
ALTER TABLE profile_general ADD COLUMN duration_from_id SMALLINT;
ALTER TABLE profile_general ADD COLUMN duration_to_id SMALLINT;
ALTER TABLE profile_general ADD COLUMN price_from_id SMALLINT;
ALTER TABLE profile_general ADD COLUMN price_to_id SMALLINT;
ALTER TABLE profile_general ADD COLUMN currency_id SMALLINT;
--
--
ALTER TABLE profile_job ADD COLUMN duration_from_id SMALLINT;
ALTER TABLE profile_job ADD COLUMN duration_to_id SMALLINT;
--
--
--  Add duration_from, duration_to, price_from, price_to to profile_tour
--
ALTER TABLE profile_tour ADD COLUMN duration_from_id SMALLINT;
ALTER TABLE profile_tour ADD COLUMN duration_to_id SMALLINT;
ALTER TABLE profile_tour ADD COLUMN price_from_id SMALLINT;
ALTER TABLE profile_tour ADD COLUMN price_to_id SMALLINT;
ALTER TABLE profile_tour ADD COLUMN currency_id SMALLINT;
ALTER TABLE profile_tour ADD COLUMN group_size_id SMALLINT;
--
-- Merge 
--		profile_tour.price
--		profile_tour.include 
--		profile_tour.not_included
--		profile_tour.local_payment
-- 
--
--
--UPDATE profile_tour SET price = (
--   SELECT 
--   'Price:' || p2.price || E'\r\nLocal Payment: ' || p2.local_payment || E'\r\nIncluded / Excluded: ' || p2.included || E'\r\n' || p2.not_included  
--   FROM profile_tour p2
--   WHERE p2.p_hdr_id = p.p_hdr_id
--);
--
--
--ALTER TABLE profile_tour DROP column local_payment;
--ALTER TABLE profile_tour DROP column included;
--ALTER TABLE profile_tour DROP column not_included;
--
--
--
ALTER TABLE profile_job
    ALTER COLUMN start_dt_multiple TYPE text,
    ALTER COLUMN salary TYPE text;
--
--
--
--
