#!/bin/bash
#--- Fco de Borja Sanchez
#--------------------------
tmp_file="ddns_operation_`date +'%d%m%Y%H%M%S'`_"$RANDOM
server="127.0.0.1"
TTL="8460"
#8640
KEY="/share/ddns/rndc.key"

# $1 - host name
# $2 - mode
# $3 - IP
prepare_addRow(){
    echo "server "$server > /tmp/$tmp_file
    echo "update add "$1" "$TTL" "$2" "$3 >> /tmp/$tmp_file
    echo "send" >> /tmp/$tmp_file
    echo "quit" >> /tmp/$tmp_file
}

# $1 - host name
# $2 - host type
prepare_deleteRow(){
    echo "server "$server > /tmp/$tmp_file
    echo "update delete "$1" "$2 >> /tmp/$tmp_file
    echo "send" >> /tmp/$tmp_file
    echo "quit" >> /tmp/$tmp_file
}

launch(){
    nsupdate -k $KEY < /tmp/$tmp_file 2>&1
}

clean(){
    yes|rm -f $1
}

#----------- MAIN --
if [ $# -lt 3 ] || [ $# -gt 4 ]; then
    echo "nARGS ERR"
    exit 1;
fi

#-- $1 [a|d]
#-- $2 [hostname]
#-- $3 [type]
#-- $4 [ip]

case $1 in
    "a")
        prepare_addRow $2 $3 $4
        launch
    ;;
    "d")
        prepare_deleteRow $2 $3
        launch
    ;;
    *)
        echo "ARGS MALFORMED"
        exit 2;
    ;;
esac

clean $tmp_file

