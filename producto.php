<?php
    $producto_encontrado = false;
    if(isset($_GET["id"])){
        $fichero = "json/productos.json";
        $file = file_get_contents($fichero);
        $productos = json_decode($file,true);
        foreach($productos["producto"] as $index => $e){
            if($e["id"] == $_GET["id"]){
                $producto = $e;
                $producto_encontrado = true;
            }
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php
        if($producto_encontrado){
            echo $producto["nombre"];
        }else{
            echo "Producto no disponible";
        }
    ?></title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/style.css">
    <script src="https://kit.fontawesome.com/5f232b4c5b.js" crossorigin="anonymous"></script>
    <script src="js/JQueryV3.7.1.js"></script>
    <script src="js/jquery.easing.1.3.min.js"></script>
    <script>
        var user;
        mostrarComentario();
        async function mostrarComentario(){
            comentarios = await $.getJSON("json/comentarios.json");
            usuarios = await $.getJSON("json/usuarios.json");
            usuarios = usuarios.cliente.concat(usuarios.vendedor);
            comentarios.forEach(c => {
                if(c.id_producto != <?php echo $_GET["id"] ?>) return;
                let $img;
                let $h4;
                usuarios.forEach(u => {
                    if(u.correo == c.correo){
                        $img = $("<img>").attr("src", u.foto);
                        $h4 = $("<h4>").text(u.user);
                        return;
                    }
                });
                let $li = $("<li>").addClass("item_comentario");
                let $div_coment = $("<div>").addClass("comentario");
                let $div_star = $("<div>").addClass("stars-rating");
                let $p = $("<p>").text(c.texto);

                for (let i = 1; i <= 5; i++) {
                    if (i <= c.puntaje) {
                        $div_star.append("★");
                    } else {
                        $div_star.append("☆");
                    }
                }

                $div_coment.append($h4, $div_star, $p);
                $li.append($img, $div_coment);

                $("#lista_comentario").append($li); 
            });
        }

        $(function () {
            let puntaje = 0;
            if(JSON.parse(window.localStorage.getItem("credencial"))){
                user = JSON.parse(window.localStorage.getItem("credencial"));
            }else{
                $(".form_comentario").hide();
            }
            $(".estrella").click(function () {
                for (let i = 1; i <= 5; i++) {
                    let star = $("#star" + i + " + label");
                    star.css("color", "");
                    if (i <= $(this).val()) {
                        star.css("color", "#f39c12");
                        puntaje = $(this).val();
                    }
                }
            });

            $("#icon_user").hover(function(){
                $(this).stop().animate({ top: "-3px" }, 200, "easeOutBounce");
            }, function(){
                $(this).stop().animate({ top: "0px" }, 200);
            });

            $("#icon_cart").click(function(){
                $(this).parent().css('overflow', 'hidden');
                $(this).stop().animate(
                    {
                        left: "50px"
                    },
                    1000,
                    "easeOutBounce",
                    function(){
                        $(this).css("left", "-50px");
                    }
                )
            });

            $("#icon_close").click(function(){
                $("#icon_cart").stop().animate(
                    {
                        left: "0"
                    },
                    1000,
                    "easeOutBounce"
                )
            });

            $("form").submit(function (e) {
                e.preventDefault();
            });

            $("#btn_comentar").click(function () {
                if(puntaje==0){
                    $("#mensajeError").removeClass("d-none").hide().fadeIn();
                    $("#mensajeError").text("El puntaje minimo es de una estrella");
                    setTimeout(function(){
                        $("#mensajeError").fadeOut(function(){
                            $(this).addClass("d-none");
                        });
                    }, 3000);
                    return;
                }
                $.ajax({
                    url: "php/productos.php?funcion=comentar",
                    method: "POST",
                    data: {
                        id_producto: <?php echo $_GET["id"]?>,
                        correo: user.correo,
                        texto: $("#texto_comentario").val(),
                        puntaje: puntaje
                    },
                    success: function (respuesta) { 
                        window.location.reload(true);
                    }, 
                    error: function (xhr, status, error) { 
                        console.error("Error en la solicitud AJAX:", status, error); 
                    }
                });
            });

            $("#num_producto").on("input", function () {
                let regex = /^\d+$/;
                if (!regex.test($(this).val()) || $(this).val() === 0) {
                    $(this).val(1);
                }
            });

            $("#btn_add").click(function () {
                let precio = $("#num_producto").val() * 16;
                let cantidad = $("#num_producto").val();
                $("#modal_container").empty();
                $("#modal_container").html('<div class="carrito-producto border-1 border-bottom pb-2"><div class="col-1"><img src="https://shop-cons.com/cdn/shop/files/IMG-2631_400x.jpg?v=1734447515"></div><div><b>BUCKET VERDE ♂</b></div><div><p class="m-0">Cant: ' + cantidad + '</p></div><div><b>Total: ' + precio + '</b></div><div><i class="fas fa-trash-can" id="icon_trash" onclick="borrar()"></i></div></div>');

                $("#icon_prod").animate(
                    {
                        bottom: "19px"
                    },
                    1000,
                    "easeOutBounce",
                    function(){
                        $(this).css("bottom", "50px");
                    }
                )
            });
        });

        function borrar(){
            $("#modal_container").empty();
            $("#modal_container").html('<div class="col-auto"><span style="color: #ddd;"><i class="fas fa-cart-shopping fa-2x"></i></span> </div><div class="col-12 text-center mt-4"><p class="text-muted">No tienes productos en tu carrito</p></div>');
        }
    </script>
</head>

<body>
    <header class="ps-5 pe-5">
        <nav class="navbar navbar-expand-lg navbar-light">
            <div class="d-flex align-items-center col-2">
                <a class="logo navbar-brand me-5" href="index.html">Mathion</a>
            </div>
            <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasScrolling"
                aria-controls="offcanvasScrolling">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse">
                <div class="d-flex justify-content-between w-100">
                    <ul class="navbar-nav gap-2 col-lg-3">
                        <li class="nav-item">
                            <a class="nav-link" href="hombres.html">Hombres ♂</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="mujeres.html">Mujeres ♀</a>
                        </li>
                    </ul>
                    <div class="w-100 row justify-content-center align-items-center">
                        <form class="form-inline d-flex col-8" role="search">
                            <div class="form-group w-100">
                                <input class="form-control me-2 border-top-0 border-end-0 border-start-0" type="search"
                                    placeholder="Busqueda" aria-label="Busqueda">
                            </div>
                        </form>
                    </div>
                    <div  class="iconos col-2 d-flex justify-content-end">
                        <ul class="navbar-nav gap-4">
                            <li class="nav-item dropdown  d-none d-lg-flex">
                                <a class="nav-link dropdown-toggle " href="#" id="navbarDropdown" role="button"
                                    data-bs-toggle="dropdown" aria-expanded="false">
                                    <i id="icon_user" class="fa fa-user text-dark fa-xl"></i>
                                </a>
                                <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                                    <li><a class="dropdown-item" href="login.html">Iniciar Sesion</a></li>
                                    <li><a class="dropdown-item" href="registro.html">Registrarse</a></li>
                                </ul>
                            </li>
                            <li class="nav-item">
                                <a style="overflow: hidden;" class="nav-link position-relative text-decoration-none" href=""
                                    data-bs-toggle="modal" data-bs-target="#exampleModal">
                                    <i style="position: absolute; bottom: 50px; right: 0;" id="icon_prod" class="fab fa-shopify text-dark"></i>
                                    <i id="icon_cart" class="fa fa-cart-arrow-down text-dark fa-xl"></i>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </nav>
    </header>

    <!-- Modal -->
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content px-5">
                <div class="modal-body">
                    <div class="container">
                        <div class="row justify-content-between mt-4">
                            <div class="col-auto">
                                <p>Carrito</p>
                            </div>
                            <div class="col-auto">
                                <button id="icon_close" type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                        </div>
                        <div class="row justify-content-center mt-5" id="modal_container">
                            <div class="col-auto">
                                <span style="color: #ddd;">
                                    <i class="fas fa-cart-shopping fa-2x"></i>
                                </span>
                            </div>
                            <div class="col-12 text-center mt-4">
                                <p class="text-muted">No tienes productos en tu carrito</p>
                            </div>
                        </div>
                        <div class="row justify-content-center mt-5 mb-5">
                            <div class="col-auto">
                                <a href="" class="btn btn-outline-dark">Continuar comprando</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasScrolling"
        aria-labelledby="offcanvasScrollingLabel">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title" id="offcanvasScrollingLabel"></h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body row">
            <div class="w-100 row justify-content-center align-items-center">
                <form class="form-inline d-flex col-10" role="search">
                    <div class="form-group w-100">
                        <input class="form-control me-2" type="search" placeholder="Busqueda" aria-label="Busqueda">
                    </div>
                </form>
            </div>
            <ul class="navbar-nav gap-2 px-5">
                <li class="nav-item d-flex justify-content-between align-items-center">
                    <a class="nav-link" href="hombres.html">Hombres</a>
                    <i class="fas fa-angle-right"></i>
                </li>
                <li class="nav-item d-flex justify-content-between align-items-center">
                    <a class="nav-link" href="mujeres.html">Mujeres</a>
                    <i class="fas fa-angle-right"></i>
                </li>
            </ul>
            <ul class="navbar-nav gap-2 px-5 align-self-end mb-5">
                <li class="nav-item d-flex justify-content-between align-items-center">
                    <a class="nav-link" href="login.html">Iniciar Sesion</a>
                    <i class="fas fa-user"></i>
                </li>
                <li class="nav-item d-flex justify-content-between align-items-center">
                    <a class="nav-link" href="registro.html">Registrarse</a>
                    <i class="fas fa-user-plus"></i>
                </li>
                <li class="nav-item d-flex justify-content-between align-items-center" data-bs-toggle="modal"
                    data-bs-target="#exampleModal">
                    <p>Carrito</p>
                    <i class="fas fa-cart-shopping"></i>
                </li>
            </ul>
        </div>
    </div>

    <section class="products m-5">
        <?php
            if($producto_encontrado){
                if($producto["categoria"]=="hombre"){
                    $link = "hombres.html";
                    $categoria = "Hombres";
                }else{
                    $link = "mujeres.html";
                    $categoria = "Mujeres";
                }
                echo 
                '<div class="pagina_producto">
                    <div class="contenedor_producto">
                        <div>
                            <img id="producto_img" src="'.$producto["img"].'">
                        </div>
                        <div class="detalle_producto mt-4">
                            <h1 id="nombre">'.$producto["nombre"].'</h1>
                            <p id="precio" class="precio_producto">$'.$producto["precio"].'</p>
                            <div class="cantidad">
                                <input type="number" name="" id="num_producto" class="m-3" min="1" value="1">
                                <a class="btn btn-primary" id="btn_add">Agregar al Carrito</a>
                            </div>
                            <hr class="mb-3" style="margin: 0;">
                            <p>Categoría: <a id="link_categoria" href="'.$link.'">'.$categoria.'</a></p>
                        </div>
                    </div>

                    <div class="barra_derecha">
                        <div class="producto_similar">
                            <h3>Productos Similares</h3>
                            <p>No hay productos similares.</p>
                        </div>
                    </div>
                </div>

                <div class="seccion_comentario">
                    <h3 class="mb-4">Comentarios</h3>
                    <form class="form_comentario">
                        <textarea id="texto_comentario" placeholder="Escribe tu comentario aquí..."></textarea>
                        <div class="puntaje">
                            <span>Calificación:</span>
                            <input type="radio" name="estrella" id="star1" value="1" class="estrella"><label for="star1"><i
                                    class="fas fa-star"></i></label>
                            <input type="radio" name="estrella" id="star2" value="2" class="estrella"><label for="star2"><i
                                    class="fas fa-star"></i></label>
                            <input type="radio" name="estrella" id="star3" value="3" class="estrella"><label for="star3"><i
                                    class="fas fa-star"></i></label>
                            <input type="radio" name="estrella" id="star4" value="4" class="estrella"><label for="star4"><i
                                    class="fas fa-star"></i></label>
                            <input type="radio" name="estrella" id="star5" value="5" class="estrella"><label for="star5"><i
                                    class="fas fa-star"></i></label>
                        </div>
                        <button id="btn_comentar" class="btn btn-primary">Enviar Comentario</button>
                    </form>
                    <div id="mensajeError" class="alert alert-danger mt-3 d-none"></div>
                    <ul id="lista_comentario"></ul>
                </div>';
            }else{
                echo "<h3 class='text-center' style='margin: 200px; color: #aaa'>Producto no disponible</h3>";
            }
        ?>
    </section>
    <footer class="bg-dark">
        <div class="container">
            <div class="row justify-content-around">
                <div class="col-md-4 pt-5">
                    <h4 class="text-light border-bottom pb-3 border-light">Mathion Shop</h4>
                    <ul class="list-unstyled text-light footer-link-list">
                        <li>
                            <i class="fas fa-map-marker-alt fa-fw"></i>
                            Universidad de las Fuerzas Armadas ESPE
                        </li>
                        <li>
                            <i class="fa fa-phone fa-fw"></i>
                            <a class="text-decoration-none text-white" href="">0980098210</a>
                        </li>
                        <li>
                            <i class="fa fa-envelope fa-fw"></i>
                            <a class="text-decoration-none text-white" href="">jfgalarza5@espe.edu.ec</a>
                        </li>
                    </ul>
                </div>

                <div class="col-md-4 pt-5">
                    <h4 class="text-light border-bottom pb-3 border-light">Explora</h4>
                    <ul class="list-unstyled text-light footer-link-list">
                        <li><a class="text-decoration-none text-white" href="index.html">Inicio</a></li>
                        <li><a class="text-decoration-none text-white" href="Conocenos.html">Conocenos</a></li>
                        <li><a class="text-decoration-none text-white" href="hombres.html">Hombres</a></li>
                        <li><a class="text-decoration-none text-white" href="mujeres.html">Mujeres</a></li>
                    </ul>
                </div>

                <div class="col-md-4 pt-5">
                    <h4 class="text-light border-bottom pb-3 border-light">Redes Sociales</h4>
                    <ul class="list-inline text-left footer-icons">
                        <li class="list-inline-item rounded-circle text-center">
                            <a class="text-light text-decoration-none" target="_blank" href="http://facebook.com/"><i class="fab fa-facebook-f fa-lg fa-fw"></i></a>
                        </li>
                        <li class="list-inline-item rounded-circle text-center">
                            <a class="text-light text-decoration-none" target="_blank" href="https://www.instagram.com/"><i class="fab fa-instagram fa-lg fa-fw"></i></a>
                        </li>
                        <li class="list-inline-item rounded-circle text-center">
                            <a class="text-light text-decoration-none" target="_blank" href="https://twitter.com/"><i class="fab fa-twitter fa-lg fa-fw"></i></a>
                        </li>
                        <li class="list-inline-item rounded-circle text-center">
                            <a class="text-light text-decoration-none" target="_blank" href="https://www.linkedin.com/"><i class="fab fa-linkedin fa-lg fa-fw"></i></a>
                        </li>
                    </ul>
                </div>
            </div>

            <hr class="bg-light">
        </div>

        <div class="w-100 bg-black py-3">
            <div class="container">
                <div class="row pt-2">
                    <div class="col-12">
                        <p class="text-left text-light">
                            Copyright &copy; 2024 Mathion Shop
                        </p>
                    </div>
                </div>
            </div>
        </div>

    </footer>

    <script src="js/bootstrap.min.js"></script>
    <script src="js/bootstrap.bundle.min.js"></script>
    <script src="https://kit.fontawesome.com/5f232b4c5b.js" crossorigin="anonymous"></script>
</body>

</html>