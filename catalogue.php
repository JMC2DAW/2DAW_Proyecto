<?php include("templates/session.php"); ?>
<?php include("templates/header.php"); ?>

<div class="page-background"></div>
<div class="page-overlay"></div>

<main class="catalogue-main" style="position: relative; z-index: 10;">
    <div class="catalogue-header">
        <h1>Catálogo de Juegos</h1>
        <p>Explora nuestra colección de juegos indie seleccionados</p>
    </div>
    
    <div class="games-grid" id="gamesContainer">
        <!-- Los juegos se cargarán aquí con JavaScript -->
    </div>
</main>

<?php include("templates/footer.php"); ?>
