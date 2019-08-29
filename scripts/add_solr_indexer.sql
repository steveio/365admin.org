

ALTER TABLE company ADD COLUMN last_indexed_solr timestamp without time zone;

ALTER TABLE profile_hdr ADD COLUMN last_indexed_solr timestamp without time zone;

ALTER TABLE article ADD COLUMN last_indexed_solr timestamp without time zone;

UPDATE article SET last_indexed_solr = now();

UPDATE profile_hdr SET last_indexed_solr = now();

UPDATE company SET last_indexed_solr = now();
