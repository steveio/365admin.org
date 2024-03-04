#!/bin/bash

#####################################################################
#
# Nightly Cron Script
#
#####################################################################



# report disk free space
df -k > ~/cron/df.out

# report biggest files
sudo /home/web_developer/cron/biggest.sh -l 30 / > biggest_files.out

# get the size of db objects
sudo -u postgres /usr/local/sbin/pgsql_report_db_size.sh > oneworld_db_size.out

# run nightly backup
~/cron/backup.sh

# email a status report
/usr/bin/php ~/cron/cronlog_mailer.php

