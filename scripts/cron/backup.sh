#!/bin/bash

# backup.sh
#
# generate nightly backup of oneworld365 application, configs & database
#
# keep n days of backups
#
# does not backup images (oneworld365.org/htdocs/img) which must be archived manually
#


# create nightly backup of code and databases
prefix=oneworld365_
suffix=$(date +%Y%m%d)
filename=$prefix$suffix.tar.gz
cd /www/vhosts/oneworld365.org/htdocs
tar -zcvf $filename \
./ \
--exclude ./img \
--exclude ./cache \
--exclude ./upload \
--exclude ./.git
mv $filename /www/vhosts/backup/

prefix=365api_
suffix=$(date +%Y%m%d)
filename=$prefix$suffix.tar
cd /www/vhosts/365api.org/htdocs
tar -zcvf $filename ./
mv $filename /www/vhosts/backup/

prefix=365admin_
suffix=$(date +%Y%m%d)
filename=$prefix$suffix.tar
cd /www/vhosts/365admin.org/htdocs
tar -zcvf $filename ./ 
mv $filename /www/vhosts/backup

# backup postgres db
cd ~
sudo -u postgres /usr/local/sbin/pgsql_dump_db.sh oneworld365 /www/vhosts/backup 

# delete any backup archives > 10days old
find /www/vhosts/backup -mtime +10 -exec rm {} \;

