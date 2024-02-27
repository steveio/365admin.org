
#
# cache_build runs each night to refresh cache index table
#

# refresh cache db table to trigger update (cache_generator2.php) of static cache files
/usr/bin/php /www/vhosts/365admin.org/htdocs/scripts/cache/cache_content2.php oneworld365.org ALL > ./cache_build.sh.out 2>&1


