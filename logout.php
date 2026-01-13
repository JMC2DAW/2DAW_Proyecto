<?php include("templates/session.php"); ?>

<?php
// Destruir la sesión
session_destroy();

// Redirigir al inicio
header("Location: index.php");
exit();
