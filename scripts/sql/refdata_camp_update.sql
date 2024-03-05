
INSERT INTO refdata_type VALUES (22,'US_REGION','US / Canadian Regions');
INSERT INTO refdata_type VALUES (23,'AGE_RANGE','Age range (children)');
INSERT INTO refdata_type VALUES (24,'RELIGION','Religious affiliation');
INSERT INTO refdata_type VALUES (25,'CAMP_GENDER','Camp gender');

UPDATE refdata SET type = 25 WHERE id IN (53,54,57);
UPDATE refdata SET type = 24 WHERE id IN (60,61,62,63,64);

INSERT INTO refdata VALUES (nextval('refdata_seq'),1,'Traditional');
INSERT INTO refdata VALUES (nextval('refdata_seq'),1,'Academic');
INSERT INTO refdata VALUES (nextval('refdata_seq'),1,'Arts');
INSERT INTO refdata VALUES (nextval('refdata_seq'),1,'Sports');
INSERT INTO refdata VALUES (nextval('refdata_seq'),1,'Private / Independent');
INSERT INTO refdata VALUES (nextval('refdata_seq'),1,'Agency / Non Profit');
INSERT INTO refdata VALUES (nextval('refdata_seq'),1,'Faith Based');

INSERT INTO refdata VALUES (nextval('refdata_seq'),22,'Northeast');
INSERT INTO refdata VALUES (nextval('refdata_seq'),22,'Great Lakes');
INSERT INTO refdata VALUES (nextval('refdata_seq'),22,'Mid-Atlantic');
INSERT INTO refdata VALUES (nextval('refdata_seq'),22,'Midwest');
INSERT INTO refdata VALUES (nextval('refdata_seq'),22,'Southeast');
INSERT INTO refdata VALUES (nextval('refdata_seq'),22,'West & Pacific');
INSERT INTO refdata VALUES (nextval('refdata_seq'),22,'Southwest');
INSERT INTO refdata VALUES (nextval('refdata_seq'),22,'Canada');

INSERT INTO refdata VALUES (nextval('refdata_seq'),23,'<5');
INSERT INTO refdata VALUES (nextval('refdata_seq'),23,5);
INSERT INTO refdata VALUES (nextval('refdata_seq'),23,6);
INSERT INTO refdata VALUES (nextval('refdata_seq'),23,7);
INSERT INTO refdata VALUES (nextval('refdata_seq'),23,8);
INSERT INTO refdata VALUES (nextval('refdata_seq'),23,9);
INSERT INTO refdata VALUES (nextval('refdata_seq'),23,10);
INSERT INTO refdata VALUES (nextval('refdata_seq'),23,11);
INSERT INTO refdata VALUES (nextval('refdata_seq'),23,12);
INSERT INTO refdata VALUES (nextval('refdata_seq'),23,13);
INSERT INTO refdata VALUES (nextval('refdata_seq'),23,14);
INSERT INTO refdata VALUES (nextval('refdata_seq'),23,15);
INSERT INTO refdata VALUES (nextval('refdata_seq'),23,16);
INSERT INTO refdata VALUES (nextval('refdata_seq'),23,17);
INSERT INTO refdata VALUES (nextval('refdata_seq'),23,'18+');


ALTER TABLE profile_summercamp ADD COLUMN camp_gender smallint;
ALTER TABLE profile_summercamp ADD COLUMN camp_religion smallint;
ALTER TABLE profile_summercamp ADD COLUMN camper_age_from_id smallint;
ALTER TABLE profile_summercamp ADD COLUMN camper_age_to_id smallint;

UPDATE refdata set value = '&dollar; Dollars (US)' where id = 292;
