<?php
$data = $_POST;

$ubiTempFoto = $_FILES['foto']['tmp_name'];
$rutaFoto = "img/publicidad/".$data['correo'].".png";
move_uploaded_file($ubiTempFoto, "../".$rutaFoto);
$fichero = "../json/publicidad.json";
$file = file_get_contents($fichero);    
$publicidad = json_decode($file,true);

$publicidadNuevo = [
    "correo" => $data["correo"],
    "banner" => $rutaFoto
];

$existe = false;
foreach($publicidad as &$p){
    if($p['correo']==$data['correo']){
        $p['banner'] = $rutaFoto;
        $existe = true;
        break;
    }
}

if(!$existe){
    $publicidad[]=$publicidadNuevo;
}

$identificador = fopen($fichero,"w");
fwrite($identificador, json_encode($publicidad, JSON_PRETTY_PRINT));
?>