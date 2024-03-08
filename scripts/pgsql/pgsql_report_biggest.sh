#!/bin/bash

. $HOME/.bash_profile
/usr/bin/psql -U postgres -d oneworld365 -c 'SELECT relname, reltuples, relpages FROM pg_class ORDER BY relpages DESC;'


