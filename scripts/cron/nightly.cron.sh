#!/bin/bash

#####################################################################
#
# Nightly Cron Script
#
#####################################################################



# report disk free space
df -k > /www/vhosts/365admin.org/htdocs/scripts/cron/df.out

# report biggest files
sudo /www/vhosts/365admin.org/htdocs/scripts/cron/biggest.sh -l 30 / > biggest_files.out

# get the size of db objects
sudo -u postgres /usr/local/sbin/pgsql_report_db_size.sh > oneworld_db_size.out

# run nightly backup
/www/vhosts/365admin.org/htdocs/script/cron/backup.sh

# email a status report
/usr/bin/php /www/vhosts/365admin.org/htdocs/scripts/cron/cronlog_mailer.php

