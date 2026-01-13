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
                    <li><a href="catalogue.php">Catálogo</a></li>
                    <li><a href="compras.php">Planeamiento de compras</a></li>
                    <li><a href="ranking.php">Ranking</a></li>
                    <li><a href="discovery.php">Recomendaciones</a></li>
                    <li><a href="quiz.php">Quiz</a></li>
                </ul>
            </nav>
            <div class="auth-section">
                <?php if (isset($_SESSION['usuario'])): ?>
                    <span class="usuario-nombre"><?php echo htmlspecialchars($_SESSION['usuario']); ?></span>
                    <a href="logout.php" class="btn btn-secondary">Cerrar sesión</a>
                </div>
                <?php else: ?>
                    <a href="signup.php" class="btn btn-primary">Iniciar sesión</a>
                <?php endif; ?>
            </div>
        </div>
    </header>