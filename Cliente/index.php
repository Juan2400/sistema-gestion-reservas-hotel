<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Golden Red</title>
    <link rel="icon" href="imagenes/icono.png" type="image/png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Open+Sans:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Ovo&family=Playwrite+CL:wght@100..400&display=swap" rel="stylesheet">
</head>
<style>
    body {
    color: #333;
    margin: 0;
    padding: 0;
    background-color: #f0f0f0; /* Color gris claro para el fondo general */
}

.ovo-regular {
  font-family: "Ovo", serif;
}


header {
    background: #9f4142; /* Color rojo */
    color: #f8f9fa; /* Color blanco para el texto */
    font-family: 'Playfair Display', serif;
}

.navbar {
    background-color: #9f4142; /* Color rojo */
}

.navbar-brand {
    font-family: 'Playfair Display', serif;
    color: #f8f9fa; /* Color blanco */
}

.navbar-nav .nav-link {
    color: #f8f9fa !important; /* Color blanco */
}

.navbar-nav .nav-link:hover {
    color: #e9ecef !important; /* Color gris claro */
}

.carousel-item img {
    object-fit: cover;
    height: 100%;
}

.card {
    border: none;
    overflow: hidden;
}

footer {
    background-color: #677786; /* Color gris */
    color: #f8f9fa; /* Color blanco */
}

.display-4 {
    color: #9f4142; /* Color rojo */
}

.display5 {
    color: #9f4142; /* Color rojo */
}

.playwrite {
    font-family: 'Open Sans', sans-serif;
    font-weight: 600;
}

.container h2 {
    margin-bottom: 2rem;
    color: #9f4142; /* Color rojo */
}

.custom-header {
    background-color: #677786; /* Color gris */
}

.custom-header h1 {
    color: #f8f9fa; /* Color blanco para el texto */
}

.custom-header .playwrite {
    color: #f8f9fa; /* Color blanco para el texto */
}

.image-container {
    position: relative;
    text-align: center;
}

#btn-reservar:hover {
    background-color: rgba(159, 65, 66, 1); /* Color sólido sin transparencia */
}

.custom-line {
    border: none;
    height: 4px; /* Grosor de la línea */
    width: 10%; /* Ancho de la línea */
    background-color: #9f4142; /* Color dorado de la línea */
    margin: 0 auto; /* Centrar la línea */
}

.custom-line2 {
    border: none;
    height: 4px; /* Grosor de la línea */
    width: 50%; /* Ancho de la línea */
    background-color: black;
}
.carousel-caption {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        color: white;
        text-align: center;
        padding: 20px;
    }

    .carousel-caption1 {
        position: absolute;
        top: 85%;
        left: 50%;
        transform: translate(-50%, -50%);
        color: white;
        text-align: center;
        padding: 20px;
    }
    .carousel-caption h3 {
        font-size: 24px;
        margin-bottom: 10px;
    }

    .carousel-caption span {
        font-size: 18px;
    }
    /* Estilos del contenedor de la pregunta */
    .faq-container {
            display: flex;
            align-items: center;
            cursor: pointer;
            padding: 10px;
            border-bottom: 1px solid #ddd;
            width: 100%;
        }

        /* Estilos del botón con el icono "+" */
        .faq-icon {
            background-color: #9f4142;; /* Color de fondo del círculo */
            border-radius: 50%;
            width: 35px;
            height: 35px;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 18px;
            color: white;
            margin-right: 15px;
            font-weight: bold;
        }

        /* Estilos del texto de la pregunta */
        .faq-question {
            font-size: 16px;
            color: #333;
        }

        /* Estilos del panel de respuesta oculto */
        .faq-answer {
            display: none;
            padding-left: 50px; /* Para alinear el texto de la respuesta */
            margin-top: 10px;
            font-size: 14px;
            color: #555;
        }

        /* Estilo del contenedor desplegado */
        .active .faq-answer {
            display: block;
        }
</style>

<body>

    <!-- Barra de Navegación-->
    <?php include 'navegacionCliente.php'; ?>

        <!-- Header -->
        <header class="custom-header text-white text-center py-5">
            <h1><b>H O T E L G O L D E N R E D</b></h1>
            <p class="playwrite">EL LUJO AL ALCANCE DE TUS MANOS</p>
        </header>

        <section>
            <div id="carouselExampleCaptions" class="carousel slide">
                <div class="carousel-inner">
                    <div class="carousel-item active">
                        <img src="imagenes/fondo.jpg" class="d-block w-100" alt="...">
                        <div class="carousel-caption1 d-none d-md-block">
                            <button id="btn-reservar" style="font-size: 24px; background-color: rgba(159, 65, 66, 1); color:white; border-radius: 0; padding: 30px 70px; border: none;" type="button" onclick="window.location.href='clienteReserva.php';">
                                Reservar ahora
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </section>


        <section class="mt-5">
            <br>
            <h3 class="display-4 text-center ovo-regular">BIENVENIDO AL HOTEL GOLDEN RED</h3>
            <br>
            <hr class="custom-line">
            <br>
            <div style="padding-left: 30px; padding-right: 30px;">
                <p class="lead text-muted text-center">Nuestra misión es hacer que tu estadía de negocios, turística o de paso supere tus expectativas.</p>
                <p class="lead text-muted text-center">Nos aseguramos de brindarte más valor por tu dinero reflejado en la calidad de nuestro servicio, con una atención personalizada y comodidad en todos los espacios.</p>
                <p class="lead text-muted text-center">¡Te esperamos! en Hotel Golden Red Piura</p>
            </div>
        </section>
        <br>
        <section class="mt-5">
            <div style="background-color: white;">
                <div class="row align-items-center">
                    <div class="col-md-6 d-flex align-items-center" style="padding: 6%; font-size: 15px; text-align: justify;">
                        <div>
                            <h2 class="ovo-regular">Sobre Nosotros</h2>
                            <hr class="custom-line2">
                            <br>
                            <p style="font-size: 20px;">Somos un hotel con más de 15 años liderando la oferta hotelera de la ciudad, debido a nuestra óptima ubicación, variedad de servicios y oferta culinaria inigualable.
                                <br> Contamos con 29 habitaciones entre simples, matrimoniales y suites. Todas diseñadas pensando en que tu estadía sea insuperable.</p>
                            <br>
                            <button id="btn-reservar" style="font-size: 20px; background-color: rgba(159, 65, 66, 1); color:white; border-radius: 0; padding: 20px 50px; border: none;" type="button" onclick="window.location.href='clienteReserva.php';">
                                Ver mas
                            </button>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <img class="img-fluid" src="imagenes/md6.jpg" alt="Fachada del Hotel">
                    </div>
                </div>
            </div>
        </section>
        <br>
        <section class="mt-5">
            <br>
            <h1 class="display5 text-center ovo-regular">NUESTRAS HABITACIONES</h1>
            <br>
            <hr class="custom-line">
            <br>
            <div style="padding-left: 30px; padding-right: 30px;">
                <p class="lead text-muted text-center">Tenemos la habitación ideal para ti</p>
            </div>
            <br>
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-md-4 mb-4">
                        <div class="card">
                            <img src="imagenes/habitacionsimple.jpg" class="card-img-top" alt="...">
                            <div class="card-body">
                                <h5 class="card-title">Habitacion Simple</h5>
                                <br>
                                <p>
                                    <i class="bi bi-person-fill" style="color: #9f4142; font-size: 1.5rem; padding-right: 5px;"></i>
                                    <b style="color: #9f4142; font-size: 1.2rem;">Capacidad máxima:</b> <span style="font-size: 1.2rem;">1 personas</span>
                                </p>
                                <p>
                                    <i class="fas fa-bed" style="color: #9f4142; font-size: 1.5rem; padding-right: 5px;"></i>
                                    <b style="color: #9f4142; font-size: 1.2rem;">Capacidad máxima:</b> <span style="font-size: 1.2rem;">2 plz</span>
                                </p>
                                <br>
                                <button id="btn-reservar" style="font-size: 1.2rem; background-color: rgba(159, 65, 66, 1); color:white; border-radius: 0; padding: 20px 40px; border: none;" type="button" onclick="window.location.href='clienteReserva.php';">
                                    Precio S/50 / Noche
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-4">
                        <div class="card">
                            <img src="imagenes/habitacionmatrimonial.jpg" class="card-img-top" alt="...">
                            <div class="card-body">
                                <h5 class="card-title">Habitacion Matrimonial</h5>
                                <br>
                                <p>
                                    <i class="bi bi-person-fill" style="color: #9f4142; font-size: 1.5rem; padding-right: 5px;"></i>
                                    <b style="color: #9f4142; font-size: 1.2rem;">Capacidad máxima:</b> <span style="font-size: 1.2rem;">2 personas</span>
                                </p>
                                <p>
                                    <i class="fas fa-bed" style="color: #9f4142; font-size: 1.5rem; padding-right: 5px;"></i>
                                    <b style="color: #9f4142; font-size: 1.2rem;">Capacidad máxima:</b> <span style="font-size: 1.2rem;">2.5 plz</span>
                                </p>
                                <br>
                                <button id="btn-reservar" style="font-size: 1.2rem; background-color: rgba(159, 65, 66, 1); color:white; border-radius: 0; padding: 20px 40px; border: none;" type="button" onclick="window.location.href='clienteReserva.php';">
                                    Precio S/60 / Noche
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-4">
                        <div class="card">
                            <img src="imagenes/habitacionsuite.jpg" class="card-img-top" alt="...">
                            <div class="card-body">
                                <h5 class="card-title">Habitacion Presidencial</h5>
                                <br>
                                <p>
                                    <i class="bi bi-person-fill" style="color: #9f4142; font-size: 1.5rem; padding-right: 5px;"></i>
                                    <b style="color: #9f4142; font-size: 1.2rem;">Capacidad máxima:</b> <span style="font-size: 1.2rem;">2 personas</span>
                                </p>
                                <p>
                                    <i class="fas fa-bed" style="color: #9f4142; font-size: 1.5rem; padding-right: 5px;"></i>
                                    <b style="color: #9f4142; font-size: 1.2rem;">Capacidad máxima:</b> <span style="font-size: 1.2rem;">3 plz</span>
                                </p>
                                <br>
                                <button id="btn-reservar" style="font-size: 1.2rem; background-color: rgba(159, 65, 66, 1); color:white; border-radius: 0; padding: 20px 40px; border: none;" type="button" onclick="window.location.href='clienteReserva.php';">
                                    Precio S/180 / Noche
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </section>

        <section class="mt-5">
            <br>
            <h1 class="display5 text-center ovo-regular">COMODIDADES</h1>
            <br>
            <hr class="custom-line">
            <br>
            <br>

            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-md-4 text-center">
                        <!-- Mantiene las columnas, centrando el contenido dentro de cada una -->
                        <i class="fas fa-lock" style="color: #9f4142; font-size: 5rem;"></i>
                        <br>
                        <br>
                        <h5 class="ovo-regular"><b>Seguridad y Atención 24/7</b></h5>
                        <br>
                        <p class="lead text-muted text-center">Nuestra recepción atiende 24 horas al día, 7 días a la semana.</p>
                    </div>
                    <div class="col-md-4 text-center">
                        <i class="fas fa-car" style="color: #9f4142; font-size: 5rem;"></i>
                        <br>
                        <br>
                        <h5 class="ovo-regular"><b>Cochera</b></h5>
                        <br>
                        <p class="lead text-muted text-center">Contamos con cochera privada, con iluminación y video vigilancia.</p>
                    </div>
                    <div class="col-md-4 text-center">
                        <i class="fas fa-wifi" style="color: #9f4142; font-size: 5rem;"></i>
                        <br>
                        <br>
                        <h5 class="ovo-regular"><b>Wifi</b></h5>
                        <br>
                        <p class="lead text-muted text-center">Con línea dedicada de alta velocidad en todos los espacios del hotel. Ideales para reuniones de trabajo o trabajo remoto.</p>
                    </div>
                </div>
            </div>
        </section>

        <section class="my-3">
            <br>
            <h1 class="display5 text-center ovo-regular">TESTIMONIOS</h1>
            <br>
            <hr class="custom-line">
            <br>
            <br>
            <div class="">
                <div id="carouselExampleFade" class="carousel slide carousel-fade" data-bs-ride="carousel" data-bs-interval="3000" style="padding-top: 7px;">
                    <div class="carousel-inner">
                        <div class="carousel-item active">
                            <img src="imagenes/imagen4.jpg" class="d-block w-100" alt="...">
                            <div class="carousel-caption d-flex flex-column justify-content-center align-items-center">
                                <h3 class="display5 text-center ovo-regular"><b>Pedro A. Manrique</b></h3>
                                <span class="ovo-regular">
                    "LO RECOMIENDO. ATENCIÓN PERSONALIZADA, HABITACIONES CÓMODAS Y MUY LIMPIAS, EL PERSONAL DE PRIMERA. EL SERVICIO EN EL RASTAURANTE HIGIÉNICO Y MENÚ VARIADO."</span>
                            </div>
                        </div>
                        <div class="carousel-item">
                            <img src="imagenes/imagen5.jpg" class="d-block w-100" alt="...">
                            <div class="carousel-caption d-flex flex-column justify-content-center align-items-center">
                                <h3 class="display5 text-center ovo-regular"><b>Shah Mat</b></h3>
                                <span class="ovo-regular">
                    "COMIDA SÍUPER RICA Y PERSONAL MUY AMABLE COMO UN ÁNGEL. LIMPIO Y CÓMODO REALMENTE UN SÚPER LUGAR."</span>
                            </div>
                        </div>
                        <div class="carousel-item">
                            <img src="imagenes/imagen6.jpg" class="d-block w-100" alt="...">
                            <div class="carousel-caption d-flex flex-column justify-content-center align-items-center">
                                <h3 class="display5 text-center ovo-regular"><b>Carlos A.</b></h3>
                                <span>"HOPEDADO HACE POCO. LO RESUMO EN POCAS PALABRAS: EXCELENTE ATENCIÓN, EXCELENTES INSTALACIONES, EXCELENTE EL RESTAURANTE. LO RECOMIENDO!!"</span>
                            </div>
                        </div>
                    </div>
                    <!-- Controles del Carrusel -->
                    <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleFade" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Anterior</span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleFade" data-bs-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Siguiente</span>
                    </button>
                </div>
            </div>
        </section>

        <section class="my-3">
            <br>
            <h1 class="display5 text-center ovo-regular">TESTIMONIOS</h1>
            <br>
            <hr class="custom-line">
            <br>
            <br>
            <div class="container">
                <div class="container">
                    <div class="faq-container" onclick="toggleAnswer(this)">
                        <div class="faq-icon">+</div>
                        <div class="faq-question">En caso llegue antes de la hora del check in, ¿puedo ingresar a mi habitación?</div>
                    </div>
                    <div class="faq-answer">
                        <p>Dependiendo de la disponibilidad, puede solicitar un early check-in. En caso de que no esté disponible, podrá dejar su equipaje en la recepción.</p>
                    </div>
                    <br>
                    <div class="faq-container" onclick="toggleAnswer(this)">
                        <div class="faq-icon">+</div>
                        <div class="faq-question">¿El desayuno está incluido en la tarifa?</div>
                    </div>
                    <div class="faq-answer">
                        <p>Todas nuestras tarifas incluyen el desayuno. Y no se cobra menos por no consumirlo.</p>
                    </div>
                    <br>
                    <div class="faq-container" onclick="toggleAnswer(this)">
                        <div class="faq-icon">+</div>
                        <div class="faq-question">¿Cuentan con cochera Segura?</div>
                    </div>
                    <div class="faq-answer">
                        <p>Sí, nuestra cochera está ubicada al frente del hotel. Cuenta con vigilancia y seguridad las 24 horas del día.</p>
                    </div>
                    <br>
                    <div class="faq-container" onclick="toggleAnswer(this)">
                        <div class="faq-icon">+</div>
                        <div class="faq-question">¿A qué distancia están del aeropuerto y terminales de transporte Terrestre?</div>
                    </div>
                    <div class="faq-answer">
                        <p>El aeropuerto está ubicado a 2 kilómetros del hotel o 7 minutos en taxi.
                            <br> Transportes como Eppo, Civa, Palomino, etc. se encuentran a menos de 1km yse puede llegar caminando.</p>
                    </div>
                    <br>

                </div>
            </div>
            <script>
                // Función para desplegar la respuesta al hacer clic
                function toggleAnswer(container) {
                    container.classList.toggle("active");
                    var answer = container.nextElementSibling;
                    if (answer.style.display === "block") {
                        answer.style.display = "none";
                    } else {
                        answer.style.display = "block";
                    }
                }
            </script>
        </section>

        <!-- Footer -->
        <footer class="bg-dark text-white py-5">
            <div class="container">
                <div class="row">
                    <!-- Columna 1: Información de contacto -->
                    <div class="col-md-4 text-center">
                        <h5><b>Contacto</b></h5>
                        <ul class="list-unstyled">
                            <li>Teléfono: +123 456 789</li>
                            <li>Email: contacto@goldenredhotel.com</li>
                            <li>Dirección: Calle Principal 123, Lima, Perú</li>
                        </ul>
                    </div>

                    <!-- Columna 2: Enlaces útiles -->
                    <div class="col-md-4 text-center">
                        <h5><b>Enlaces útiles</b></h5>
                        <ul class="list-unstyled">
                            <li><a href="#" class="text-white">Sobre nosotros</a></li>
                            <li><a href="#" class="text-white">Habitaciones</a></li>
                            <li><a href="#" class="text-white">Servicios</a></li>
                        </ul>
                    </div>

                    <!-- Columna 3: Redes sociales -->
                    <div class="col-md-4 text-center">
                        <h5><b>Siguenos</b></h5>
                        <a href="#" class="text-white me-3"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="text-white me-3"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="text-white"><i class="fab fa-instagram"></i></a>
                    </div>
                </div>

                <!-- Texto de derechos reservados -->
                <div class="text-center mt-4">
                    <p>&copy; 2024 Hotel Golden Red. Todos los derechos reservados.</p>
                </div>
            </div>
        </footer>


        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>