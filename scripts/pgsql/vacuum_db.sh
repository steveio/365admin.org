#!/bin/bash

PGDB=

# vacuum the databases (run as user postgres)

PGPASSWORD=$PGDB /usr/bin/psql -d oneworld365 -c "VACUUM FULL;"
PGPASSWORD=$PGDB /usr/bin/psql -d postgres -c "VACUUM FULL;"
PGPASSWORD=$PGDB /usr/bin/psql -d template1 -c "VACUUM FULL;"
PGPASSWORD=$PGDB /usr/bin/psql -d oneworld365 -c "REINDEX DATABASE oneworld365;"
PGPASSWORD=$PGDB /usr/bin/psql -d oneworld365 -c "ANALYZE;"

