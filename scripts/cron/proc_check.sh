#!/bin/bash

#check if postgressql is running
if pgrep postmaster >/dev/null 2>&1
  then
     printf "Postgres is running \n"
  else
     printf "Stopping postgres \n"
     service postgresql-10 stop > /dev/null
     printf "Starting postgres... \n"
     service postgresql-10 start > /dev/null
fi

#check if nginx is running
if pgrep nginx >/dev/null 2>&1
  then
     printf "NGINX is running \n"
  else
     printf "Stopping NGINX \n"
     service nginx stop > /dev/null
     printf "Starting NGINX... \n"
     service nginx start > /dev/null
fi

#check if Apache is running
if pgrep httpd >/dev/null 2>&1
  then
     printf "Apache is running \n"
  else
     printf "Stopping Apache \n"
     service httpd stop > /dev/null
     printf "Starting Apache... \n"
     service httpd start > /dev/null
fi


if pgrep crond >/dev/null 2>&1
  then
     printf "Crond is running \n"
  else
     printf "Stopping crond \n"
     service crond stop > /dev/null
     printf "Starting crond... \n"
     service crond start > /dev/null
fi

# Check SOLR status (as user solr)
su - solr /var/solr/proc_check.sh
