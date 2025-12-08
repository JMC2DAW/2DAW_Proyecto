<?php?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IndieStyled</title>
    <link rel="stylesheet" href="css/style.css">
    <script src="scripts/script.js" defer></script>
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="header-container">
            <h1 class="logo">IndieStyled</h1>
            <nav class="navbar">
                <ul>
                    <li><a href="#inicio">Inicio</a></li>
                    <li><a href="pages/catalogue.php">Catálogo</a></li>
                    <li><a href="pages/compras.php">Planeamiento de compras</a></li>
                    <li><a href="pages/ranking.php">Ranking</a></li>
                    <li><a href="pages/discovery.php">Recomendaciones</a></li>
                    <li><a href="pages/quiz.php">Quiz</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <!-- Sección Principal (main) -->
    <section class="main" id="inicio">
        <div class="main-inner">
            <div class="main-content">
                <h2 class="main-title">¡Bienvenid@ a IndieStyled!</h2>
                <p class="main-subtitle">Explora el fascinante mundo de los videojuegos indie, donde la creatividad y la innovación son el pan de cada día.</p>
                <div class="main-buttons">
                    <a href="pages/catalogue.php" class="btn btn-primary">Explora juegos</a>
                    <a href="pages/discovery.php" class="btn btn-secondary">Descubre más</a>
                </div>
            </div>

            <div class="main-image" aria-hidden="true">
                <img src="img/top_image.jpg" alt="Collage de juegos indie" />
            </div>
        </div>
    </section>

    <main>
        <!-- Sección Historia de los videojuegos indie -->
        <section class="story-section" id="historia">
            <div class="story-background" style="background-image: url('img/story_bg.jpg')"></div>
            <div class="story-overlay"></div>
            <div class="story-content">
                <h2 class="story-title">La Historia de los Videojuegos Indie</h2>
                <p class="story-text">
                    Desde finales de los años <b>2000</b>, plataformas como Steam, itch.io y las tiendas de aplicaciones móviles han abierto las puertas a pequeños estudios y desarrolladores individuales para compartir sus creaciones con el mundo. Juegos como <i>Minecraft</i>, <i>Celeste</i>, <i>Hades</i> y <i>Stardew Valley</i> han dejado una huella imborrable en la industria, demostrando que la creatividad y la pasión pueden superar cualquier limitación económica.
                </p>
                <p class="story-text">
                    Pero, ¿cómo llegamos a este punto? El término "indie" en los videojuegos, abreviatura de "independiente", comenzó a usarse de manera más prominente a principios de los <b>2000</b>, aunque sus raíces se remontan a los años <b>80</b> y <b>90</b>. En esa época, el desarrollo de juegos estaba dominado por grandes empresas. Sin embargo, con el auge de los ordenadores personales y el software de desarrollo más accesible, los creadores independientes comenzaron a surgir.
                </p>
                <p class="story-text">
                    La popularización del término "indie" en los videojuegos se consolidó gracias a la aparición de comunidades online y eventos como el Independent Games Festival (IGF), fundado en <b>1998</b>. Estos espacios proporcionaron una plataforma para que los desarrolladores independientes mostraran sus juegos y se conectaran con una audiencia más amplia.
                </p>
                <p class="story-text">
                    Hoy en día, los juegos indie no solo son reconocidos por su innovación mecánica y narrativa, sino también por su capacidad de impactar emocionalmente a millones de jugadores alrededor del mundo. Son espacios de experimentación donde los desarrolladores pueden tomar riesgos, explorar nuevas ideas y conectar directamente con su audiencia.
                </p>
                <p class="story-text">
                    La evolución de los juegos indie ha sido impulsada por la tecnología y la comunidad. El acceso a herramientas de desarrollo más fáciles de usar, como Unity y Unreal Engine, ha democratizado aún más la creación de juegos. Además, la creciente comunidad de jugadores que buscan experiencias únicas y originales ha sido un factor clave en el éxito de los juegos indie.
                </p>
                <p class="story-text">
                    En IndieStyled, celebramos esta creatividad sin límites y te invitamos a descubrir los títulos que están transformando la industria del videojuego.
                </p>


            </div>
        </section>

        <!-- Sección de Funciones -->
        <section class="features-section">
            <h2 class="features-title">Explora todas nuestras funciones</h2>
            <div class="features-grid">
                <a href="pages/catalogue.php" class="feature-card">
                    <div class="feature-icon">📚</div>
                    <h3>Catálogo</h3>
                    <p>Descubre nuestra extensa colección de videojuegos indie con descripciones detalladas y reseñas.</p>
                </a>

                <a href="pages/compras.php" class="feature-card">
                    <div class="feature-icon">🛒</div>
                    <h3>Planeamiento de compras</h3>
                    <p>Planifica tus compras de juegos indie y gestiona tu lista de deseos de forma eficiente.</p>
                </a>

                <a href="pages/ranking.php" class="feature-card">
                    <div class="feature-icon">⭐</div>
                    <h3>Ranking</h3>
                    <p>Explora los juegos indie mejor valorados por la comunidad y descubre los favoritos.</p>
                </a>

                <a href="pages/discovery.php" class="feature-card">
                    <div class="feature-icon">💡</div>
                    <h3>Recomendaciones</h3>
                    <p>Obtén recomendaciones personalizadas basadas en tus preferencias de juego.</p>
                </a>

                <a href="pages/quiz.php" class="feature-card">
                    <div class="feature-icon">❓</div>
                    <h3>Quiz</h3>
                    <p>Realiza nuestro quiz interactivo para demostrar tus conocimientos sobre los videojuegos indie con tus amigos.</p>
                </a>
            </div>
        </section>
    </main>

    <footer>
        <p>"IndieStyled, la página independiente para los videojuegos independientes."</p>
        <p>- <i>Javier Martín Cruz, creador de IndieStyled</i></p>
        <br>
        <p>&copy; 2025-2026 IndieStyled. Todos los derechos reservados.</p>
        <p>Esta página no está afiliada con Steam, Itch.io u otra plataforma relacionada, esta página es un proyecto escolar y personal.</p>
    </footer>
</body>
</html>