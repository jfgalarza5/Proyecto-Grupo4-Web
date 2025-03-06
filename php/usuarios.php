<?php
function registro($tipo){
    $nuevoUser = [
        "user" => $_POST['nombre'],
        "correo" => $_POST['correo'],
        "clave" => $_POST['pass'],
        "foto"=> "img/perfiles/SinPerfil.png",
        "tipo"=> $tipo
    ];

    $fichero = "../json/usuarios.json";
    $file = file_get_contents($fichero);
    $usuarios = json_decode($file,true);
    $usuarios[$tipo][]=$nuevoUser;
    
    $identificador = fopen($fichero,"w");
    fwrite($identificador, json_encode($usuarios, JSON_PRETTY_PRINT));
}

function actualizar($tipo){
    $data = $_POST;

    $ubiTempFoto = $_FILES['foto']['tmp_name'];
    $rutaFoto = "img/perfiles/".$data['correo'].".png";
    move_uploaded_file($ubiTempFoto, "../".$rutaFoto);
    $fichero = "../json/usuarios.json";
    $file = file_get_contents($fichero);    
    $usuarios = json_decode($file,true);

    foreach($usuarios[$tipo] as &$u){
        if($u['correo']===$data['correo']){
            $u['user'] = $data['user'];
            $u['clave'] = $data['clave'];
            $u['foto'] = $rutaFoto;
            break;
        }
    }

    $identificador = fopen($fichero,"w");
    fwrite($identificador, json_encode($usuarios, JSON_PRETTY_PRINT));
}

if($_SERVER['REQUEST_METHOD']==='POST'){
    switch($_GET['funcion']){
        case 'registro':
            registro($_GET['tipo']);
            break;
        case 'actualizar':
            actualizar($_GET['tipo']);
            break;
    }
}
?>