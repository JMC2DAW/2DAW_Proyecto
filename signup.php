<?php include("templates/session.php"); ?>

<?php
// Si ya está logueado, redirigir al inicio
if (isset($_SESSION['usuario'])) {
    header("Location: index.php");
    exit();
}

$errores = [];
$exito = false;

// Procesar formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = trim($_POST['usuario'] ?? '');
    $correo = trim($_POST['correo'] ?? '');
    $contrasena = $_POST['contrasena'] ?? '';
    $fecha_nacimiento = $_POST['fecha_nacimiento'] ?? '';

    // Validaciones
    if (empty($usuario)) {
        $errores[] = "El usuario es obligatorio.";
    } elseif (strlen($usuario) < 3) {
        $errores[] = "El usuario debe tener al menos 3 caracteres.";
    }

    if (empty($correo)) {
        $errores[] = "El correo es obligatorio.";
    } elseif (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        $errores[] = "El correo no es válido.";
    }

    if (empty($contrasena)) {
        $errores[] = "La contraseña es obligatoria.";
    } elseif (strlen($contrasena) < 6) {
        $errores[] = "La contraseña debe tener al menos 6 caracteres.";
    }

    if (empty($fecha_nacimiento)) {
        $errores[] = "La fecha de nacimiento es obligatoria.";
    }

    // Si no hay errores, guardar sesión
    if (empty($errores)) {
        $_SESSION['usuario'] = $usuario;
        $_SESSION['correo'] = $correo;
        $_SESSION['fecha_nacimiento'] = $fecha_nacimiento;
        $exito = true;
    }
}

// Redirigir si es exitoso
if ($exito) {
    header("Location: index.php");
    exit();
}
?>

<?php include("templates/header.php"); ?>

<body>
    <div class="login-background"></div>
    <div class="login-overlay"></div>

    <main style="position: relative; z-index: 10;">
        <div class="signup-container">
            <h1>Iniciar sesión</h1>

            <?php if (!empty($errores)): ?>
                <ul class="errores">
                    <?php foreach ($errores as $error): ?>
                        <li><?php echo htmlspecialchars($error); ?></li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="form-group">
                    <label for="usuario">Usuario</label>
                    <input 
                        type="text" 
                        id="usuario" 
                        name="usuario" 
                        value="<?php echo htmlspecialchars($_POST['usuario'] ?? ''); ?>"
                        required
                    />
                </div>

                <div class="form-group">
                    <label for="correo">Correo electrónico</label>
                    <input 
                        type="email" 
                        id="correo" 
                        name="correo" 
                        value="<?php echo htmlspecialchars($_POST['correo'] ?? ''); ?>"
                        required
                    />
                </div>

                <div class="form-group">
                    <label for="contrasena">Contraseña</label>
                    <input 
                        type="password" 
                        id="contrasena" 
                        name="contrasena" 
                        required
                    />
                </div>

                <div class="form-group">
                    <label for="fecha_nacimiento">Fecha de nacimiento</label>
                    <input 
                        type="date" 
                        id="fecha_nacimiento" 
                        name="fecha_nacimiento" 
                        value="<?php echo htmlspecialchars($_POST['fecha_nacimiento'] ?? ''); ?>"
                        required
                    />
                </div>

                <button type="submit" class="submit-btn">Iniciar sesión</button>
            </form>

            <div class="back-link">
                <a href="index.php">← Volver al inicio</a>
            </div>
        </div>
    </main>

    <footer style="position: relative; z-index: 10;">
        <p>&copy; 2025-2026 IndieStyled. Todos los derechos reservados.</p>
    </footer>
</body>
</html>
