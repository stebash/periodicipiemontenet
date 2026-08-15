<?php
session_start();
if (($_SESSION['username'] ?? '') !== 'admin') {
    header("Location: messages.php?id=2");
    exit;
}
require_once('header.php');
?>

<div class="container">
    <h1>Inserisci un Collaboratore (Persona)</h1>
    <form method="POST" action="inserisci.php">
        <input type="hidden" name="t" value="pers" />
        <table class="table-form">
            <tr>
                <td>Cognome e Nome:</td>
                <td><input type="text" name="cognom" size="40" required /></td>
            </tr>
            <tr>
                <td>Ruolo:</td>
                <td><input type="text" name="ruolo" size="40" placeholder="es. Direttore, Redattore" /></td>
            </tr>
            <tr>
                <td>ID Periodico (Scheda):</td>
                <td><input type="number" name="scheda" size="10" required /></td>
            </tr>
        </table>
        <div style="margin-top:20px;">
            <input type="submit" value="Salva Collaboratore" class="btn-submit" />
            <a href="index.php" class="btn-back">Torna al menù</a>
        </div>
    </form>
</div>

<?php require_once('../html/footer.html'); ?>