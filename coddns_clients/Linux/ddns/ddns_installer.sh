#!/bin/bash
# Instalador del sistema de seguimiento IP dinamico
# Autor: Fco de Borja Sanchez
#---------------------------------------------------

# definiciones
ddnsconf=`cat /etc/passwd | grep $USER | head -1| cut -f6 -d':'`"/.userdata"
global_ddnsconf_root="/usr/share/ddns"
global_ddnsconf="/usr/share/ddns/userdata"
updater="./ddns_updater.sh"
ruta="/opt/ddns"
HOUR_FREQ=6

# Funciones
check_release(){
    if [ "`cat /etc/*-release |grep NAME | head -1 | cut -f 2 -d"="`" = "Fedora" ]; then
        release="fedora"
        installer="yum"
        programador="chkconfig"
    elif [ "`cat /etc/*-release |grep DISTRIB | head -1 | cut -f 2 -d"="`" = "Ubuntu" ]; then
        release="ubuntu"
        installer="apt-get"
        programador="update-rc.d"
    elif [ "`cat /etc/*-release |grep NAME | tail -1 | cut -f 2 -d"="`" = "\"Debian GNU/Linux\"" ]; then
        release="debian"
        installer="apt-get"
        programador="update-rc.d"
    else
        read -p "Indique su distribucion: " release
        read -p "Introduzca el comando que utiliza para instalar paquetes: " installer
        read -p "Introduzca el comando que utiliza para programar servicios al inicio (p.e. update-rc.d): " programador
    fi
        echo "Resumen:
  Sistema: $release
  Se utilizara $installer para las posibles necesidades de instalacion
  Se utilizara $programador para asignar al inicio del sistema"

}

add_cron_entry(){
	#0-59 0-23 1-31 1-12 0-6 usuario comando
	minutes=`shuf -i 0-59 -n 1`
	start_hour=`shuf -i 0-3 -n 1`
	hours=$start_hour
	salida=$start_hour
	freq=1
	cuanto=`expr 24 / $HOUR_FREQ`
	while [ $freq -ne $HOUR_FREQ ]; do
		incr=`expr $freq \* $cuanto`
		salida=`echo $salida","$(expr $hours + $incr)`
		freq=`expr $freq + 1`
	done

	rt_horas=$salida
	rt_minutos=$minutes
}

sn_func(){
	readd=""
	while [ "$readd" != "S" ] && [ "$readd" != "s" ] && [ "$readd" != "n" ] && [ "$readd" != "N" ]; do
		read -p "$1" readd
	done
}

mmenu() {
	echo ""
	echo "  Gestor de seguimiento IP dinamico"
	echo ""
	echo "    1. Instalar"
	echo "    2. Desinstalar"
	echo "    3. Forzar actualizacion IP"
	echo ""
	echo "    0. Salir"
	echo ""
	read -p "Seleccione: " mmenuRet
}

ask_userdata(){

	# Guardamos los datos de usuario
	usuario=""
	while [ "$usuario" = "" ]; do
		read -p "Usuario: " usuario
	done
	password=""
	while [ "$password" = ""  ]; do
        if [ "$0" = "./ddns_installer.sh" ]; then
    		read -s -p "Password: " password
        else
            read -p "Password: " password
        fi
	done
    echo ""
	host=""
	while [ "$host" = "" ]; do
		read -p "Host [p.e. mimaquina.coddns.org]: " host
	done

	echo ""
	echo "  usuario:"$usuario > $ddnsconf
	echo "  password:"`echo $password| tr -d '\n'|base64` >> $ddnsconf
    echo "  host:"$host >> $ddnsconf
	echo ""
}

#-------------------------
# main
#-------------------------

if [ ! -f $updater ]; then
	echo "No se ha encontrado $updater, por favor, vuelva a descargar el instalador"
fi

mmenuRet=""
while [ "$mmenuRet" != "0" ]; do
	mmenu
	echo "Has seleccionado "$mmenuRet
	case $mmenuRet in
		"1")
			echo "Revisando el sistema..."
			check_release
			echo ""
			echo "Comprobando correcto funcionamiento:"
			ask_userdata
			$updater
			if [ $? -ne 0 ]; then
				echo "Comprueba los avisos antes de instalarlo."
				exit 1;
			else
				echo "bien"
			fi
			echo ""
			echo "Comprobado correcto funcionamiento del actualizador, procedemos a la instalacion"
			echo ""
            read -p "  Seleccione ruta de instalacion [por defecto /opt/ddns]" readd
			if  [ "$readd" != "" ]; then
				ruta=$readd
			fi
			if [ ! -d $ruta ]; then
				sn_func "    El destino $ruta no existe, desea crearlo? (S/N) "
				if [ "$readd" = "S" ] || [ "$readd" = "s" ]; then
					echo "  Creando $ruta"
					mkdir -p $ruta
					if [ $? -ne 0 ]; then
						echo "   [ERROR]: $ruta no valida";
						exit 2;
					fi
				fi
			fi
			cp $updater $ruta"/ddns_updater.sh" > /dev/null 2>&1
			if [ $? -ne 0 ]; then
				echo "   [ERROR]: No he podido escribir en $ruta, tienes permisos?"
				exit 3;
			fi

			# Limpiamos primero las posibles entradas en el crontab (instalaciones repetidas)
			cat /etc/crontab | grep -v ddns > /tmp/ddns.tmp
            cat /tmp/ddns.tmp > /etc/crontab
			add_cron_entry
            # Copiamos la configuracion a global
            if [ ! -d $global_ddnsconf_root ]; then
                mkdir -p $global_ddnsconf_root > /dev/null 2>&1
            fi
            cp $ddnsconf $global_ddnsconf
            # Linkamos la aplicacion a la consola de sistema
			ln -s $ruta"/ddns_updater.sh" /bin/ddns_updater > /dev/null 2>&1
            # Agregamos la entrada a CRON
			echo $rt_minutos $rt_horas \* \* \* " root "$ruta"/ddns_updater.sh"' > /dev/null 2>&1' >> /etc/crontab
			echo "Instalado con exito"
		;;
		"2")
			echo "has seleccionado desinstalar"
			rm -f /bin/ddns_updater > /dev/null 2>&1
			if [ $? -ne 0 ]; then
				echo " [ERROR]: No tienes permisos de superusuario"
				exit 3;
			fi
			cat /etc/crontab | grep -v ddns > /tmp/ddns.tmp
			cat /tmp/ddns.tmp > /etc/crontab

			rm -f $ddnsconf /tmp/ddns.tmp > /dev/null 2>&1
			echo "Eliminadas las tareas programadas y desinstalado con exito"
		;;
		"3")
			echo "has seleccionado forzar actualizacion IP"
			if [ ! -f $ddnsconf ]; then
				ask_userdata
			fi
			$updater
		;;
		"0")
			echo "has seleccionado salir"
		;;
		*)
			echo "has seleccionado algo que no entiendo"
		;;
	esac
done
