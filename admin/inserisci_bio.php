<?php
session_start();
if (($_SESSION['username'] ?? '') !== 'admin') {
    header("Location: messages.php?id=2");
    exit;
}
require_once('header.php');
?>

<div class="container">
    <h1>Inserisci Scheda Biografica</h1>
    <form method="POST" action="inserisci.php">
        <input type="hidden" name="t" value="bio" />
        <table class="table-form">
            <tr>
                <td>Nome della Persona:</td>
                <td><input type="text" name="PERS" size="50" required /></td>
            </tr>
            <tr>
                <td>Dati Biografici / Note:</td>
                <td><textarea name="RUO" rows="6" cols="50" required></textarea></td>
            </tr>
        </table>
        <div style="margin-top:20px;">
            <input type="submit" value="Crea Biografia" class="btn-submit" />
            <a href="../admin" class="btn-back">Torna all'Amministrazione</a>
        </div>
    </form>
</div>

<?php require_once('../html/footer.html'); ?>