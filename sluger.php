<?

/*

Copyright 2009 Allan Psicobyte

sluger es una aplicación de la Oficina de Software Libre (http://osl.ugr.es/) de la Universidad de Granada (http://www.ugr.es/) que permite crear atajos con URLs cortas y redireccionar al usuario a través de ellas (smilar a servicios como tinyurl o ATAJA). 

Es software libre y se distribuye bajo una licencia Affero (AFFERO GENERAL PUBLIC LICENSE: http://www.affero.org/oagpl.html).
This program is free software and it's licensed under the AFFERO GENERAL PUBLIC LICENSE (http://www.affero.org/oagpl.html).

 
*/
Init();

if ($_GET["esp"]=='JASON' || $_GET["modo"]=='JASON'){
    $Especial='JASON';
}


if ($_GET["modo"]=='go' && $_GET["url"] != ''){

    if (strlen($_GET["url"])>4){
        $url= Buscar_por_id_propia($_GET["url"]);
        if (!preg_match('"^ERROR:"',$url)){
            Logea($_GET["url"]);
            Redirecciona($url);
        }

        else {
            MuestraError($url);
        }

    }
    else {

        $url= Buscar_por_id($_GET["url"]);
        if (!preg_match('"^ERROR:"',$url)){
            Logea($_GET["url"]);
            Redirecciona($url);
        }

        else {
            MuestraError($url);
        }
    }
}



elseif (($_GET["modo"]=='new' || $_GET["modo"]=='JASON') && $_GET["url"]!= ''){
    if (FiltraIP()){
        if (TestAutoreferencia($_GET["url"])){
            if ($_GET["myid"]!=''){
        
                $url= CreaPropia($_GET["myid"],$_GET["url"]);
                if (!preg_match('"^ERROR:"',$url)){
                    Pantallamuestra($url);
                }
                else {
                    MuestraError($url);
                }
            }
            else {    
    
                $url= Crear($_GET["url"]);
                if (!preg_match('"^ERROR:"',$url)){
                    Pantallamuestra($url);
                }
                else {
                    MuestraError($url);
                }
            }
        }
        else{
            MuestraError('ERROR:autoreferencia');
        }
    }
    
    else {
        MuestraError('ERROR:ip');
    }
}


elseif($_GET["modo"]=='stats') {
    Estadistica($_GET["url"]);

}

else {

    if (FiltraIP()){
        PantallaCrea();
    }
    else {
        MuestraError('ERROR:ip');
    }
}



function Init(){
    global $Conexion, $N, $Caracteres;

    $Caracteres= array('0','1','2','3','4','5','6','7','8','9','a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z','A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z');

    $N= count($Caracteres);

    // Editar aquí los datos de usuario Y contraseña para la Base de Datos.
    $Conexion = mysql_connect("localhost","USUARIO","CONTRASEÑA");
    mysql_select_db ("sluger", $Conexion) OR die ("No se puede conectar");

}



//Pequeño validador de URLs
// Solo para ver si tiene el protocolo
function TestURL($url){
    return preg_match('"^(ftp|(http(s)?))://"',$url);
}


// Para evitar el peligro de crear URLs cortas que enlacen a sí mismas
function TestAutoreferencia($url){

    $test1= $_SERVER['HTTP_HOST'];
    $test2= 'http://'. $_SERVER['HTTP_HOST'];
    
    $result1= strpos($url,$test1);
    $result2= strpos($url,$test2);

    if ($result1=== 0 || $result2=== 0){
        return false;
    }
    else {
        
        return true;
    }
}



// Para testear que no se usan caracteres no permitidos en las URLs cortas definidas por el usuario.
function TestCaracteres($id){
    return !preg_match('"[^a-z0-9A-Z_]"',$id);
}




// Crea una URL corta automática. El usuario sólo debe indicar la URL destino
function Crear($url){
    global $Conexion;

    if (!TestURL($url)){
    $url= 'http://'. $url;
    }

    $escapada= mysql_real_escape_string($url);


    $sql="SELECT * FROM direcciones WHERE url='".$escapada."';";
    $result=mysql_query($sql,$Conexion);

    while($row = mysql_fetch_array($result)) {
        $URL_id=$row["id"];
    }
    mysql_free_result($result);
    
    if ($URL_id!=''){
        $URL_corta= Num_Cad($URL_id);

        if (!preg_match('"^ERROR:"',$URL_corta)){
            return $URL_corta;
        }

    }
    else {

        $fecha= date("Y-m-d H:i:s");

        $sql="INSERT INTO direcciones (url, creada) VALUES ('".$escapada."','".$fecha."');";
        $result=mysql_query($sql,$Conexion);
        $SQL_Error=  mysql_error();
        if ($SQL_Error!=''){
            //echo $SQL_Error;
        }
        else {

            $sql="SELECT * FROM direcciones WHERE url='".$escapada."';";
            $result=mysql_query($sql,$Conexion);

            while($row = mysql_fetch_array($result)) {
                $URL_id=$row["id"];
            }
            mysql_free_result($result);

            $URL_corta= Num_Cad($URL_id);

            if (!preg_match('"^ERROR:"',$URL_corta)){
                return $URL_corta;
            }
        }
    }
}



// Pasa de formato numérico a cadena de caracteres, para convertir ids de la BD en cadenas para la URL.
function Num_Cad($num){
    global $N, $Caracteres;

    $N4= pow($N,4);

    if (is_numeric($num) && $num < $N4 && $num > -1){

        $nume= $num;
        $Caracter[0]= $nume % $N;
    
        $nume= ($nume - $Caracter[0])/$N;
        $Caracter[1]= $nume % $N;
    
        $nume= ($nume - $Caracter[1])/$N;
        $Caracter[2]= $nume % $N;
    
        $nume= ($nume - $Caracter[2])/$N;
        $Caracter[3]= $nume % $N;
        
        $resultado= $Caracteres[$Caracter[3]] . $Caracteres[$Caracter[2]] . $Caracteres[$Caracter[1]] . $Caracteres[$Caracter[0]];
    
        return $resultado;
    }
    else {
        return 'ERROR:inválido';
    }
}


// Pasa de  cadena de caracteres a formato numérico, para convertir URLs cortas en ids de la BD.
function Cad_Num($cad){
    global $N, $Caracteres;

    $N2= pow($N,2);
    $N3= pow($N,3);

    $Caracter= str_split($cad);


    $RegExp= implode('', $Caracteres);
    $RegExp= "^[". $RegExp ."]*$";

    if (ereg($RegExp,$cad) && (strlen($cad)==4)){

        
    $Valor[0]= array_keys($Caracteres, $Caracter[0]);
    $Valor[1]= array_keys($Caracteres, $Caracter[1]);
    $Valor[2]= array_keys($Caracteres, $Caracter[2]);
    $Valor[3]= array_keys($Caracteres, $Caracter[3]);

    $numero = $N3 * ($Valor[0][0]) + $N2 * ($Valor[1][0]) + $N * ($Valor[2][0]) + $Valor[3][0];


        return $numero;

    }
    else {
        return 'ERROR:inválido';
    }
}


// Busca una URL en la BD (automáticas)
function Muestra($id){
    global $Conexion;

    $escapada= mysql_real_escape_string($id);
 
    $sql="SELECT * FROM direcciones WHERE id='".$escapada."';";
    $result=mysql_query($sql,$Conexion);

    while($row = mysql_fetch_array($result)) {
        $url=$row["url"];
    }
    mysql_free_result($result);
    
    if ($url!=''){
        $URL_corta= Num_Cad($URL_id);

        if (!preg_match('"^ERROR:"',$URL_corta)){
            return $URL_corta;
        }

    }
    else {
        return 'ERROR:noexiste';
    }
}


// Busca una URL en la BD (automáticas)
function Buscar_por_id($id){
    global $Conexion;


    $escapada= Cad_Num($id);


    if (preg_match('"^ERROR:"',$escapada)) {

        return 'ERROR:inválido';
    }
    else {
        $sql="SELECT * FROM direcciones WHERE id='".$escapada."';";
        $result=mysql_query($sql,$Conexion);

        while($row = mysql_fetch_array($result)) {
            $url=$row["url"];
        }
        mysql_free_result($result);
        if ($url!=''){

            return $url;
        }
        else {
            return 'ERROR:noexiste';
        }
    }
}


// Busca una URL en la BD (definidas por el usuario)
function Buscar_por_id_propia($id){
    global $Conexion;

        $escapada= mysql_real_escape_string($id);

        $sql="SELECT * FROM elegidas WHERE id='".$escapada."';";
        $result=mysql_query($sql,$Conexion);

        while($row = mysql_fetch_array($result)) {
            $url=$row["url"];
        }
        mysql_free_result($result);
        if ($url!=''){

            return $url;
        }
        else {
            return 'ERROR:noexiste';
        }
}





// Guarda un log de accesos
function Logea($id){
    global $Conexion;
    $fecha= date("Y-m-d H:i:s");
    $refer= $_SERVER['HTTP_REFERER'];
    $sql="INSERT INTO log (idpag, fecha, refer) VALUES ('".$id."','".$fecha."','".$refer."');";
    $result=mysql_query($sql,$Conexion);
    $SQL_Error=  mysql_error();
    

}


// La redirección, propiamente dicha
function Redirecciona($url){
    header('Location: '. $url);
}



// Formulario para crear URLs
function Pantallacrea(){


    //Si no existe o no encuentra la plantilla, muestra una página por defecto
    if (!@include("template/form.html")){
        echo '<html><head><title>Short URL</title></head><p>Introduzca la URL que desea acortar:</p><form action=""><input type="hidden" name="modo" value="new"><p>URL: <input type="text" name="url" value=""></p><p>Tambi&eacute;n puede elegir un mombre corto (ID)</p><p>ID: <input type="text" name="myid" value=""> (<em>opcional</em>)</p><input class="boton" type="submit" name="submit" value="Crear"></form><body></body></html>';
    }



}



// Esto es una exigencia del "cliente": Permite dar de alta direcciones sólo a IPs de la UGR (más o menos).
// Está comentada para que no de guerra.
function FiltraIP(){

    if  (WhiteList() && !BlackList()){
        return true;
    }
    else{
        return false;
    }
}


// retorna true si existe la lista negra y la ip está en ella
function BlackList(){

    $BlackList= 'blacklist.txt';

    $ip= $_SERVER['REMOTE_ADDR'];

    $fichero = @fopen($BlackList, "r");

    if ($fichero) {

        while (($entrada = fgets($fichero, 512)) !== false) {
            $lineas[]= rtrim($entrada);
        }
        fclose($lineas);

        foreach ($lineas as $patron){

            if (preg_match('/^.{1,3}\..{1,3}\..{1,3}\..{1,3}\z/', $patron)){

                    if (ComparaIP($ip,$patron)){
                        return true;
                    }
            }
        }

        return false;
    }

    else {
        return false;
    }
}


// retorna true si NO existe la lista blanca, o si existe y la ip está en ella
function WhiteList(){

    $WhiteList= 'whitelist.txt';

    $ip= $_SERVER['REMOTE_ADDR'];

    $fichero = @fopen($WhiteList, "r");

    if ($fichero) {

        while (($entrada = fgets($fichero, 512)) !== false) {
            $lineas[]= rtrim($entrada);
        }
        fclose($lineas);

        foreach ($lineas as $patron){

            if (preg_match('/^.{1,3}\..{1,3}\..{1,3}\..{1,3}\z/', $patron)){

                    if (ComparaIP($ip,$patron)){
                        return true;
                    }
            }
        }

        return false;

    }
    else {
        return true;
    }
}


// Retorna true si la IP en $ip concide con el patron (del tipo 192.168.*.*) en $patron
function ComparaIP($ip,$patron){

    $ipArr= explode( '.' , $ip);
    $patronArr= explode( '.' , $patron);

    for ($i = 0; $i <= 3; $i++) {
        if ($patronArr[$i] != "*" && $patronArr[$i] != $ipArr[$i]){
            return false;
        }
    }
    return true;
}


//Si todo ha ido bien, muestra el resultado en una página al efecto:
function Pantallamuestra($id){
    global $Especial;



if ($Especial=='JASON'){
        MuestraJASON($id,'1','URL Creada');
    }
    else{
        //Si no existe o no encuentra la plantilla, muestra una página por defecto
        if (!@include("template/result.html")){
            echo '<html><head><title>Short URL</title></head><p>Se ha creado la URL corta '. $_SERVER['HTTP_HOST'] . '/' . $id  .'</p><body></body></html>';
        }
    }

}



//Under construction: Módulo de estadísticas de uso
function Estadistica($id){
    global $Conexion;

    $lista= array();
    $escapada= mysql_real_escape_string($id);

    
    if (strlen($id)==4){
        $num= Cad_Num($id);
        if (!preg_match('"^ERROR:"',$num)){
            $sql="SELECT * FROM direcciones WHERE id=".$num.";";
            $result=mysql_query($sql,$Conexion);
            while($row = mysql_fetch_array($result)) {
                $creada=$row["creada"];
            }
            
            $SQL_Error=  mysql_error();
                if ($SQL_Error!=''){
                    //echo $SQL_Error;
                }
            
            
            mysql_free_result($result);
            if ($creada==''){
                $Error= 'ERROR:noexiste';
            }
        }
        
        else {
            $Error= 'ERROR:noexiste';
        }
    }
    elseif(strlen($id)>4){
        $sql="SELECT * FROM elegidas WHERE id='".$escapada."';";
        $result=mysql_query($sql,$Conexion);
        while($row = mysql_fetch_array($result)) {
            $creada=$row["creada"];
        }
        mysql_free_result($result);
        if ($creada==''){
            $Error= 'ERROR:noexiste';
        }
    }

    else {
        $Error= 'ERROR:noexiste';
    }

    if ($Error==''){
        $sql="SELECT * FROM log WHERE idpag='".$escapada."';";
        $result=mysql_query($sql,$Conexion);

        while($row = mysql_fetch_array($result)) {
            $Total++;
            if ($row["refer"]!=''){
                $lista[$row["refer"]]++;
            }
        }
        mysql_free_result($result);
    
        arsort($lista);
        MuestraStats($Total,$lista,$creada);
    }
    else {
        MuestraError($Error);
        }
    
}


//Para crear una URL con un nombre asignado por el usuario
function CreaPropia($id,$url){
    global $Conexion;


    if (!TestURL($url)){
        $url= 'http://'. $url;
    }


    if ((strlen($id) > 4) && (strlen($id) < 51)){

        if (TestCaracteres($id)){

            $idescapada= mysql_real_escape_string($id);
            $urlescapada= mysql_real_escape_string($url);

            $sql="SELECT * FROM elegidas WHERE id='".$idescapada."';";
            $result=mysql_query($sql,$Conexion);

            while($row = mysql_fetch_array($result)) {
                $idb=$row["id"];
            }
            mysql_free_result($result);

            if ($idb != ''){
                return 'ERROR:existe';
            }
            else {

                $fecha= date("Y-m-d H:i:s");
                $sql="INSERT INTO elegidas (id, url, creada) VALUES ('".$idescapada."','".$urlescapada."','".$fecha."');";
                $result=mysql_query($sql,$Conexion);
                $SQL_Error=  mysql_error();
                if ($SQL_Error!=''){
                    //echo $SQL_Error;
                }
                else {
                    return $id;
                }
            
            }
        }
        else {
            return 'ERROR:caracteres';
        }
            
    }
    else{
        return 'ERROR:tamaño';
    }

}



// Pantallas de Errores
function MuestraError($Iderror){
    global $Especial;

    $Errores= array('ERROR:ip'=>'Lo siento. Me temo que estas URLs cortas s&oacute;lo se pueden dar de alta desde la red de la UGR (pero esto es s&oacute;lo para darlas de alta, no para usar la redirecci&oacute;n)','ERROR:autoreferencia'=>'No se puede llamar a una URL corta con una URL corta, es parad&oacute;jico. El universo conocido podr&iacute;a colapsar por autoreferencias como esta, y t&uacute; no quieres ser el culpable de algo como eso...','ERROR:tamaño' => 'La ID elegida como URL corta debe tener entre 5 y 50 caracteres (aunque 50 probablemente sea exagerar la definici&oacute;n de la palabra "corta")','ERROR:caracteres' => 'S&oacute;lo se admiten letras, n&uacute;meros y el signo "_". Pero las letras puden ser min&uacute;sculas y may&uacute;sculas, que ya es algo (en el futuro se admitir&aacute;n n&uacute;meros may&uacute;sculos, pero a&uacute;n no los hemos encontrado en este teclado)','ERROR:existe' => 'La cadena elegida como url corta ya existe (te han pillado la vez, prueba combinando min&uacute;sculas y may&uacute;sculas y esas cosas)','ERROR:noexiste' => 'No existe la URL corta elegida (es tu oportunidad para crearla, aprovecha la ocasi&oacute;n)','ERROR:inválido' => 'Código inválido: O sea, que has metido caracteres que este programa se niega a reconocer');

    $TextoError= $Errores[$Iderror];

    if ($Especial=='JASON'){
        MuestraJASON('','0',$TextoError);
    }
    else{
        //Si no existe o no encuentra la plantilla, muestra una página por defecto
        if (!@include("template/error.html")){
            echo '<html><head><title>Short URL</title></head><body>'. $TextoError .'</body></html>';
        }
    }
}






function MuestraJASON($id,$error,$texto){

    if ($id!=''){
        $id=  $_SERVER['HTTP_HOST'] . '/' . $id;
    }

    echo json_encode( array('url' => $id, 'error' => $error, 'text' => $texto) );

}




// Pantallas de Estadísticas
function MuestraStats($visitas,$lista,$creada){
    global $Tabla;
    $Tabla= '<table class="stats"><tr><th>URL</th><th>Visitas</th></tr>' ."\n";
    
    
    
    foreach($lista as $key => $val) {
        
        $muestra= preg_replace('"^(ftp|(http(s)?))://"', '', $key);
        
        if (strlen($muestra)>55){
            $muestra= substr($muestra,0,52).'...';
        }
        $Tabla.= '<tr><td><a href="'.$key.'">'.$muestra.'</a></td><td>'.$val.'</td></tr>' ."\n";
    }
    $Tabla.= '</table>'."\n";
    
        //Si no existe o no encuentra la plantilla, muestra una página por defecto
        if (!@include("template/stats.html")){
            echo '<html><head><title>Short URL</title></head><body><p>N&uacute;mero de visitas totales: '. $visitas .'</p>';
            echo $Tabla;
            echo '</body></html>';
        }

}

?>
