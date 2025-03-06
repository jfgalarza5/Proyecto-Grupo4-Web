<?php
function guardar(){
    $fichero = "../json/productos.json";
    $file = file_get_contents($fichero);
    $productos = json_decode($file,true);
    $productos["id_max"]++;
    $productos['producto'][] = [
        'id'=> $productos["id_max"],
        "nombre" => $_POST['nombre'],
        "precio" => $_POST['precio'],
        "img" => $_POST['img'],
        "categoria" => $_POST['categoria'],
        "correo" => $_POST['correo']
    ];
    
    $identificador = fopen($fichero,"w");
    fwrite($identificador, json_encode($productos, JSON_PRETTY_PRINT));
}

function borrar(){
    $fichero = "../json/productos.json";
    $file = file_get_contents($fichero);
    $productos = json_decode($file,true);
    foreach($productos["producto"] as $index => $producto){
        if($producto["id"] == $_POST["id"]){
            unset($productos["producto"][$index]);
            break;
        }
    }
    $identificador = fopen($fichero,"w");
    fwrite($identificador, json_encode($productos, JSON_PRETTY_PRINT));
}

function editar(){
    $fichero = "../json/productos.json";
    $file = file_get_contents($fichero);
    $productos = json_decode($file,true);

    foreach ($productos['producto'] as &$producto) {
        if ($producto['id'] == $_POST["id"]) {
            $producto['nombre'] = $_POST["nombre"];
            $producto['precio'] = $_POST["precio"];
            $producto['img'] = $_POST["img"];
            $producto['categoria'] = $_POST["categoria"];
            break;
        }
    }

    $identificador = fopen($fichero,"w");
    fwrite($identificador, json_encode($productos, JSON_PRETTY_PRINT));
}

function comentar(){
    $fichero = "../json/comentarios.json";
    $file = file_get_contents($fichero);
    $comentarios = json_decode($file,true);
    $comentarios[] = [
        'id_producto'=> $_POST['id_producto'],
        "correo" => $_POST['correo'],
        "texto" => $_POST['texto'],
        "puntaje" => $_POST['puntaje']
    ];
    $identificador = fopen($fichero,"w");
    fwrite($identificador, json_encode($comentarios, JSON_PRETTY_PRINT));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if(isset($_GET['funcion'])){
        $funcion = $_GET['funcion'];
        switch($funcion){
            case 'guardar':
                guardar();
                break;
            case 'borrar':
                borrar();
                break;
            case 'editar':
                editar();
                break;
            case 'comentar':
                comentar();
                break;
        }
    }
} else {
    echo json_encode(["mensaje" => "Método no permitido."]);
}
?>
