--
-- Refdata system
--
--
--
DROP TABLE refdata_map;
--
DROP TABLE refdata;
--
DROP SEQUENCE refdata_seq;
--
DROP TABLE refdata_type;
--
--
--
--
-- refdata types 
CREATE TABLE refdata_type (
	id int UNIQUE NOT NULL,
	name varchar(16) NOT NULL,
	description varchar(512)
);
--
CREATE SEQUENCE refdata_seq;
--
-- refdata value/item
CREATE TABLE refdata (
	id int UNIQUE NOT NULL,
	type smallint NOT NULL REFERENCES refdata_type(id) ON DELETE CASCADE,
	value varchar(512) NOT NULL
);
--
CREATE INDEX refdata_type_index ON refdata(type);
--
-- links an item of refdata to another object typically a profile
CREATE TABLE refdata_map (
	link_to smallint NOT NULL, -- 0 = company, 1 = placement
	link_id int NOT NULL,
	refdata_type smallint NOT NULL REFERENCES refdata_type(id) ON DELETE CASCADE,
	refdata_id int NOT NULL REFERENCES refdata(id) ON DELETE CASCADE
);
--
CREATE INDEX refdata_map_index ON refdata_map(refdata_type);
--
CREATE INDEX refdata_map_type_index ON refdata_map(link_to, link_id);
--
--
-- refdata types
INSERT INTO refdata_type VALUES (0,'US_STATE','US State / Region');
INSERT INTO refdata_type VALUES (1,'CAMP_TYPE','Summer Camp Type');
INSERT INTO refdata_type VALUES (2,'CAMP_JOB_TYPE','Summer Camp Job Type');
INSERT INTO refdata_type VALUES (3,'ACTIVITY','Activity / Recreation');
INSERT INTO refdata_type VALUES (4,'INT_RANGE','Size range 1 - 1000+');
INSERT INTO refdata_type VALUES (5,'DURATION','Durations in weeks / months');
INSERT INTO refdata_type VALUES (7,'BONDING','Travel Industry Affiliation / Bonding');
INSERT INTO refdata_type VALUES (8,'STAFF_ORIGIN','Domestic / International / Both');
INSERT INTO refdata_type VALUES (9,'GENDER','Male / Female / Both');
INSERT INTO refdata_type VALUES (10,'APPROX_COST','Inidicitive price range');
INSERT INTO refdata_type VALUES (11,'HABITATS','Natural habitats / locations');
INSERT INTO refdata_type VALUES (12,'SPECIES','Animal / Marine Species');
INSERT INTO refdata_type VALUES (13,'ACCOMODATION','Accomodation types');
INSERT INTO refdata_type VALUES (14,'MEALS','Meals / Food provided');
INSERT INTO refdata_type VALUES (15,'TRAVEL_TRANSPORT','Travel / transport included');
INSERT INTO refdata_type VALUES (16,'ADVENTURE_SPORTS','Adventure / Extreme Sports Activities');
INSERT INTO refdata_type VALUES (17,'ORG_PROJECT_TYPE','Organisation / Project Type');
INSERT INTO refdata_type VALUES (18,'CURRENCY','Currency labels/symbols');
INSERT INTO refdata_type VALUES (19,'JOB_OPTIONS','Job options - live in, meals, accomodation etc');
INSERT INTO refdata_type VALUES (20,'INT_SMALL_RANGE','Size range 1 - 100 increments of 10');
INSERT INTO refdata_type VALUES (21,'JOB_WORK_HOURS','Working hours, fulltime, partime etc');
--
--
--
--
--
--
-- US STATE
INSERT INTO refdata VALUES (nextval('refdata_seq'),0,'Alabama');
INSERT INTO refdata VALUES (nextval('refdata_seq'),0,'Alaska');
INSERT INTO refdata VALUES (nextval('refdata_seq'),0,'Arizona');
INSERT INTO refdata VALUES (nextval('refdata_seq'),0,'Arkansas');
INSERT INTO refdata VALUES (nextval('refdata_seq'),0,'California');
INSERT INTO refdata VALUES (nextval('refdata_seq'),0,'Colorado');
INSERT INTO refdata VALUES (nextval('refdata_seq'),0,'Connecticut');
INSERT INTO refdata VALUES (nextval('refdata_seq'),0,'Delaware');
INSERT INTO refdata VALUES (nextval('refdata_seq'),0,'District of Columbia');
INSERT INTO refdata VALUES (nextval('refdata_seq'),0,'Florida');
INSERT INTO refdata VALUES (nextval('refdata_seq'),0,'Georgia');
INSERT INTO refdata VALUES (nextval('refdata_seq'),0,'Hawaii');
INSERT INTO refdata VALUES (nextval('refdata_seq'),0,'Idaho');
INSERT INTO refdata VALUES (nextval('refdata_seq'),0,'Illinois');
INSERT INTO refdata VALUES (nextval('refdata_seq'),0,'Indiana');
INSERT INTO refdata VALUES (nextval('refdata_seq'),0,'Iowa');
INSERT INTO refdata VALUES (nextval('refdata_seq'),0,'Kansas');
INSERT INTO refdata VALUES (nextval('refdata_seq'),0,'Kentucky');
INSERT INTO refdata VALUES (nextval('refdata_seq'),0,'Louisiana');
INSERT INTO refdata VALUES (nextval('refdata_seq'),0,'Maine');
INSERT INTO refdata VALUES (nextval('refdata_seq'),0,'Maryland');
INSERT INTO refdata VALUES (nextval('refdata_seq'),0,'Massachusetts');
INSERT INTO refdata VALUES (nextval('refdata_seq'),0,'Michigan');
INSERT INTO refdata VALUES (nextval('refdata_seq'),0,'Minnesota');
INSERT INTO refdata VALUES (nextval('refdata_seq'),0,'Mississippi');
INSERT INTO refdata VALUES (nextval('refdata_seq'),0,'Missouri');
INSERT INTO refdata VALUES (nextval('refdata_seq'),0,'Montana');
INSERT INTO refdata VALUES (nextval('refdata_seq'),0,'Nebraska');
INSERT INTO refdata VALUES (nextval('refdata_seq'),0,'Nevada');
INSERT INTO refdata VALUES (nextval('refdata_seq'),0,'New Hampshire');
INSERT INTO refdata VALUES (nextval('refdata_seq'),0,'New Jersey');
INSERT INTO refdata VALUES (nextval('refdata_seq'),0,'New Mexico');
INSERT INTO refdata VALUES (nextval('refdata_seq'),0,'New York');
INSERT INTO refdata VALUES (nextval('refdata_seq'),0,'North Carolina');
INSERT INTO refdata VALUES (nextval('refdata_seq'),0,'North Dakota');
INSERT INTO refdata VALUES (nextval('refdata_seq'),0,'Ohio');
INSERT INTO refdata VALUES (nextval('refdata_seq'),0,'Oklahoma');
INSERT INTO refdata VALUES (nextval('refdata_seq'),0,'Oregon');
INSERT INTO refdata VALUES (nextval('refdata_seq'),0,'Pennsylvania');
INSERT INTO refdata VALUES (nextval('refdata_seq'),0,'Rhode Island');
INSERT INTO refdata VALUES (nextval('refdata_seq'),0,'South Carolina');
INSERT INTO refdata VALUES (nextval('refdata_seq'),0,'South Dakota');
INSERT INTO refdata VALUES (nextval('refdata_seq'),0,'Tennessee');
INSERT INTO refdata VALUES (nextval('refdata_seq'),0,'Texas');
INSERT INTO refdata VALUES (nextval('refdata_seq'),0,'Utah');
INSERT INTO refdata VALUES (nextval('refdata_seq'),0,'Vermont');
INSERT INTO refdata VALUES (nextval('refdata_seq'),0,'Virginia');
INSERT INTO refdata VALUES (nextval('refdata_seq'),0,'Washington');
INSERT INTO refdata VALUES (nextval('refdata_seq'),0,'West Virginia');
INSERT INTO refdata VALUES (nextval('refdata_seq'),0,'Wisconsin');
INSERT INTO refdata VALUES (nextval('refdata_seq'),0,'Wyoming');
--
--
--
-- CAMP TYPE
INSERT INTO refdata VALUES (nextval('refdata_seq'),1,'Residential');
INSERT INTO refdata VALUES (nextval('refdata_seq'),1,'Boys');
INSERT INTO refdata VALUES (nextval('refdata_seq'),1,'Girls');
INSERT INTO refdata VALUES (nextval('refdata_seq'),1,'Day camp');
INSERT INTO refdata VALUES (nextval('refdata_seq'),1,'Disability');
INSERT INTO refdata VALUES (nextval('refdata_seq'),1,'Coed');
INSERT INTO refdata VALUES (nextval('refdata_seq'),1,'Family');
INSERT INTO refdata VALUES (nextval('refdata_seq'),1,'Special Needs');
INSERT INTO refdata VALUES (nextval('refdata_seq'),1,'Protestant');
INSERT INTO refdata VALUES (nextval('refdata_seq'),1,'Religious');
INSERT INTO refdata VALUES (nextval('refdata_seq'),1,'Catholic');
INSERT INTO refdata VALUES (nextval('refdata_seq'),1,'Christian');
INSERT INTO refdata VALUES (nextval('refdata_seq'),1,'Jewish');
--
--
-- CAMP JOB TYPE
INSERT INTO refdata VALUES (nextval('refdata_seq'),2,'Chef');
INSERT INTO refdata VALUES (nextval('refdata_seq'),2,'Teacher');
INSERT INTO refdata VALUES (nextval('refdata_seq'),2,'Instructor');
INSERT INTO refdata VALUES (nextval('refdata_seq'),2,'Counselor');
INSERT INTO refdata VALUES (nextval('refdata_seq'),2,'Staff');
INSERT INTO refdata VALUES (nextval('refdata_seq'),2,'Childcare Assistant');
INSERT INTO refdata VALUES (nextval('refdata_seq'),2,'Sports Instructor');
--
--
-- ACTIVITY / RECREATION
INSERT INTO refdata VALUES (nextval('refdata_seq'),3,'Baseball');
INSERT INTO refdata VALUES (nextval('refdata_seq'),3,'Basketball');
INSERT INTO refdata VALUES (nextval('refdata_seq'),3,'Cooking');
INSERT INTO refdata VALUES (nextval('refdata_seq'),3,'Computing');
INSERT INTO refdata VALUES (nextval('refdata_seq'),3,'Volleyball');
INSERT INTO refdata VALUES (nextval('refdata_seq'),3,'Adventure');
INSERT INTO refdata VALUES (nextval('refdata_seq'),3,'Art');
INSERT INTO refdata VALUES (nextval('refdata_seq'),3,'Cheerleading');
INSERT INTO refdata VALUES (nextval('refdata_seq'),3,'Craft');
INSERT INTO refdata VALUES (nextval('refdata_seq'),3,'Rock Climbing');
INSERT INTO refdata VALUES (nextval('refdata_seq'),3,'Sailing');
INSERT INTO refdata VALUES (nextval('refdata_seq'),3,'Science');
INSERT INTO refdata VALUES (nextval('refdata_seq'),3,'Ski Snowboard');
INSERT INTO refdata VALUES (nextval('refdata_seq'),3,'Sport');
INSERT INTO refdata VALUES (nextval('refdata_seq'),3,'Soccer');
INSERT INTO refdata VALUES (nextval('refdata_seq'),3,'Tennis');
INSERT INTO refdata VALUES (nextval('refdata_seq'),3,'Theatre');
INSERT INTO refdata VALUES (nextval('refdata_seq'),3,'Water-Skiing');
INSERT INTO refdata VALUES (nextval('refdata_seq'),3,'Weight Loss');
INSERT INTO refdata VALUES (nextval('refdata_seq'),3,'Wilderness');
INSERT INTO refdata VALUES (nextval('refdata_seq'),3,'Acting');
INSERT INTO refdata VALUES (nextval('refdata_seq'),3,'Dance');
INSERT INTO refdata VALUES (nextval('refdata_seq'),3,'Drama');
INSERT INTO refdata VALUES (nextval('refdata_seq'),3,'Film');
INSERT INTO refdata VALUES (nextval('refdata_seq'),3,'Hiking');
INSERT INTO refdata VALUES (nextval('refdata_seq'),3,'Horse Riding');
INSERT INTO refdata VALUES (nextval('refdata_seq'),3,'Kayaking');
INSERT INTO refdata VALUES (nextval('refdata_seq'),3,'Golf');
INSERT INTO refdata VALUES (nextval('refdata_seq'),3,'Gymnastics');
INSERT INTO refdata VALUES (nextval('refdata_seq'),3,'Magic');
INSERT INTO refdata VALUES (nextval('refdata_seq'),3,'Mountain Biking');
INSERT INTO refdata VALUES (nextval('refdata_seq'),3,'Mountaineering');
INSERT INTO refdata VALUES (nextval('refdata_seq'),3,'Motorsport');
INSERT INTO refdata VALUES (nextval('refdata_seq'),3,'Music');
INSERT INTO refdata VALUES (nextval('refdata_seq'),3,'Nature');
INSERT INTO refdata VALUES (nextval('refdata_seq'),3,'Photography');
INSERT INTO refdata VALUES (nextval('refdata_seq'),3,'Weight Loss');
INSERT INTO refdata VALUES (nextval('refdata_seq'),3,'Winter Sports');
--
--
-- RANGE / APPROX NUMBER
INSERT INTO refdata VALUES (nextval('refdata_seq'),4,'0-10');
INSERT INTO refdata VALUES (nextval('refdata_seq'),4,'10-25');
INSERT INTO refdata VALUES (nextval('refdata_seq'),4,'25-50');
INSERT INTO refdata VALUES (nextval('refdata_seq'),4,'50-100');
INSERT INTO refdata VALUES (nextval('refdata_seq'),4,'100-1000');
INSERT INTO refdata VALUES (nextval('refdata_seq'),4,'1000+');
--
-- DURATION
INSERT INTO refdata VALUES (nextval('refdata_seq'),5,'< 1 week');
INSERT INTO refdata VALUES (nextval('refdata_seq'),5,'1 week');
INSERT INTO refdata VALUES (nextval('refdata_seq'),5,'2 weeks');
INSERT INTO refdata VALUES (nextval('refdata_seq'),5,'3 weeks');
INSERT INTO refdata VALUES (nextval('refdata_seq'),5,'4 weeks');
INSERT INTO refdata VALUES (nextval('refdata_seq'),5,'6 weeks');
INSERT INTO refdata VALUES (nextval('refdata_seq'),5,'2 months');
INSERT INTO refdata VALUES (nextval('refdata_seq'),5,'3 months');
INSERT INTO refdata VALUES (nextval('refdata_seq'),5,'4 months');
INSERT INTO refdata VALUES (nextval('refdata_seq'),5,'6 months');
INSERT INTO refdata VALUES (nextval('refdata_seq'),5,'1 year');
INSERT INTO refdata VALUES (nextval('refdata_seq'),5,'> 1 year');
--
--
-- TRAVEL BONDING / PROTECTION
INSERT INTO refdata VALUES (nextval('refdata_seq'),7,'ABTA');
INSERT INTO refdata VALUES (nextval('refdata_seq'),7,'AITA');
--
-- STAFF ORIGIN
INSERT INTO refdata VALUES (nextval('refdata_seq'),8,'Domestic');
INSERT INTO refdata VALUES (nextval('refdata_seq'),8,'International');
INSERT INTO refdata VALUES (nextval('refdata_seq'),8,'Domestic &amp; International');
--
-- GENDER
INSERT INTO refdata VALUES (nextval('refdata_seq'),9,'Male');
INSERT INTO refdata VALUES (nextval('refdata_seq'),9,'Female');
INSERT INTO refdata VALUES (nextval('refdata_seq'),9,'Male &amp; Female');
--
--
-- APPROX COST  (Pound / Euro / Dollar)
INSERT INTO refdata VALUES (nextval('refdata_seq'),10,'0');
INSERT INTO refdata VALUES (nextval('refdata_seq'),10,'100');
INSERT INTO refdata VALUES (nextval('refdata_seq'),10,'250');
INSERT INTO refdata VALUES (nextval('refdata_seq'),10,'500');
INSERT INTO refdata VALUES (nextval('refdata_seq'),10,'750');
INSERT INTO refdata VALUES (nextval('refdata_seq'),10,'1000');
INSERT INTO refdata VALUES (nextval('refdata_seq'),10,'1250');
INSERT INTO refdata VALUES (nextval('refdata_seq'),10,'1500');
INSERT INTO refdata VALUES (nextval('refdata_seq'),10,'1750');
INSERT INTO refdata VALUES (nextval('refdata_seq'),10,'2000');
INSERT INTO refdata VALUES (nextval('refdata_seq'),10,'2500');
INSERT INTO refdata VALUES (nextval('refdata_seq'),10,'3000+');
--
--
--
-- HABITATS
INSERT INTO refdata VALUES (nextval('refdata_seq'),11,'Coastal');
INSERT INTO refdata VALUES (nextval('refdata_seq'),11,'Beaches');
INSERT INTO refdata VALUES (nextval('refdata_seq'),11,'Forest');
INSERT INTO refdata VALUES (nextval('refdata_seq'),11,'Cloud Forest');
INSERT INTO refdata VALUES (nextval('refdata_seq'),11,'Rainforest');
INSERT INTO refdata VALUES (nextval('refdata_seq'),11,'Tropical Forest');
INSERT INTO refdata VALUES (nextval('refdata_seq'),11,'Rivers');
INSERT INTO refdata VALUES (nextval('refdata_seq'),11,'Savannah');
INSERT INTO refdata VALUES (nextval('refdata_seq'),11,'Mangrove Coastal');
INSERT INTO refdata VALUES (nextval('refdata_seq'),11,'Wetlands');
INSERT INTO refdata VALUES (nextval('refdata_seq'),11,'Nature Park');
INSERT INTO refdata VALUES (nextval('refdata_seq'),11,'National Park');
INSERT INTO refdata VALUES (nextval('refdata_seq'),11,'Eco Park');
INSERT INTO refdata VALUES (nextval('refdata_seq'),11,'Desert');
INSERT INTO refdata VALUES (nextval('refdata_seq'),11,'Marine');
INSERT INTO refdata VALUES (nextval('refdata_seq'),11,'Island');
INSERT INTO refdata VALUES (nextval('refdata_seq'),11,'Mountains');
INSERT INTO refdata VALUES (nextval('refdata_seq'),11,'Coral reef');
INSERT INTO refdata VALUES (nextval('refdata_seq'),11,'Grassland');
INSERT INTO refdata VALUES (nextval('refdata_seq'),11,'Alpine');
INSERT INTO refdata VALUES (nextval('refdata_seq'),11,'Lagoons');
INSERT INTO refdata VALUES (nextval('refdata_seq'),11,'Arctic');
INSERT INTO refdata VALUES (nextval('refdata_seq'),11,'Antarctic');
INSERT INTO refdata VALUES (nextval('refdata_seq'),11,'Lakes');
INSERT INTO refdata VALUES (nextval('refdata_seq'),11,'Glaciers');
INSERT INTO refdata VALUES (nextval('refdata_seq'),11,'Volcanoes');
INSERT INTO refdata VALUES (nextval('refdata_seq'),11,'Hot Springs');
--
--
--
-- SPECIES
INSERT INTO refdata VALUES (nextval('refdata_seq'),12,'Marine Species');
INSERT INTO refdata VALUES (nextval('refdata_seq'),12,'Sea Turtles');
INSERT INTO refdata VALUES (nextval('refdata_seq'),12,'Fish');
INSERT INTO refdata VALUES (nextval('refdata_seq'),12,'Whales');
INSERT INTO refdata VALUES (nextval('refdata_seq'),12,'Dolphins');
INSERT INTO refdata VALUES (nextval('refdata_seq'),12,'Sharks');
INSERT INTO refdata VALUES (nextval('refdata_seq'),12,'Birds');
INSERT INTO refdata VALUES (nextval('refdata_seq'),12,'Condors');
INSERT INTO refdata VALUES (nextval('refdata_seq'),12,'Pelicans');
INSERT INTO refdata VALUES (nextval('refdata_seq'),12,'Toucans');
INSERT INTO refdata VALUES (nextval('refdata_seq'),12,'Eagles');
INSERT INTO refdata VALUES (nextval('refdata_seq'),12,'Tropical Birds');
INSERT INTO refdata VALUES (nextval('refdata_seq'),12,'Birds of Prey');
INSERT INTO refdata VALUES (nextval('refdata_seq'),12,'Bats');
INSERT INTO refdata VALUES (nextval('refdata_seq'),12,'Primates');
INSERT INTO refdata VALUES (nextval('refdata_seq'),12,'Monkeys');
INSERT INTO refdata VALUES (nextval('refdata_seq'),12,'Chimapanzees');
INSERT INTO refdata VALUES (nextval('refdata_seq'),12,'Gorillas');
INSERT INTO refdata VALUES (nextval('refdata_seq'),12,'Orang-utans');
INSERT INTO refdata VALUES (nextval('refdata_seq'),12,'Cats');
INSERT INTO refdata VALUES (nextval('refdata_seq'),12,'Cheetahs');
INSERT INTO refdata VALUES (nextval('refdata_seq'),12,'Leopard');
INSERT INTO refdata VALUES (nextval('refdata_seq'),12,'Lions');
INSERT INTO refdata VALUES (nextval('refdata_seq'),12,'Tigers');
INSERT INTO refdata VALUES (nextval('refdata_seq'),12,'Hyenas');
INSERT INTO refdata VALUES (nextval('refdata_seq'),12,'Snow Leopard');
INSERT INTO refdata VALUES (nextval('refdata_seq'),12,'Jaguar');
INSERT INTO refdata VALUES (nextval('refdata_seq'),12,'Reptiles');
INSERT INTO refdata VALUES (nextval('refdata_seq'),12,'Crocodiles');
INSERT INTO refdata VALUES (nextval('refdata_seq'),12,'Aligators');
INSERT INTO refdata VALUES (nextval('refdata_seq'),12,'Snakes');
INSERT INTO refdata VALUES (nextval('refdata_seq'),12,'Lizards');
INSERT INTO refdata VALUES (nextval('refdata_seq'),12,'Mammals');
INSERT INTO refdata VALUES (nextval('refdata_seq'),12,'Bears');
INSERT INTO refdata VALUES (nextval('refdata_seq'),12,'Elephants');
INSERT INTO refdata VALUES (nextval('refdata_seq'),12,'Hippos');
INSERT INTO refdata VALUES (nextval('refdata_seq'),12,'Pandas');
INSERT INTO refdata VALUES (nextval('refdata_seq'),12,'Rhinos');
INSERT INTO refdata VALUES (nextval('refdata_seq'),12,'Wolves');
INSERT INTO refdata VALUES (nextval('refdata_seq'),12,'Donkeys');
INSERT INTO refdata VALUES (nextval('refdata_seq'),12,'Camals');
INSERT INTO refdata VALUES (nextval('refdata_seq'),12,'Horses');
INSERT INTO refdata VALUES (nextval('refdata_seq'),12,'Otters');
INSERT INTO refdata VALUES (nextval('refdata_seq'),12,'Penguins');
INSERT INTO refdata VALUES (nextval('refdata_seq'),12,'Seals');
INSERT INTO refdata VALUES (nextval('refdata_seq'),12,'Koalas');
INSERT INTO refdata VALUES (nextval('refdata_seq'),12,'Amphibians');
INSERT INTO refdata VALUES (nextval('refdata_seq'),12,'Frogs');
INSERT INTO refdata VALUES (nextval('refdata_seq'),12,'Toads');
INSERT INTO refdata VALUES (nextval('refdata_seq'),12,'Invertebrates');
INSERT INTO refdata VALUES (nextval('refdata_seq'),12,'Insects');
INSERT INTO refdata VALUES (nextval('refdata_seq'),12,'Butterflies');
--
--
--
--
-- ACCOMODATION
INSERT INTO refdata VALUES (nextval('refdata_seq'),13,'Accomodation Included');
INSERT INTO refdata VALUES (nextval('refdata_seq'),13,'Hotel');
INSERT INTO refdata VALUES (nextval('refdata_seq'),13,'Guesthouse');
INSERT INTO refdata VALUES (nextval('refdata_seq'),13,'Hostel - Dormitory');
INSERT INTO refdata VALUES (nextval('refdata_seq'),13,'Hostel - Room');
INSERT INTO refdata VALUES (nextval('refdata_seq'),13,'Camping');
INSERT INTO refdata VALUES (nextval('refdata_seq'),13,'Chalet / Cabins');
INSERT INTO refdata VALUES (nextval('refdata_seq'),13,'Homestay');
--
--
--
-- MEALS
INSERT INTO refdata VALUES (nextval('refdata_seq'),14,'Meals Included');
INSERT INTO refdata VALUES (nextval('refdata_seq'),14,'Breakfast');
INSERT INTO refdata VALUES (nextval('refdata_seq'),14,'Lunch');
INSERT INTO refdata VALUES (nextval('refdata_seq'),14,'Dinner');
--
--
--
-- TRAVEL / TRANSPORT
INSERT INTO refdata VALUES (nextval('refdata_seq'),15,'International Flights');
INSERT INTO refdata VALUES (nextval('refdata_seq'),15,'Airport Pickup');
INSERT INTO refdata VALUES (nextval('refdata_seq'),15,'Internal Travel');
INSERT INTO refdata VALUES (nextval('refdata_seq'),15,'Internal Flights');
INSERT INTO refdata VALUES (nextval('refdata_seq'),15,'Bus / Coach');
INSERT INTO refdata VALUES (nextval('refdata_seq'),15,'Minibus');
INSERT INTO refdata VALUES (nextval('refdata_seq'),15,'Bicycle');
INSERT INTO refdata VALUES (nextval('refdata_seq'),15,'Boat / Ferry');
INSERT INTO refdata VALUES (nextval('refdata_seq'),15,'Jeep / 4x4');
INSERT INTO refdata VALUES (nextval('refdata_seq'),15,'Taxi');
INSERT INTO refdata VALUES (nextval('refdata_seq'),15,'Trekking / Hiking');
INSERT INTO refdata VALUES (nextval('refdata_seq'),15,'Balloon');
--
--
--
-- ADVENTURE / SPORTS ACTIVITIES
INSERT INTO refdata VALUES (nextval('refdata_seq'),16,'Trekking');
INSERT INTO refdata VALUES (nextval('refdata_seq'),16,'Hiking');
INSERT INTO refdata VALUES (nextval('refdata_seq'),16,'Skiing');
INSERT INTO refdata VALUES (nextval('refdata_seq'),16,'Snowboarding');
INSERT INTO refdata VALUES (nextval('refdata_seq'),16,'Mountaineering');
INSERT INTO refdata VALUES (nextval('refdata_seq'),16,'Climbing');
INSERT INTO refdata VALUES (nextval('refdata_seq'),16,'Ice Climbing');
INSERT INTO refdata VALUES (nextval('refdata_seq'),16,'Bungee Jumping');
INSERT INTO refdata VALUES (nextval('refdata_seq'),16,'Rafting');
INSERT INTO refdata VALUES (nextval('refdata_seq'),16,'Canoeing');
INSERT INTO refdata VALUES (nextval('refdata_seq'),16,'Sailing');
INSERT INTO refdata VALUES (nextval('refdata_seq'),16,'Jet Skiing');
INSERT INTO refdata VALUES (nextval('refdata_seq'),16,'Flying');
INSERT INTO refdata VALUES (nextval('refdata_seq'),16,'Hang Gliding');
INSERT INTO refdata VALUES (nextval('refdata_seq'),16,'Parachuting');
INSERT INTO refdata VALUES (nextval('refdata_seq'),16,'Sky Diving');
INSERT INTO refdata VALUES (nextval('refdata_seq'),16,'Kite Surfing');
INSERT INTO refdata VALUES (nextval('refdata_seq'),16,'Surfing');
INSERT INTO refdata VALUES (nextval('refdata_seq'),16,'Scuba Diving');
INSERT INTO refdata VALUES (nextval('refdata_seq'),16,'Water Skiing');
INSERT INTO refdata VALUES (nextval('refdata_seq'),16,'Wakeboarding');
INSERT INTO refdata VALUES (nextval('refdata_seq'),16,'Snowboarding');
INSERT INTO refdata VALUES (nextval('refdata_seq'),16,'Windsurfing');
INSERT INTO refdata VALUES (nextval('refdata_seq'),16,'Skateboarding');
INSERT INTO refdata VALUES (nextval('refdata_seq'),16,'Caving');
INSERT INTO refdata VALUES (nextval('refdata_seq'),16,'Mountain Biking');
INSERT INTO refdata VALUES (nextval('refdata_seq'),16,'BMX');
--
--
--
-- ORG / PROJECT TYPE
INSERT INTO refdata VALUES (nextval('refdata_seq'),17,'Animal Rescue Centre / Sanctuary');
INSERT INTO refdata VALUES (nextval('refdata_seq'),17,'Scientific Research');
INSERT INTO refdata VALUES (nextval('refdata_seq'),17,'Expedition');
INSERT INTO refdata VALUES (nextval('refdata_seq'),17,'Conservation');
INSERT INTO refdata VALUES (nextval('refdata_seq'),17,'NGO');
INSERT INTO refdata VALUES (nextval('refdata_seq'),17,'Charity');
INSERT INTO refdata VALUES (nextval('refdata_seq'),17,'Religious Mission');
INSERT INTO refdata VALUES (nextval('refdata_seq'),17,'Non-Profit');
INSERT INTO refdata VALUES (nextval('refdata_seq'),17,'Private');
INSERT INTO refdata VALUES (nextval('refdata_seq'),17,'Academic');
INSERT INTO refdata VALUES (nextval('refdata_seq'),17,'Travel Company');
INSERT INTO refdata VALUES (nextval('refdata_seq'),17,'Sending Agency');
--
--
--
-- CURRENCY
INSERT INTO refdata VALUES (nextval('refdata_seq'),18,'&pound; Pound (UK)');
INSERT INTO refdata VALUES (nextval('refdata_seq'),18,'&euro; Euro ');
INSERT INTO refdata VALUES (nextval('refdata_seq'),18,'&dollar; Dollar (US) ');
--
--
-- JOB OPTIONS
INSERT INTO refdata VALUES (nextval('refdata_seq'),19,'Live in');
INSERT INTO refdata VALUES (nextval('refdata_seq'),19,'Accomodation');
INSERT INTO refdata VALUES (nextval('refdata_seq'),19,'Meal(s) Included');
INSERT INTO refdata VALUES (nextval('refdata_seq'),19,'Airport Pickup');
INSERT INTO refdata VALUES (nextval('refdata_seq'),19,'Outdoors Work');
INSERT INTO refdata VALUES (nextval('refdata_seq'),19,'Sports Activities');
INSERT INTO refdata VALUES (nextval('refdata_seq'),19,'Ski Passes');
INSERT INTO refdata VALUES (nextval('refdata_seq'),19,'Nightlife');
--
--
INSERT INTO refdata VALUES (nextval('refdata_seq'),20,'0-5');
INSERT INTO refdata VALUES (nextval('refdata_seq'),20,'5-10');
INSERT INTO refdata VALUES (nextval('refdata_seq'),20,'10-25');
INSERT INTO refdata VALUES (nextval('refdata_seq'),20,'25-50');
INSERT INTO refdata VALUES (nextval('refdata_seq'),20,'50+');
--
--
--
INSERT INTO refdata VALUES (nextval('refdata_seq'),21,'Full Time');
INSERT INTO refdata VALUES (nextval('refdata_seq'),21,'Part Time');
INSERT INTO refdata VALUES (nextval('refdata_seq'),21,'Flexible');
--
--
INSERT INTO refdata VALUES (nextval('refdata_seq'),10,'< 25');
INSERT INTO refdata VALUES (nextval('refdata_seq'),10,'50');

