#!/bin/bash
# Script de actualizacion de datos IP de cliente
#  Programar al inicio del sistema / cada 4h
# Autor: Fco de Borja Sanchez
#------------------------------------------------

# datos

ddnsconf=`cat /etc/passwd | grep $USER |head -1 | cut -f6 -d':'`"/.userdata"
global_ddnsconf="/usr/share/ddns/userdata"
dest="http://devel.coddns.org/cliupdate.php"
release=""
installer=""

# funciones
check_release(){
	if [ "`cat /etc/*-release |grep NAME | head -1 | cut -f 2 -d"="`" = "Fedora" ]; then
		release="fedora"
		installer="yum"
	elif [ "`cat /etc/*-release |grep DISTRIB | head -1 | cut -f 2 -d"="`" = "Ubuntu" ]; then
		release="ubuntu"
		installer="apt-get"
	elif [ "`cat /etc/*-release |grep NAME | tail -1 | cut -f 2 -d"="`" = "\"Debian GNU/Linux\"" ]; then
		release="debian"
		installer="apt-get"
	else
		#read -p "introduzca el comando que utiliza para instalar paquetes: " installer
        echo "  [ERROR]: Sistema no soportado"
        exit 4;
	fi
}

#-------
# MAIN
#-------

# comprobaciones previas

curl --version > /dev/null 2>&1
if [ $? != 0 ]; then
    echo "cURL es necesario para la correcta ejecucion del script."
	check_release
    echo "    Instalando... usando $installer"
    (yes | $installer install curl) > /dev/null 2>&1
    if [ $? != 0 ]; then
        echo " [ERROR]: Error al instalar curl. Lance este script con permisos de super usuario"
        exit 1;
    else
        echo "    instalado correctamente."
    fi
fi


if [ ! -f $ddnsconf ]; then
    ddnsconf=$global_ddnsconf
elif [ ! -f $ddnsconf ]; then
    echo "  [ERROR]: datos de conexion no encontrados, por favor, reinstale"
    exit 2;
fi

# inicio solicitudes
usuario=`cat $ddnsconf | grep usuario | cut -f 2 -d':'`
password=`cat $ddnsconf |grep password |cut -f 2 -d':'`
host=`cat $ddnsconf |grep host |cut -f 2 -d':'`

echo "  datos de conexion: "
echo "  "$usuario"/"$password
echo "  HOST: "$host
echo "  conectando con servidor ddns"
r=`curl --data 'u='$usuario'&p='$password'&h='$host $dest 2>/dev/null`
if [ $? -eq 0 ]; then
    echo "  conexion completada con exito"
    echo "  Recibido mensaje: "$r
else
	echo "  [ERROR]: error en la conexion"
	exit 3;
fi
