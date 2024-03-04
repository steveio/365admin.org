#!/bin/bash

#####################################################################
#
# Nightly Cron Script
#
#####################################################################


path=/www/vhosts/365admin.org/htdocs/scripts/


# report disk free space
df -k > $path/cron/df.out

# report biggest files
$path/cron/biggest.sh -l 30 / > $path/cron/biggest_files.out

# get the size of db objects
sudo -u postgres /usr/local/sbin/pgsql_report_db_size.sh > $path/cron/oneworld_db_size.out

# run nightly backup
$path/cron/backup.sh

# email a status report
/usr/bin/php $path/cron/cronlog_mailer.php

