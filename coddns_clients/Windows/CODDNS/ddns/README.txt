¡Hola!

Gracias por descargar el actualizador de DNS dinámico para Windows.
Lea este documento para poder utilizarlo correctamente.

Saludos!

Borja Sánchez

CONFIGURACIÓN
 El fichero de configuración debe ser ajustado de la siguiente forma:
  usuario:ejemplo@coddns.org
  password:su_password
  host:ejemplo.coddns.org

 En usuario: introduzca el correo electrónico con que se ha registrado en
 http://coddns.org

 En password: introduzca la contraseña que ha elegido al crear su cuenta en
 http://coddns.org

 En host: indique de manera completa, el nombre que ha elegido para su host
 por ejemplo, si ha elegido la etiqueta casa, introduzca el siguiente valor

 casa.coddns.org

PRUEBAS Y EJECUCIÓN MANUAL
 Una vez configurado el fichero de datos de usuario, puede ejecutar el script
 de actualización manualmente mediante:


  > cscript ddns_updater.vbs ddns_userdata.conf


INSTALACIÓN
 Si desea instalarlo y que se ejecute por un cierto intervalo de tiempo, copie
 el script a una carpeta donde no lo elimine (en el ejemplo C:\CODDNS\ddns\)

	1. Crear carpeta destino
		> makedir C:\CODDNS\ddns\
	2. Extraiga el contenido de ddns_updater.zip en dicha carpeta
	3. Configure el fichero ddns_userdata.conf de acuerdo a las indicaciones vistas
	   en el apartado CONFIGURACIÓN
	4. Creamos una tarea programada mediante:
		> SCHTASKS /Create /RU SYSTEM /SC HOURLY /TN "DDNS_UPDATER" /TR "cscript C:\CODDNS\ddns\ddns_updater.vbs C:\CODDNS\ddns\ddns_userdata.conf"

