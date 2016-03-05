#!/bin/bash
#--- Fco de Borja Sanchez
#--------------------------
tmp_file="ddns_operation_`date +'%d%m%Y%H%M%S'`_"$RANDOM
server="127.0.0.1"
TTL="600"
#8640
KEY="/share/ddns/rndc.key"

if [ "$CODDNS_RNDC_KEY" != "" ]; then
    KEY=$CODDNS_RNDC_KEY
fi

launch(){
    nsupdate -k $KEY < /tmp/$tmp_file 2>&1
}

clean(){
	if [ "$1" != "" ]; then
	    yes|rm -f $1
	fi
}

# $1 - host name
# $2 - mode
# $3 - IP
# $4 - extra
prepare_addRow(){
    echo "server "$server > /tmp/$tmp_file

	if [ "$2" == "MX" ] || [ "$2" == "mx" ];  then
		if [ "$4" == "" ]; then
			clean /tmp/$tmp_file
			echo "ERR: Unknown priority"
			exit 4;
		fi
		echo "update add "$1" "$TTL" "$2" "$4" "$3 >> /tmp/$tmp_file
		echo "update add "$1" "$TTL" "$2" "$4" "$3 
	else
		echo "update add "$1" "$TTL" "$2" "$3 >> /tmp/$tmp_file
	fi
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

#----------- MAIN --
if [ $# -lt 3 ] || [ $# -gt 6 ]; then
    echo "ERR nARGS"
    exit 1;
fi

#-- $1 [a|d]
#-- $2 [hostname]
#-- $3 [type]
#-- $4 [ip]
#-- $5 [ttl]
#-- $6 [extra]
if [ "$5" != "" ]; then
	TTL=$5
fi

r=0
case $1 in
    "a")
        prepare_addRow $2 $3 $4 $6
        launch
		r=$?
    ;;
    "d")
        prepare_deleteRow $2 $3
        launch
		r=$?
    ;;
    *)
        echo "ERR Args malformed"
        exit 2;
    ;;
esac

clean $tmp_file
if [ "$r" != "0" ]; then
	echo "ERR $r"
	exit 3
fi
echo "OK"
exit $r
