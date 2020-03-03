ALTER TABLE article_map_opts ADD COLUMN opt_gads boolean default true;
ALTER TABLE article_map_opts ADD COLUMN opt_ads boolean default true;

update article_map_opts set opt_gads = true;
update article_map_opts set opt_ads = true;
