#!/bin/bash

cd /www/vhosts/oneworl365.org/htdocs

mkdir cache
mkdir cache/page

ln -s /www/vhosts/365admin.org/htdocs/classes/ classes
ln -s /www/vhosts/365admin.org/htdocs/controllers/ controllers 
ln -s /www/vhosts/365admin.org/htdocs/css/ css
ln -s /www/vhosts/365admin.org/htdocs/images/ images
ln -s /www/vhosts/365admin.org/htdocs/includes/ includes
ln -s /www/vhosts/365admin.org/htdocs/lib/ lib
ln -s /www/vhosts/365admin.org/htdocs/scripts/ scripts
ln -s /www/vhosts/365admin.org/htdocs/solr/ solr
ln -s /www/vhosts/365admin.org/htdocs/templates/ templates
ln -s /www/vhosts/365admin.org/htdocs/vendor/ vendor
ln -s /www/vhosts/365admin.org/htdocs/webservices webservices

chown -R web_developer:apache ./
chmod -R 0774 ./ 
