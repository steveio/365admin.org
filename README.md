# 365admin.org

Web Application CMS for: www.oneworld365.org


Release Notes:

v3.0.1   04/03/2024 

- Website Responsive UI updated to latest version
- Templates optimised for mobile, tablet & desktop screen sizes
- Extended Profile metadata displayed for Tours, Summer Camp, Seasonal Jobs & Volunteer Project profile types
- Additional Publisher options to create article  pages with variety of content elements (search results, blog articles, reviews, related profiles / articles)
- Easier maintenance: single CMS codebase, templates for all content elements 
- Use of larger image formats in article / profile summary
- SEO : improved HTML page metadata (description / keywords)
- Search Engine (SOLR) : ensure a range of advertiser profiles are returned, quicker / lightweight search result algorithm

...................................................................

Build / Hosting Install Notes

Required Components:

oneworld365.org /www/vhosts/oneworld365.org/htdocs ( website front-end )
365admin.org /www/vhosts/365admin.org/htdocs ( CMS admin system )
365api.org /www/vhosts/365api.org/htdocs ( SOLR REST API ) 

...................................................................................................

Database - Postgres v10 

CentOS 7 Install

rpm -Uvh https://yum.postgresql.org/10/redhat/rhel-7-x86_64/pgdg-centos10-10-2.noarch.rpm
yum install postgresql10-server postgresql10
/usr/pgsql-10/bin/postgresql-10-setup initdb


Paths

# main 
/var/lib/pgsql/10/
# data / config
/var/lib/pgsql/10/data/
# logs
/var/lib/pgsql/10/data/log/postgresql-<Day>.log
# binaries
/usr/pgsql-10/bin/


CMDs

systemctl start postgresql-10.service
systemctl stop postgresql-10.service
systemctl restart postgresql-10.service

systemctl status postgresql-10.service

# view log messages from all (or -b latest boot)
journalctl -u service-name.service

# example manual start
/usr/pgsql-10/bin/pg_ctl -D /var/lib/pgsql/10/data/ -l logfile start


#     Start PGSQL shell
sudo -u postgres psql -U postgres -d template1

# set DB user password (scram-sha-256 auth)
sudo -u postgres psql postgres
# \password postgres


Config


Authentication:

/var/lib/pgsql/10/data/pg_hba.conf

# TYPE  DATABASE        USER            ADDRESS                 METHOD

# "local" is for Unix domain socket connections only
local   all             all                                     scram-sha-256
# IPv4 local connections:
host    all             all             127.0.0.1/32            scram-sha-256
# IPv6 local connections:
host    all             all             ::1/128                 scram-sha-256
# Allow replication connections from localhost, by a user with the
# replication privilege.
local   replication     all                                     peer
host    replication     all             127.0.0.1/32            ident
host    replication     all             ::1/128                 ident



Server	/var/lib/pgsql/10/data/postgres.conf

max_connections =  35
password_encryption = scram-sha-256  
shared_buffers = 512MB  



(Ubuntu) Install

Docs: https://wiki.postgresql.org/wiki/Apt

sudo apt-get install wget ca-certificates
wget --quiet -O - https://www.postgresql.org/media/keys/ACCC4CF8.asc | sudo apt-key add -
sudo sh -c 'echo "deb http://apt.postgresql.org/pub/repos/apt/ $(lsb_release -cs)-pgdg main" > /etc/apt/sources.list.d/pgdg.list'

sudo apt-get update
sudo apt-get upgrade
sudo apt-get install postgresql-10

Paths

    (Ubuntu)
    /usr/lib/postgresql/10/bin/postgres -D /var/lib/postgresql/10/main -c config_file=/etc/postgresql/10/main/postgresql.conf
    Logs /etc/postgresql/10/main/
    Path    /etc/postgresql/10/main/postgresql.conf


CMDs

    Start PGSQL shell (Ubuntu)

    sudo -i -u postgres
    # without switch os account
    sudo -u postgres psql

    # set DB user password
    sudo -u postgres psql postgres
    # \password postgres

    # pgsql as user=postgres, database=template1
    sudo -u postgres psql -U oneworld365_pgsql -d oneworld365

export PGPASSWORD='1ndoN3s!4a'

export PGPASSWORD='tH3a1LAn6iA'

Maintenance

    Reindex [postgres@...]:
    /usr/bin/psql -d oneworld365 -c "REINDEX DATABASE oneworld365;"

    Vacuum [postgres@...]:
    /root/cron/vacuum_db.sh


Database Install / Restore


Add web app PostgreSQL database user, create database

# add oneworld365_pgsql shell user
sudo adduser oneworld365_pgsql

# start pgsql shell
sudo -u postgres psql -U postgres -d template1

CREATE USER oneworld365_pgsql WITH PASSWORD '';

CREATE DATABASE oneworld365 WITH TEMPLATE = template0 ENCODING = 'UTF8';

GRANT ALL PRIVILEGES ON DATABASE oneworld365 to oneworld365_pgsql;

# test
sudo -u oneworld365_pgsql psql -U oneworld365_pgsql -d template1


Create Schema / Data Restore

mkdir db-restore-20190717
cd db-restore-20190717

-- copy schema + database dumps, un-compress
cp ../../backups/20190717/oneworld365_db_schema20190717.gz ./
cp ../../backups/20190717/oneworld365_db_data20190717.gz ./

gunzip oneworld365_db_schema20190717.gz
gunzip oneworld365_db_data20190717.gz

-- edit DB schema file

-- create db only if required
-- CREATE DATABASE oneworld365 WITH TEMPLATE = template0 ENCODING = 'UTF8';
-- ALTER DATABASE oneworld365 OWNER TO oneworld365;

\connect postgres

#
# Use gedit to split schema backup into create table (pre-data load) and create view, add constraint, create index (post data load)
#  SQL restore file #1: create table statements
#  SQL restore file #2: create view, alter add constraint, create index
#

# load DB schema
sudo -u postgres psql -U postgres -d template1 < ./oneworld365_db_schema20190831


# load DB data
sudo -u postgres psql -U postgres -d oneworld365 < ./oneworld365_db_data20181015

pg_restore -c -F t -f your.backup.tar


# load DB schema post install SQL
sudo -u postgres psql -U postgres -d oneworld365 < ./oneworld365_db_schema20181015_postdata_sql


# grant to web user 
GRANT ALL PRIVILEGES ON ALL TABLES IN SCHEMA public TO oneworld365_pgsql;

GRANT ALL PRIVILEGES ON ALL SEQUENCES IN SCHEMA public TO oneworld365_pgsql;



# verify
sudo -u postgres psql -U postgres -d oneworld365

# Dump - single table to text file

/usr/bin/pg_dump -U oneworld365_pgsql -Fp -a -t article_restore oneworld365 > oneworld365_article_restore.sql

# Restore single table - extract required lines from SQL dump file
sed -n '2786,6766p' oneworld365_db_data20181105 > oneworld365_db_data20181105_article

sed -n '16318,19774p' oneworld365_db_data20181105 > oneworld365_db_data20181105_article_map

# grant
GRANT ALL PRIVILEGES ON ALL TABLES IN SCHEMA public TO oneworld365;



# vacuum the databases (run as user postgres)
PGPASSWORD=XXX /usr/bin/psql -d oneworld365 -c "VACUUM FULL;"
PGPASSWORD=XXX /usr/bin/psql -d postgres -c "VACUUM FULL;"
PGPASSWORD=XXX /usr/bin/psql -d template1 -c "VACUUM FULL;"
PGPASSWORD=XXX /usr/bin/psql -d oneworld365 -c "REINDEX DATABASE oneworld365;"
PGPASSWORD=XXX /usr/bin/psql -d oneworld365 -c "ANALYZE;"



.........................................................................................

HTTPD - Apache / NGINX

Apache2

Install

Centos7
yum install httpd

Ubuntu
sudo apt-get update && sudo apt-get install apache2


systemctl restart apache2.service

systemctl restart nginx.service


Configure

Config files:
    Ubuntu: /etc/apache2
    CENTOS: /etc/httpd


httpd.conf

    # Test apache2 config
    apachectl -t


    Ubuntu /etc/apache2/ports.conf

    # listen on 8080
    # /etc/apache2/ports.conf:Listen 80
    Listen 8080


    # Oct 15 11:00:01 ideapad-530S apachectl[13642]: AH00548: NameVirtualHost has no effect and will be removed in the next release /etc/apache2/ports.conf:6
    NameVirtualHost *:8080

    Ubuntu /etc/apache2/apache2.conf
    CENTOS /etc/httpd/conf/httpd.conf

    Config:
    Timeout 300
    # nginx talks HTTP/1.0 to reverse proxy apache, no keep alive support
    KeepAlive Off


    -- set SA email
    > ServerAdmin www-error@oneworld365.org

    -- add X-Forwarded-For header to Log Pattern
    > LogFormat "%{X-Forwarded-For}i %l %u %t \"%r\" %>s %b \"%{Referer}i\" \"%{User-Agent}i\"" combined

    -- display cgi-bin
    < ScriptAlias /cgi-bin/ "/var/www/cgi-bin/"
    > #ScriptAlias /cgi-bin/ "/var/www/cgi-bin/"
    > #<Directory "/var/www/cgi-bin">
    > #    AllowOverride None
    > #    Options None
    > #    Order allow,deny
    > #    Allow from all:q!

    > #</Directory>

    -- set vhost port to :8080
    > NameVirtualHost *:8080

    -- remove PHP mime type
    < AddType application/x-httpd-php .php
    < AddType application/x-httpd-php-source .phps

    Mods Enabled (Centos 7)

	/etc/httpd/conf.modules.d/00-mpm.conf:LoadModule mpm_prefork_module modules/mod_mpm_prefork.so
	/etc/httpd/conf.modules.d/00-systemd.conf:LoadModule systemd_module modules/mod_systemd.so
	/etc/httpd/conf.modules.d/00-base.conf:LoadModule access_compat_module modules/mod_access_compat.so
	/etc/httpd/conf.modules.d/00-base.conf:LoadModule alias_module modules/mod_alias.so
	/etc/httpd/conf.modules.d/00-base.conf:LoadModule auth_basic_module modules/mod_auth_basic.so
	/etc/httpd/conf.modules.d/00-base.conf:LoadModule authz_core_module modules/mod_authz_core.so
	/etc/httpd/conf.modules.d/00-base.conf:LoadModule authz_host_module modules/mod_authz_host.so
	/etc/httpd/conf.modules.d/00-base.conf:LoadModule authz_user_module modules/mod_authz_user.so
	/etc/httpd/conf.modules.d/00-base.conf:LoadModule autoindex_module modules/mod_autoindex.so
	/etc/httpd/conf.modules.d/00-base.conf:LoadModule dir_module modules/mod_dir.so
	/etc/httpd/conf.modules.d/00-base.conf:LoadModule headers_module modules/mod_headers.so
	/etc/httpd/conf.modules.d/00-base.conf:LoadModule include_module modules/mod_include.so
	/etc/httpd/conf.modules.d/00-base.conf:LoadModule log_config_module modules/mod_log_config.so
	/etc/httpd/conf.modules.d/00-base.conf:LoadModule logio_module modules/mod_logio.so
	/etc/httpd/conf.modules.d/00-base.conf:LoadModule mime_module modules/mod_mime.so
	/etc/httpd/conf.modules.d/00-base.conf:LoadModule negotiation_module modules/mod_negotiation.so
	/etc/httpd/conf.modules.d/00-base.conf:LoadModule rewrite_module modules/mod_rewrite.so
	/etc/httpd/conf.modules.d/00-base.conf:LoadModule setenvif_module modules/mod_setenvif.so
	/etc/httpd/conf.modules.d/00-base.conf:LoadModule slotmem_plain_module modules/mod_slotmem_plain.so
	/etc/httpd/conf.modules.d/00-base.conf:LoadModule slotmem_shm_module modules/mod_slotmem_shm.so
	/etc/httpd/conf.modules.d/00-base.conf:LoadModule unixd_module modules/mod_unixd.so
	/etc/httpd/conf.modules.d/00-base.conf:LoadModule vhost_alias_module modules/mod_vhost_alias.so



    Mods enabled (Centos 5):
        LoadModule alias_module modules/mod_alias.so
        LoadModule auth_basic_module modules/mod_auth_basic.so
        LoadModule authz_host_module modules/mod_authz_host.so
        LoadModule authz_user_module modules/mod_authz_user.so
        LoadModule autoindex_module modules/mod_autoindex.so
        LoadModule dir_module modules/mod_dir.so
        LoadModule headers_module modules/mod_headers.so
        LoadModule include_module modules/mod_include.so
        LoadModule log_config_module modules/mod_log_config.so
        LoadModule logio_module modules/mod_logio.so
        LoadModule mime_module modules/mod_mime.so
        LoadModule negotiation_module modules/mod_negotiation.so
        LoadModule proxy_connect_module modules/mod_proxy_connect.so
        LoadModule proxy_http_module modules/mod_proxy_http.so
        LoadModule proxy_module modules/mod_proxy.so
        LoadModule rewrite_module modules/mod_rewrite.so
        LoadModule setenvif_module modules/mod_setenvif.so
        LoadModule vhost_alias_module modules/mod_vhost_alias.so


vhosts:

<VirtualHost *:8080>
	DocumentRoot /www/vhosts/oneworld365.org/htdocs
	ServerName oneworld365.org
	ServerAdmin webmaster@oneworld365.org
	ServerAlias oneworld365.org oneworld365.net *.oneworld365.net oneworld365.com *.oneworld365.com
	LogFormat COMBINED
	ErrorLog /var/www/vhosts/oneworld365.org/logs/error_log
	TransferLog /var/www/vhosts/oneworld365.org/logs/access_log
	HostNameLookups off

	<Directory "/var/www/vhosts/oneworld365.org/htdocs">
	  Options All
	  AllowOverride All
	  Order allow,deny
	  Allow from all
	</Directory>
</VirtualHost>

<VirtualHost *:8080>
	DocumentRoot /www/vhosts/365admin.org/htdocs
	ServerName admin.oneworld365.org
	ServerAdmin webmaster@oneworld365.org
	ServerAlias admin.oneworld365.org
	LogFormat COMBINED
	ErrorLog /var/www/vhosts/365admin.org/logs/error_log
	TransferLog /var/www/vhosts/365admin.org/logs/access_log
	HostNa\meLookups off
	<Directory "/www/vhosts/365admin.org/htdocs">
	  Opt\ions All
	  AllowOverride All
	  Order allow,deny
	  Allow from all
	</Directory>
</VirtualHost>


<VirtualHost *:8080>
	DocumentRoot /www/vhosts/365api.org/htdocs
	ServerName api.oneworld365.org
	ServerAdmin webmaster@oneworld365.org
	ServerAlias api.oneworld365.org
	LogFormat COMBINED
	ErrorLog /var/www/vhosts/365api.org/logs/error_log
	TransferLog /var/www/vhosts/365api.org/logs/access_log
	HostNameLookups off
	<Directory "/www/vhosts/365api.org/htdocs">
	  Options All
	  AllowOverride All
	  Order allow,deny
	  Allow from all
	</Directory>
</VirtualHost>

# Test VHOST config
apachectl -S

# list loaded apache modules
apachectl -t -D DUMP_MODULES

NGINX

Install

Centos 7
yum install epel-release
yum install nginx

Ubuntu
sudo apt-get update && sudo apt-get install nginx

Config 
/etc/nginx/nginx.conf

Start / Stop / Status

[root@cloud-vps vhosts]# service nginx status
Redirecting to /bin/systemctl status nginx.service
● nginx.service - The nginx HTTP and reverse proxy server
   Loaded: loaded (/usr/lib/systemd/system/nginx.service; enabled; vendor preset: disabled)
   Active: active (running) since Mon 2019-11-18 11:00:16 UTC; 3 months 3 days ago
 Main PID: 31011 (nginx)
   CGroup: /system.slice/nginx.service
           ├─31011 nginx: master process /usr/sbin/nginx
           ├─31012 nginx: worker process
           ├─31013 nginx: worker process
           ├─31014 nginx: worker process
           └─31015 nginx: worker process


Config

    -- increase worker connections
    events {
        worker_connections 1024;
    }

    -- enable gzip
        gzip on;

        gzip_vary on;
        gzip_proxied any;
        gzip_comp_level 6;
        gzip_buffers 16 8k;
        gzip_http_version 1.1;
        gzip_types text/plain text/css application/json application/javascript text/xml application/xml application/xml+rss text/javascript;

    -- add vhost config (ignore include sites enabled)
    server {
    listen       80;
    server_name  oneworld365.org www.oneworld365.org oneworld365.co.uk www.oneworld365.co.uk;

    location ~ ^/(img|images|includes/js|css|scripts|js|swf)/ {
    root         /www/vhosts/oneworld365.org/htdocs;
    expires 30d;
    }

    location / {
    proxy_pass    http://127.0.0.1:8080;
    }

    }




    server {
    listen       80;
    server_name  admin.oneworld365.org;

    location ~ ^/(img|images|css|scripts|js|swf)/ {
    root         /www/vhosts/365admin.org/htdocs;
    expires 30d;
    }


    location / {
    proxy_pass      http://127.0.0.1:8080;
    }

    }

    server {
    listen       80;
    server_name  api.oneworld365.org;

    location ~ ^/(img|images|css|scripts|js|swf)/ {
    root         /www/vhosts/365api.org/htdocs;
    expires 30d;
    }


    location / {
    proxy_pass      http://127.0.0.1:8080;
    }

    }



Logs

Access / Error
/var/log/nginx/access.log
/var/log/nginx/error.log



.........................................................................................


Setup and Configure PHP

Install PHP mod_php APACHE 2 + pgsql

Centos 7
yum install http://rpms.remirepo.net/enterprise/remi-release-7.rpm
yum-config-manager --enable remi-php73 
yum install php php-cli php-curl php-xml php-pgsql php-fpm

[root@cloud-vps ~]# php -version
PHP 7.3.9 (cli) (built: Aug 27 2019 22:52:39) ( NTS )
Copyright (c) 1997-2018 The PHP Group
Zend Engine v3.3.9, Copyright (c) 1998-2018 Zend Technologies



Ubuntu
sudo apt-get install php libapache2-mod-php php-pgsql php-xml


PHP Modules -

[root@s15348388 htdocs]# php -m
[PHP Modules]
bz2
calendar
Corephp
ctype
curl
date
dom
ereg
exif
fileinfo
filter
ftp
gd
gettext
gmp
hash
iconv
json
libxml
mhash
mysql
mysqli
openssl
pcntl
pcre
PDO
pdo_mysql
pdo_pgsql
pdo_sqlite
pgsql
Phar
readline
Reflection
session
shmop
SimpleXML
sockets
SPL
standard
tokenizer
wddx
xml
xmlreader
xmlwriter
xsl
zip
zlib


PHP.ini non-default

< allow_call_time_pass_reference = Off
< safe_mode = Off
< safe_mode_include_dir =
< max_execution_time = 900     ; Maximum execution time of each script, in seconds
< memory_limit = 256M      ; Maximum amount of memory a script may consume
< display_errors = Off
< error_log = /var/log/php_errors.log
< register_argc_argv = Off
< post_max_size = 8M
< magic_quotes_gpc = Off
< ;upload_tmp_dir =
< upload_max_filesize = 5M

.........................................................................................


Create Web App DIR Structure


sudo mkdir /www
sudo mkdir /www/vhosts
sudo mkdir /www/vhosts/oneworld365.org
sudo mkdir /www/vhosts/oneworld365.org/htdocs
sudo mkdir /www/vhosts/oneworld365.org/logs
sudo mkdir /www/vhosts/365admin.org
sudo mkdir /www/vhosts/365admin.org/htdocs
sudo mkdir /www/vhosts/365admin.org/logs
sudo mkdir /www/vhosts/365api.org
sudo mkdir /www/vhosts/365api.org/htdocs
sudo mkdir /www/vhosts/365api.org/logs


.........................................................................................

Deploy Web Application Code

Git - repos


https://github.com/steveio/oneworld365.org.git
https://github.com/steveio/365admin.org.git
https://github.com/steveio/365api.org.git

Git - clone

cd /www/vhosts/oneworld365.org/htdocs
git clone https://github.com/steveio/oneworld365.org.git ./

cd /www/vhosts/365admin.org/htdocs
git clone https://github.com/steveio/365admin.org.git ./

cd /www/vhosts/365api.org/htdocs
git clone https://github.com/steveio/365api.org.git ./


Git -- initial import 
echo "# oneworld365.org" >> README.md
git init
git add *
git commit -m "first commit"
git remote add origin https://github.com/steveio/oneworld365.org.git
git push -u origin master


-- install image archive from backup
stevee@ideapad-530S:/www/vhosts/oneworld365.org/htdocs$ cp ~/code/oneworld365.org/backups/20181003/oneworld365_images_08102018.tar.gz ./
tar -zxvf oneworld365_images_08102018.tar.gz ./


SVN (legacy vps)
svn co svn+ssh://web_developer@87.106.104.174/var/www/svn/gapyear365_new ./
svn co svn+ssh://web_developer@87.106.104.174/var/www/svn/365admin ./
svn co svn+ssh://web_developer@87.106.104.174/var/www/svn/365api ./

.........................................................................................

Set File Permissions:

CENTOS

sudo usermod -a -G apache web_developer

sudo find /www/vhosts/oneworld365.org/htdocs/. -exec chown web_developer:apache {} +
sudo find /www/vhosts/oneworld365.org/htdocs/. -type f -exec chmod 644 {} +
sudo find /www/vhosts/oneworld365.org/htdocs/. -type d -exec chmod 775 {} +


sudo find /www/vhosts/oneworld365.org/htdocs/upload. -exec chown apache:apache {} +

chmod -R 775 /www/vhosts/oneworld365.org/htdocs/upload
chmod -R 775 /www/vhosts/oneworld365.org/htdocs/img
chmod -R 775 /www/vhosts/oneworld365.org/htdocs/cache


sudo find /www/vhosts/365api.org/htdocs/. -exec chown web_developer:apache {} +
sudo find /www/vhosts/365api.org/htdocs/. -type f -exec chmod 644 {} +
sudo find /www/vhosts/365api.org/htdocs/. -type d -exec chmod 775 {} +

sudo find /www/vhosts/365admin.org/htdocs/. -exec chown web_developer:apache {} +
sudo find /www/vhosts/365admin.org/htdocs/. -type f -exec chmod 644 {} +
sudo find /www/vhosts/365admin.org/htdocs/. -type d -exec chmod 775 {} +

-- logs
sudo find /www/vhosts/oneworld365.org/logs/. -exec chown apache:web_developer {} +
sudo find /www/vhosts/oneworld365.org/logs/. -type f -exec chmod 775 {} +

sudo find /www/vhosts/365admin.org/logs/. -exec chown apache:web_developer {} +
sudo find /www/vhosts/365admin.org/logs/. -type f -exec chmod 775 {} +

sudo find /www/vhosts/365api.org/logs/. -exec chown apache:web_developer {} +
sudo find /www/vhosts/365api.org/logs/. -type f -exec chmod 775 {} +



chmod +x /www/vhosts/oneworld365.org/htdocs/email_batch.sh

-- 
sudo find /root/cron/. -exec chown root:apache {} +
sudo find /root/cron/. -type d -exec chmod 775 {} +


.........................................................................................


Search Engine - SOLR / Lucene

CENTOS 7

# install java 8
yum update
sudo yum install java-1.8.0-openjdk.x86_64

[root@cloud-vps ~]# java -version
openjdk version "1.8.0_222"
OpenJDK Runtime Environment (build 1.8.0_222-b10)
OpenJDK 64-Bit Server VM (build 25.222-b10, mixed mode)

# install apache solr 8.2

wget http://apache.org/dist/lucene/solr/8.2.0/solr-8.2.0.tgz 
tar xzf solr-8.2.0.tgz solr-8.2.0/bin/install_solr_service.sh --strip-components=2
sudo bash ./install_solr_service.sh solr-8.2.0.tgz


Solr process 2692 running on port 8983
{
  "solr_home":"/var/solr/data",
  "version":"8.2.0 31d7ec7bbfdcd2c4cc61d9d35e962165410b65fe - ivera - 2019-07-19 15:11:04",
  "startTime":"2019-08-28T13:32:10.868Z",
  "uptime":"0 days, 0 hours, 0 minutes, 16 seconds",
  "memory":"184 MB (%35.9) of 512 MB"}


# enable PING requestHandler
curl http://localhost:8983/solr/collection1/admin/ping?action=enable

# main binary
/opt/solr/bin/solr

# create collection
sudo su - solr -c "/opt/solr/bin/solr create -c collection1 -n schema.xml"

# data / log dir
/var/solr/data

# copy schema.xml solrconfig.xml and restart solr
/var/solr/data/collection1/conf

# view loaded schema
curl http://localhost:8983/solr/collection1/schema


CENTOS 5


/opt/solr/collection1/conf/schema.xml


UBUNTU

Install:
stevee@ideapad-530S:~$ java -version
java version "1.8.0_181"
Java(TM) SE Runtime Environment (build 1.8.0_181-b13)
Java HotSpot(TM) 64-Bit Server VM (build 25.181-b13, mixed mode)

cd /opt
sudo wget http://www-eu.apache.org/dist/lucene/solr/7.5.0/solr-7.5.0.tgz
sudo tar xzf solr-7.5.0.tgz solr-7.5.0/bin/install_solr_service.sh --strip-components=2
sudo bash ./install_solr_service.sh solr-7.5.0.tgz

sudo bash ./install_solr_service.sh solr-7.1.0.tgz -i /opt -d /var/solr -u solr -s solr -p 8983

Paths

# sudo to SOLR user
sudo -i -u solr

/opt/solr-7.1.0
/opt/solr -> /opt/solr-7.1.0

Schema: /var/solr/data/collection1/conf/schema.xml
Data DIR - /var/solr
Logs -/var/solr/logs/solr.log

Start / Stop / Restart:

sudo -i -u solr

cd /opt/solr
./bin/solr start / status / stop

/opt/solr/bin/solr stop

# SOLR admin log
http://127.0.0.1:8983/solr/

# REST API - Delete all documents
curl http://localhost:8983/solr/collection1/update --data '<delete><query>*:*</query></delete>' -H 'Content-type:text/xml; charset=utf-8'
curl http://localhost:8983/solr/collection1/update --data '<commit/>' -H 'Content-type:text/xml; charset=utf-8'


read NPROC <<< $(/opt/solr/bin/solr status | awk '/Found[[:space:]]/ { print $2 }')
if [[ $NPROC == 1 ]];
then     
	echo  "SOLR is running" 
else 
	echo "SOLR is not running"
fi

$ echo "${IPETH0}"


.........................................................................................

Solarium (PHP SOLR Interface)

Install (included in SVN SCM code):

stevee@ideapad-530S:/www/vhosts/365admin.org/htdocs/$ cat composer.json
  {
      "require": {
          "solarium/solarium": "2.4.0"
      }
  }

/www/vhosts/365admin.org/htdocs/solarium


.........................................................................................

SOLR Search - Profile / Article Keyword Indexer

SOLR indexer is a batch cmd line process tasked with indexing company, placement and article keywords



To create or re-index all content
/usr/bin/php /www/vhosts/365admin.org/htdocs/solr/solr_indexer.php ALL PLACEMENT

/usr/bin/php /www/vhosts/365admin.org/htdocs/solr/solr_indexer.php ALL ARTICLE


Process runs via cron nightly in DELTA (incremental) index mode to index new content
/usr/bin/php /www/vhosts/365admin.org/htdocs/solr/solr_indexer.php DELTA

.........................................................................................

Cron Scheduled Jobs


As user web_developer
7,20,35,50 * * * * /usr/bin/php /www/vhosts/365admin.org/htdocs/solr/solr_indexer.php DELTA > /dev/null 2>&1 
*/5 * * * * /www/vhosts/oneworld365.org/htdocs/email_batch.sh > /dev/null 2>&1
0 4 * * 6 /www/vhosts/365admin.org/htdocs/solr/solr_reindex.sh > /dev/null 2>&1
0 2 * * 6 /www/vhosts/365admin.org/htdocs/scripts/cache_build.sh > /dev/null 2>&1
15 2 * * 6 /www/vhosts/365admin.org/htdocs/scripts/cache_all.sh 
50 * * * * /www/vhosts/365admin.org/htdocs/scripts/cache_refresh.sh


As user root

-- 
0 2 * * * /root/cron/nightly.cron.sh  > /dev/null 2>&1
*/10 * * * * /root/proc_check.bash > /dev/null 2>&1

As user postgres

0 5 * * * /var/lib/pgsql/vacuum_db.sh


.........................................................................................

Laptop (local DEV) Notes

Create VPS SSH Key and Export:

ssh-keygen -t rsa
ssh-copy-id web_developer@87.106.104.174



Setup Hosts File for Online / Offline Website Edit / View

-- copy original hosts file
sudo cp /etc/hosts /etc/hosts_online

-- create hosts offline file
stevee@ideapad-530S:~$ cat /etc/hosts_offline
127.0.0.1    oneworld365.org
127.0.0.1    api.oneworld365.org
127.0.0.1    admin.oneworld365.org

stevee@ideapad-530S:~$ cat ~/offline.sh
sudo cp /etc/hosts_offline /etc/hosts
stevee@ideapad-530S:~$ cat ~/online.sh
sudo cp /etc/hosts_online /etc/hosts
stevee@ideapad-530S:~$ chmod +x offline.sh
stevee@ideapad-530S:~$ chmod +x online.sh


alias offline='~/offline.sh'
alias online='~/online.sh'

-- test

stevee@ideapad-530S:~$ offline
stevee@ideapad-530S:~$ ping oneworld365.org
PING oneworld365.org (127.0.0.1) 56(84) bytes of data.
64 bytes from oneworld365.org (127.0.0.1): icmp_seq=1 ttl=64 time=0.065 ms

stevee@ideapad-530S:~$ ping api.oneworld365.org
PING api.oneworld365.org (127.0.0.1) 56(84) bytes of data.
64 bytes from oneworld365.org (127.0.0.1): icmp_seq=1 ttl=64 time=0.084 ms

stevee@ideapad-530S:~$ ping admin.oneworld365.org
PING admin.oneworld365.org (127.0.0.1) 56(84) bytes of data.

stevee@ideapad-530S:~$ online
stevee@ideapad-530S:~$ ping oneworld365.org
PING oneworld365.org(s15348388.onlinehome-server.info (64:ff9b::576a:68ae)) 56 data bytes
64 bytes from s15348388.onlinehome-server.info (64:ff9b::576a:68ae): icmp_seq=1 ttl=50 time=41.9 ms
^C

Configure Web Application

vi /www/vhosts/oneworld365.org/htdocs/conf/config.php

ini_set('display_errors', 1);
define('DEV',false);


......................................................................................
