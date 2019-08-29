--
--
-- 365 Company Profile Updates
-- Specialisation and extending of company profile into sub-types
--
--
CREATE TABLE profile_types (
	id int UNIQUE NOT NULL,
	type smallint NOT NULL,  -- 0 = company profile, 1 = placement
	name varchar(32) NOT NULL,
	description varchar(128) NOT NULL
);
--
--
INSERT INTO profile_types (id,type,name,description) VALUES (0,0,'PROFILE_COMPANY','Organisation Profile - General');
--
INSERT INTO profile_types (id,type,name,description) VALUES (1,1,'PROFILE_PLACEMENT','Placement Profile');
--
INSERT INTO profile_types (id,type,name,description) VALUES (2,1,'PROFILE_VOLUNTEER','Volunteer Placement');
--
INSERT INTO profile_types (id,type,name,description) VALUES (3,1,'PROFILE_TOUR','Tour Placement');
--
INSERT INTO profile_types (id,type,name,description) VALUES (4,1,'PROFILE_JOB','Seasonal Job Placement');
--
INSERT INTO profile_types (id,type,name,description) VALUES (5,0,'PROFILE_SUMMERCAMP','Summer Camp');
--
INSERT INTO profile_types (id,type,name,description) VALUES (6,0,'PROFILE_VOLUNTER_PROJECT','Volunteer Travel Projects');
--
INSERT INTO profile_types (id,type,name,description) VALUES (7,0,'PROFILE_SEASONALJOBS_EMPLOYER','Seasonal Jobs');
--
INSERT INTO profile_types (id,type,name,description) VALUES (8,0,'PROFILE_TEACHING','Teaching Jobs / Courses');
--
--
--
-- add profile_type field to company profile
ALTER TABLE company ADD column profile_type smallint NOT NULL DEFAULT 0 REFERENCES profile_types(id) ON DELETE CASCADE;
--
--
ALTER TABLE company ADD state_id smallint DEFAULT NULL;
--
ALTER TABLE company ADD country_id smallint DEFAULT NULL;
--
--
-- SUB-TYPE TABLES
--
--
--
CREATE TABLE profile_volunteer_project (
	company_id int UNIQUE NOT NULL REFERENCES company(id) ON DELETE CASCADE,
	duration_from_id smallint,
	duration_to_id smallint,
	price_from_id smallint,
	price_to_id smallint,
	currency_id smallint,
	founded varchar(32),
	no_placements smallint,
	org_type smallint,
	awards varchar(512),
	support varchar(512),
	safety varchar(512)
);
--
CREATE TABLE profile_seasonaljobs (
	company_id int UNIQUE NOT NULL REFERENCES company(id) ON DELETE CASCADE,
	job_types varchar(512),
	duration_from_id smallint,
	duration_to_id smallint,
	pay varchar(512),
	benefits varchar(512),
	no_staff smallint,
	how_to_apply varchar(512),
	requirements varchar(512)
);
--
CREATE TABLE profile_summercamp (
	company_id int UNIQUE NOT NULL REFERENCES company(id) ON DELETE CASCADE,
	duration_from_id smallint,
	duration_to_id smallint,
	price_from_id smallint,
	price_to_id smallint,
	currency_id smallint,
	state_id smallint,
	no_staff smallint,
	staff_gender smallint,
	staff_origin smallint,
	season_dates varchar(512),
	requirements varchar(512),
	how_to_apply varchar(512)
);
--
CREATE TABLE profile_teaching (
	company_id int UNIQUE NOT NULL REFERENCES company(id) ON DELETE CASCADE,
	duration_from_id smallint,
	duration_to_id smallint,
	price_from_id smallint,
	price_to_id smallint,
	currency_id smallint,
	no_teachers smallint,
	class_size smallint,
	duration smallint,
	salary varchar(512),
	benefits varchar(512),
	qualifications varchar(512),
	requirements varchar(512),
	how_to_apply varchar(512)
);
--
--
--CREATE TABLE profile_tour_operator (
--	company_id int UNIQUE NOT NULL REFERENCES company(id) ON DELETE CASCADE,
--	founded_date varchar(32),
--	bonding varchar(512)
--	no_travellers smallint,
--	green_policy varchar(1024),
--	awards varchar(1024),
--	group_size varchar(1024)
--);
--
--
--
--
