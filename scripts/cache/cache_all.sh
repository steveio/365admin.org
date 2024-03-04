#!/bin/bash

#
#  Static page cache refresh process -
#  Generates static HTML files for all page urls defined in db table cache 
#
#  Runs every morning at 4am GMT after /root/cache_build.sh 
#

/usr/bin/php /www/vhosts/365admin.org/htdocs/scripts/cache/cache_generator2.php https://www.oneworld365.org

# grant read & write on cache files to apache/php */
chmod -R 0770 /www/vhosts/oneworld365.org/htdocs/cache/

chown -R web_developer:apache /www/vhosts/oneworld365.org/htdocs/cache/

