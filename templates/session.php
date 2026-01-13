<?php
// Revisa si la sesión está iniciada, si no, la inicia
if (session_status() == PHP_SESSION_NONE) { session_start(); }
?>