#!/bin/bash

#
#  Static page cache refresh process -
#  Generates static HTML files for all page urls defined in db table cache 
#
#  Runs every morning at 4am after /root/cache_build.sh 
#


/usr/bin/php /www/vhosts/oneworld365.org/htdocs/cache_generator2.php oneworld365.org

# make the cache files world writeable so apache/php can update */
chmod -R 0777 /www/vhosts/oneworld365.org/htdocs/cache/page/

