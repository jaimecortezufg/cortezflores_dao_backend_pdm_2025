<?php

//PERMITIMOS PETICIONES DESDE OTROS SERVIDORES HACIA ESTE ARCHIVO
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: A-API-KEY,Origin,X-Requested-With, Content-Type, Accept, Access-Control-Request-Method');
header('Access-Control-Allow-Methods: GET,POST,OPTIONS,PUT,DELETE');
header('Access-Control-Allow-Age:1000');
header('Access-Control-Allow-Credentials: true');
header('Allow: GET,POST,OPTIONS,PUT,DELETE');

//SE INCLUYE LA CLASE PERSONA
require('classes/persona.class.php');

//SE CREA EL OBJETO
$Persona = new Persona();

//SE VALIDA QUE EL MÉTODO O PETICIÓN ENVIADA SEA LA AUTORIZADA
if($_SERVER["REQUEST_METHOD"] === "GET"){
    
    //SE VERIFICA QUE EXISTA UN VALOR EN EL PARAMETRO "t" DE LA URL
    $tipo_peticion = "";
    if(  isset($_GET["t"]) ){
        if($_GET["t"]!= ""){
            $tipo_peticion = $_GET["t"];
        }else{
            $tipo_peticion = null;
        }
    }else{
        $tipo_peticion = null;
    }

    switch($tipo_peticion){
        case "selectAll":
            //SE OBTIENEN TODOS LOS REGISTROS DE LA BASE DE DATOS
            $resultado = $Persona->obtenerPersonas();
        break;
        case "select":
            //SE VERIFICA QUE EXISTA EN EL PARÁMETRO "id" DE LA URL UN VALOR
            $id = 0;
            if( isset($_GET["id"]) ){
                if($_GET["id"] != ""){
                    $id = intval($_GET["id"]);
                }else{
                    $id = 0;
                }
            }else{
                $id = 0;
            }

            //SE REALIZAN LAS PETICIONES A LA BASE DE DATOS
            if($id > 0){
                //EL ID ES MAYOR QUE CERO, SE OBTIENE EL REGISTRO DE UNA PERSONA
                $resultado = $Persona->obtenerPersona($id);
            }else{
                //EL ID ES IGUAL A CERO, POR LO TANTO, NO SE PUEDE CONSULTAR EN LA BASE DE DATOS
                header("HTTP/1.1 412 Precondition Failed");
                $resultado = array("mensaje"=> "El parámetro del ID no es correcto", "valores"=>"");
            }
        break;
        case "insert":
            //SE INSERTA UNA NUEVA PERSONA
            $resultado = $Persona->nuevaPersona("JAIME JEOVANNY","CORTEZ FLORES","1985-09-02","7777-7777","jaimecortez@ufg.edu.sv");
        break;
        default:
            //NO SE HA ENVIADO EL TIPO DE ACCIÓN QUE SE REALIZARÁ.
            header("HTTP/1.1 412 Precondition Failed");
            $resultado = array("mensaje"=> "Se debe indicar un tipo de procesamiento de datos", "valores"=>"");
        break;
    }

}elseif($_SERVER["REQUEST_METHOD"]=="POST"){
    //SE HARÁ UN INSERT POR QUE ES UN MÉTODO POST
    $resultado = $Persona->nuevaPersona($_POST["n"],$_POST["a"],$_POST["f"],$_POST["t"],$_POST["e"]);
}else{
    //SE HA ENVIADO UN METODO NO AUTORIZADO
    header("HTTP/1.1 500 Internal Server Error");
    $resultado = array("mensaje"=> "MÉTODO NO AUTORIZADO", "valores"=>"");
}
header("Content-Type: Application/json");
echo(json_encode($resultado));
?>