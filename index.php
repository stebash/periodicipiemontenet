<?php
// Avvio della sessione PHP prima di qualsiasi output.
// Necessario al contatore visite (una sola visita per sessione)
// definito in scripts/contatore.php.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="it">
<head>	
    <title>Periodici Piemonte</title>
    <meta charset="utf-8">

    <!-- importante per Bootstrap -->
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">

    <!-- I TUOI CSS -->
    <link rel="stylesheet" href="/css/style.css?v=20251201a">
    <link rel="stylesheet" href="/css/menu.css?v=20251201a">

</head>

<body>
<?php include ("scripts/dbpdo.php"); ?>

<!-- LAYOUT A 3 COLONNE -->
<div id="pagewrap">
    <header id="site-header">
        <img src="/img/periodici.png" alt="Periodici Piemonte">
    </header>

    <?php require_once('scripts/menu.php'); ?>

    <!-- WRAPPER DELLE 3 COLONNE -->
    <div id="columns">
        <section id="content">
            <?php 
                require_once('scripts/contatore.php');
                require_once('scripts/genere.php'); 
            ?>
        </section>

        <section id="middle">
            <?php 
                require_once('scripts/get_page.php');
                include $content; 
            ?>
        </section>

        <section id="sidebar">
            <?php require_once('scripts/provincia.php'); ?>
        </section>
    </div>

    <?php require_once('html/footer.html'); ?>

</div>

<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>

</body>
</html>
