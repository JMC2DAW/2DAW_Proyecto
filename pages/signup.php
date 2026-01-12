<?php
session_start();

// Si ya está logueado, redirigir al inicio
if (isset($_SESSION['usuario'])) {
    header("Location: ../index.php");
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
    header("Location: ../index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar sesión - IndieStyled</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        .signup-container {
            max-width: 500px;
            margin: 3rem auto;
            padding: 2rem;
            background-color: #1a1a1a;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.3);
            border: 1px solid #00d4ff;
        }

        .signup-container h1 {
            text-align: center;
            color: #00d4ff;
            margin-bottom: 2rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: #e0e0e0;
            font-weight: 500;
        }

        .form-group input {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #00d4ff;
            border-radius: 4px;
            font-size: 1rem;
            transition: border-color 0.3s;
            background-color: #2a2a2a;
            color: #e0e0e0;
        }

        .form-group input:focus {
            outline: none;
            border-color: #00d4ff;
            box-shadow: 0 0 4px rgba(0, 212, 255, 0.3);
        }

        .errores {
            background-color: #2a1a1a;
            border: 1px solid #ff4444;
            color: #ffaaaa;
            padding: 1rem;
            border-radius: 4px;
            margin-bottom: 1.5rem;
            list-style: none;
        }

        .errores li {
            margin-bottom: 0.5rem;
        }

        .errores li:last-child {
            margin-bottom: 0;
        }

        .submit-btn {
            width: 100%;
            padding: 0.75rem;
            background-color: #00d4ff;
            color: #1a1a1a;
            border: none;
            border-radius: 4px;
            font-size: 1rem;
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .submit-btn:hover {
            background-color: #00b8d4;
        }

        .back-link {
            text-align: center;
            margin-top: 1.5rem;
        }

        .back-link a {
            color: #00d4ff;
            text-decoration: none;
        }

        .back-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="login-background"></div>
    <div class="login-overlay"></div>
    <header class="header">
        <div class="header-container">
            <h1 class="logo">IndieStyled</h1>
            <nav class="navbar">
                <ul>
                    <li><a href="../index.php">Volver al inicio</a></li>
                </ul>
            </nav>
        </div>
    </header>

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
                <a href="../index.php">← Volver al inicio</a>
            </div>
        </div>
    </main>

    <footer style="position: relative; z-index: 10;">
        <p>&copy; 2025-2026 IndieStyled. Todos los derechos reservados.</p>
    </footer>
</body>
</html>
