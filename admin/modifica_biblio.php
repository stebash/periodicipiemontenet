<?php
session_start();

// 1. Controllo Autorizzazione
if (($_SESSION['username'] ?? '') !== 'admin') {
    header("Location: messages.php?id=2");
    exit;
}

require_once("../scripts/dbpdo.php");
require_once('header.php');

// 2. Recupero ID (accetta 's' dal form di ricerca o 'id' da un link)
$id = $_POST['s'] ?? $_GET['id'] ?? null;

if ($id === null || !ctype_digit((string)$id)) {
    die("<p style='text-align:center; color:red;'>Errore: ID scheda bibliografica non valido.</p>");
}

// 3. Funzioni utility
function e(?string $v): string { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }

function getTipologie($pdo) {
    return $pdo->query("SELECT CODICE, DESCRIZION FROM TIPOLOGIA ORDER BY DESCRIZION ASC")->fetchAll(PDO::FETCH_ASSOC);
}

// 4. Recupero dati della scheda e dell'eventuale autore collegato
try {
    // Dati Bibliografici
    $stmt = $pdo->prepare("SELECT * FROM BIBLIO_EST WHERE codice = :id");
    $stmt->execute([':id' => $id]);
    $bib = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$bib) {
        die("<p style='text-align:center;'>Scheda bibliografica n. $id non trovata.</p>");
    }

    // Recupero Autore (dalla tabella PERSONE)
    $stmtAutore = $pdo->prepare("SELECT PERS FROM PERSONE WHERE codice = :id AND RUO = 'Autore' LIMIT 1");
    $stmtAutore->execute([':id' => $id]);
    $autore = $stmtAutore->fetchColumn() ?: "";

} catch (PDOException $e) {
    die("Errore database: " . $e->getMessage());
}
?>

<div class="container" style="max-width: 850px; margin: auto; padding: 20px; font-family: sans-serif;">
    <h1 style="color: #0056b3; border-bottom: 2px solid #0056b3; padding-bottom: 10px;">
        Modifica Scheda Bibliografica #<?= e($id) ?>
    </h1>
    
    <form method="POST" action="update.php">
        <input type="hidden" name="t" value="bib" />
        <input type="hidden" name="ID" value="<?= e($id) ?>" />
        
        <table class="table-form" style="width: 100%; border-spacing: 10px;">
            <tr>
                <td style="width: 180px;"><strong>Titolo:</strong></td>
                <td><input type="text" name="TITOLO" style="width: 100%; padding: 8px;" value="<?= e($bib['TITOLO']) ?>" required /></td>
            </tr>
            <tr>
                <td><strong>Autore:</strong></td>
                <td><input type="text" name="autore" style="width: 100%; padding: 8px;" value="<?= e($autore) ?>" placeholder="Cognome e Nome" /></td>
            </tr>
            <tr>
                <td><strong>Editore:</strong></td>
                <td><input type="text" name="editore" style="width: 100%; padding: 8px;" value="<?= e($bib['editore']) ?>" /></td>
            </tr>
            <tr>
                <td><strong>Luogo di Edizione:</strong></td>
                <td><input type="text" name="luogoEdiz" style="width: 100%; padding: 8px;" value="<?= e($bib['luogoEdiz']) ?>" /></td>
            </tr>
            <tr>
                <td><strong>Tipologia:</strong></td>
                <td>
                    <select name="tipologia" style="width: 100%; padding: 8px;" required>
                        <?php foreach (getTipologie($pdo) as $tipo): ?>
                            <option value="<?= $tipo['CODICE'] ?>" <?= $bib['codTipologia'] == $tipo['CODICE'] ? 'selected' : '' ?>>
                                <?= e($tipo['DESCRIZION']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td><strong>Anno di Edizione:</strong></td>
                <td><input type="text" name="annoEdiz" size="10" maxlength="4" value="<?= e($bib['annoEdiz']) ?>" /></td>
            </tr>
            <tr>
                <td style="vertical-align: top;"><strong>Note:</strong></td>
                <td><textarea name="NOTE" rows="6" style="width: 100%; padding: 8px;"><?= e($bib['NOTE']) ?></textarea></td>
            </tr>
        </table>

        <div style="margin-top: 30px; text-align: center;">
            <input type="submit" value="Salva Modifiche" style="padding: 12px 40px; background-color: #28a745; color: white; border: none; border-radius: 4px; cursor: pointer; font-weight: bold;" />
            <br><br>
            <a href="index.php" style="color: #666;">Annulla e torna al menù</a>
        </div>
    </form>
</div>

<?php require_once('../html/footer.html'); ?>