#!/bin/bash

cp /home/web_developer/.bash_profile /www/vhosts/backup_conf/unix
cp /home/web_developer/.ssh/id_ed25519 /www/vhosts/backup_conf/unix
cp /var/lib/pgsql/10/data/pg_hba.conf  /www/vhosts/backup_conf/pgsql/
cp /var/lib/pgsql/10/data/postgresql.conf /www/vhosts/backup_conf/pgsql/
cp /etc/php.ini /www/vhosts/backup_conf/www/
cp /etc/httpd/conf/httpd.conf /www/vhosts/backup_conf/www/
cp /etc/httpd/conf.modules.d/*.conf /www/vhosts/backup_conf/www/
cp /etc/nginx/nginx.conf /www/vhosts/backup_conf/nginx/
cp /var/solr/data/collection1/conf/schema.xml /www/vhosts/backup_conf/solr/
cp /var/solr/data/collection1/conf/solrconfig.xml /www/vhosts/backup_conf/solr/

cd /www/vhosts/backup_conf
tar -zcvf oneworld365_conf.tar.gz ./*
