#!/bin/bash
#   Realiza copias de seguridad de la base
#   de datos.
# Autor: Fco de Borja Sanchez
#------------------------------------------
path="/usr/share/backups"

if [[ `whoami` != "postgres" ]]; then
    echo "no soy el usuario correcto "`whoami`
    exit 1
fi

cd $path
pg_dump h123_ddnsp > $path/pgdump_coddns.sql
tar -czf `date +"%Y%m%d%H%M"`_coddns.tar.gz pgdump_coddns.sql 2>&1 > /dev/null
rm pgdump_coddns.sql 2>&1 > /dev/null

exit 0
