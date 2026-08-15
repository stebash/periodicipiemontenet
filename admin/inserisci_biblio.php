<?php
session_start();

if (($_SESSION['username'] ?? '') !== 'admin') {
    header("Location: messages.php?id=2");
    exit;
}

require_once("../scripts/dbpdo.php");
require_once('header.php');

function getTipologie($pdo) {
    $stmt = $pdo->query("SELECT CODICE, DESCRIZION FROM TIPOLOGIA ORDER BY DESCRIZION ASC");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<div class="container" style="max-width: 850px; margin: auto; padding: 20px; font-family: sans-serif;">
    <h1 style="color: #0056b3;">Inserimento Nuova Scheda Bibliografica</h1>
    
    <form method="POST" action="inserisci.php">
        <input type="hidden" name="t" value="bib" />
        
        <table class="table-form" style="width: 100%; border-spacing: 10px;">
            <tr>
                <td style="width: 180px;"><strong>Titolo:</strong></td>
                <td><input type="text" name="TITOLO" style="width: 100%;" required /></td>
            </tr>
            <tr>
                <td><strong>Autore:</strong></td>
                <td><input type="text" name="autore" style="width: 100%;" placeholder="Cognome e Nome" /></td>
            </tr>
            <tr>
                <td><strong>Rivista / Periodico:</strong></td>
                <td><input type="text" name="rivista" style="width: 100%;" /></td>
            </tr>
            <tr>
                <td><strong>Dettagli (Vol/Fasc):</strong></td>
                <td>
                    <input type="text" name="volume" style="width: 45%;" placeholder="Volume" />
                    <input type="text" name="fascicolo" style="width: 45%;" placeholder="Fascicolo" />
                </td>
            </tr>
            <tr>
                <td><strong>Tipologia:</strong></td>
                <td>
                    <select name="tipologia" style="width: 100%;" required>
                        <option value="">-- Seleziona --</option>
                        <?php foreach (getTipologie($pdo) as $tipo): ?>
                            <option value="<?= htmlspecialchars($tipo['CODICE']) ?>"><?= htmlspecialchars($tipo['DESCRIZION']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td><strong>Anno / Pagine:</strong></td>
                <td>
                    <input type="text" name="annoEdiz" style="width: 20%;" placeholder="Anno" maxlength="4" />
                    <input type="text" name="pagine" style="width: 70%;" placeholder="Pagine (es. 10-25)" />
                </td>
            </tr>
            <tr>
                <td><strong>Editore / Luogo:</strong></td>
                <td>
                    <input type="text" name="editore" style="width: 45%;" placeholder="Editore" />
                    <input type="text" name="luogoEdiz" style="width: 45%;" placeholder="Luogo" />
                </td>
            </tr>
            <tr>
                <td style="vertical-align: top;"><strong>Note:</strong></td>
                <td><textarea name="NOTE" rows="4" style="width: 100%;"></textarea></td>
            </tr>
        </table>

        <div style="margin-top: 20px; text-align: center;">
            <input type="submit" value="Salva Scheda" style="padding: 10px 40px; background: #28a745; color: white; border: none; border-radius: 4px; cursor: pointer;" />
            <br><br>
            <a href="index.php">Torna al menù</a>
        </div>
    </form>
</div>

<?php require_once('../html/footer.html'); ?>