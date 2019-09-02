#!/bin/bash

/usr/bin/curl http://localhost:8983/solr/collection1/update --data '<delete><query>profile_type:0</query></delete>' -H 'Content-type:text/xml; charset=utf-8'
/usr/bin/curl http://localhost:8983/solr/collection1/update --data '<commit/>' -H 'Content-type:text/xml; charset=utf-8'

/usr/bin/php /www/vhosts/365admin.org/htdocs/solr/solr_indexer.php ALL COMPANY

/usr/bin/curl http://localhost:8983/solr/collection1/update --data '<delete><query>profile_type:1</query></delete>' -H 'Content-type:text/xml; charset=utf-8'
/usr/bin/curl http://localhost:8983/solr/collection1/update --data '<commit/>' -H 'Content-type:text/xml; charset=utf-8'

/usr/bin/php /www/vhosts/365admin.org/htdocs/solr/solr_indexer.php ALL PLACEMENT

/usr/bin/curl http://localhost:8983/solr/collection1/update --data '<delete><query>profile_type:2</query></delete>' -H 'Content-type:text/xml; charset=utf-8'
/usr/bin/curl http://localhost:8983/solr/collection1/update --data '<commit/>' -H 'Content-type:text/xml; charset=utf-8'

/usr/bin/php /www/vhosts/365admin.org/htdocs/solr/solr_indexer.php ALL ARTICLE

