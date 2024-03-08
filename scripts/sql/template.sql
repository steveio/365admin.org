
alter table template add column fetch_limit smallint;

update template set fetch_limit = 30 where id = 1;

alter table template add column is_collection bool default 'f';

update template set is_collection = 't' where id = 1;

update template set title = 'Blog Default', desc_short = 'Intro Article, 3 Col Article List, 30 Article per page' where id = 1


CREATE TABLE template1 (
	id int not null,
	title varchar(120) not null,
	filename varchar(120) not null,
	desc_short varchar(256),
	fetch_mode smallint not null default 0,
	fetct_limit smallint
);
