
sluger es una aplicación de la Oficina de Software Libre (http://osl.ugr.es/) de la Universidad de Granada (http://www.ugr.es/) que permite crear atajos con URLs cortas y redireccionar al usuario a través de ellas (smilar a servicios como tinyurl o ATAJA). 



Requisitos:

Apache con mod_rewrite habilitado.
PHP
MySQL



Instalación:

Crear la Base de Datos "sluger" usando el script SQL "DataBase.sql".

Necesitará un usuario con permisos de lectura/escritura en esa BD.

Copiar al directorio raiz del dominio que va a usarse los archivos sluger.php, modelo_htacces y, opcionalmente, las plantillas, ayuda, hoja de estilo e imágenes.

Renombrar o copiar el archivo "modelo_htacces" a ".htacces".

Editar, en la función Init() del archivo sluger.php, los datos de usuario y contraseña para la conexión a la BD.



API:

La API de slugEr permite a otras aplicaciones interactuar directamente con el programa.

Solicitud:

http://sl.ugr.es/sluger.php?modo=JASON&url=www.direccion.larga.com&id=NombreCorto

url: La URL larga de la que se quiere crear una versión corta.

id: (opcional) La cadena para definir tu propia URL corta (de 5 a 50 caracteres).

ejemplo: la cadena "NombreCorto" crearía la URL "http://sl.ugr.es/NombreCorto".


Respuesta:

{"url":"URL","error":"0|1","text":"Texto (mas o menos) descriptivo"}


url: La URL corta creada, si es el caso. En caso de error contendrá una cadena vacía.

error: Contendrá 1 si se ha creado la URL, y 0 si ha habido algún error y no ha podido crearse.

text: Texto explicativo del error, si lo hubiere. En caso de que no haya error retornará el texto "URL Creada".



Licencia:

Copyright 2009 Allan Psicobyte

sluger es una aplicación de la Oficina de Software Libre (http://osl.ugr.es/) de la Universidad de Granada (http://www.ugr.es/) que permite crear atajos con URLs cortas y redireccionar al usuario a través de ellas (smilar a servicios como tinyurl o ATAJA). 

Es software libre y se distribuye bajo una licencia Affero (AFFERO GENERAL PUBLIC LICENSE: http://www.affero.org/oagpl.html).

This program is free software and it's licensed under the AFFERO GENERAL PUBLIC LICENSE (http://www.affero.org/oagpl.html).
