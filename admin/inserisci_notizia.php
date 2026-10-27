<?php
declare(strict_types=1);

session_start();
if (!isset($_SESSION['username']) || $_SESSION['username'] !== 'admin') {
    header("Location:messages.php?id=2");
    exit;
}

require_once('header.php');

// Connessione PDO (stessa di tutto il progetto, parametri in scripts/dbpdo.php)
require_once("../scripts/dbpdo.php");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$errore   = '';
$successo = '';
$id       = 0;
$titolo   = '';
$testo    = '';

// -----------------------------------------------------------------------------
// LOGICA DI SALVATAGGIO (solo se premuto il pulsante)
// -----------------------------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['crea'])) {

    $titolo = trim((string)($_POST['titolo'] ?? ''));
    $testo  = trim((string)($_POST['testo'] ?? ''));

    // --- Validazione minima dei campi obbligatori ----------------------------
    if ($titolo === '') {
        $errore = 'Il titolo è obbligatorio.';
    } elseif (mb_strlen($titolo) > 100) {
        $errore = 'Il titolo non può superare 100 caratteri.';
    } elseif ($testo === '') {
        $errore = 'Il testo è obbligatorio.';
    }

    // --- Allegato opzionale (locandina) --------------------------------------
    // Stessa convenzione di admin/allegati.php: solo PDF, max 20 MB,
    // salvato in /attach con nome att<ID>.pdf.
    $allegatoInvio = false;
    if ($errore === '' && isset($_FILES['file']) && $_FILES['file']['name'] !== '') {
        $f = $_FILES['file'];

        if ($f['error'] !== UPLOAD_ERR_OK) {
            $errore = 'Errore durante il caricamento del file.';
        } elseif ($f['size'] > 20 * 1024 * 1024) {
            $errore = 'Il file supera la dimensione massima consentita di 20 MB.';
        } elseif (strtolower(pathinfo($f['name'], PATHINFO_EXTENSION)) !== 'pdf') {
            $errore = 'Sono ammessi soltanto file PDF.';
        } else {
            // Controllo MIME lato server, non soltanto sull'estensione.
            $finfo = new finfo(FILEINFO_MIME_TYPE);
            if ($finfo->file($f['tmp_name']) !== 'application/pdf') {
                $errore = 'Il file selezionato non risulta essere un PDF valido.';
            } else {
                $allegatoInvio = true;
            }
        }
    }

    // --- Inserimento nel database ---------------------------------------------
    if ($errore === '') {
        $attachDir = dirname(__DIR__) . '/attach';
        if (!is_dir($attachDir) && !mkdir($attachDir, 0755, true)) {
            $errore = 'Impossibile creare la cartella attach.';
        }
    }

    if ($errore === '') {
        try {
            // id è PRIMARY KEY AUTO_INCREMENT (vedi gli ALTER TABLE a fine dump):
            // non lo calcoliamo noi, il database lo genera.
            $stmt = $pdo->prepare(
                'INSERT INTO notizie (titolo, testo, allegato) VALUES (?, ?, NULL)'
            );
            $stmt->execute([$titolo, $testo]);
            $id = (int)$pdo->lastInsertId();
        } catch (PDOException $e) {
            $errore = 'Si è verificato un errore durante l\'inserimento nella tabella notizie.';
        }
    }

    // --- Salvataggio dell'allegato (se presente) ------------------------------
    if ($errore === '' && $allegatoInvio) {
        $fileName  = 'att' . $id . '.pdf';
        $destination = $attachDir . '/' . $fileName;

        if (!move_uploaded_file($_FILES['file']['tmp_name'], $destination)) {
            // Compensazione (MyISAM non supporta le transazioni):
            // rimuovo esplicitamente la notizia appena creata
            $pdo->prepare('DELETE FROM notizie WHERE id = ?')->execute([$id]);
            $errore = 'Impossibile salvare il file nella cartella attach.';
        } else {
            try {
                $stmt = $pdo->prepare('UPDATE notizie SET allegato = ? WHERE id = ?');
                $stmt->execute([$fileName, $id]);
            } catch (PDOException $e) {
                // Compensazione (MyISAM non supporta le transazioni):
                // rimuovo esplicitamente il file e la notizia appena creati
                @unlink($destination);
                $pdo->prepare('DELETE FROM notizie WHERE id = ?')->execute([$id]);
                $errore = 'Errore durante l\'aggiornamento del database.';
            }
        }
    }

    if ($errore === '') {
        $successo = "Notizia inserita correttamente (numero $id).";
        // Azzero i campi per l'eventuale inserimento successivo
        $titolo = '';
        $testo  = '';
    }
}
?>

<div>
    <h1>Nuova notizia</h1>

    <?php if ($successo !== ''): ?>
        <p style="color:green;"><strong><?= htmlspecialchars($successo, ENT_QUOTES, 'UTF-8') ?></strong></p>
        <p><a href="index.php">Torna al menù di amministrazione</a>
        &nbsp;|&nbsp;
        <a href="../index.php?c=notizie-eventi">Vedi le notizie nella pagina pubblica</a></p>
    <?php endif; ?>

    <?php if ($errore !== ''): ?>
        <p style="color:red;"><strong><?= htmlspecialchars($errore, ENT_QUOTES, 'UTF-8') ?></strong></p>
    <?php endif; ?>

    <form method="POST" action="inserisci_notizia.php" enctype="multipart/form-data">
        <table>
            <tr>
                <td>Titolo <small>(obbligatorio, max 100 caratteri)</small></td>
                <td><input type="text" name="titolo" size="60" maxlength="100" required value="<?= htmlspecialchars($titolo, ENT_QUOTES, 'UTF-8') ?>"></td>
            </tr>
            <tr>
                <td>Testo <small>(obbligatorio, consentito HTML)</small></td>
                <td><textarea name="testo" rows="14" cols="60" required><?= htmlspecialchars($testo, ENT_QUOTES, 'UTF-8') ?></textarea></td>
            </tr>
            <tr>
                <td>Locandina <small>(facoltativa, solo PDF, max 20 MB)</small></td>
                <td><input type="file" name="file" accept="application/pdf"></td>
            </tr>
        </table>

        <p><input type="submit" name="crea" value="Inserisci notizia"></p>
        <p><a href="index.php">Torna al menù di amministrazione</a></p>
    </form>
</div>

    <script src="vendor/tinymce/tinymce.min.js"></script>
    <script>
    // Editor WYSIWYG (TinyMCE 8.9.2 self-hosted) per il testo della notizia.
    // Trasforma il <textarea name="testo"> in un editor: senza JavaScript
    // il textarea resta utilizzabile normalmente.
    tinymce.init({
        selector: 'textarea[name="testo"]',
        language: 'it',
        language_url: 'vendor/tinymce/langs/it.js',
        plugins: 'link lists',
        toolbar: 'undo redo | blocks | bold italic underline | bullist numlist | link | removeformat',
        block_formats: 'Paragrafo=p;Titolo 2=h2;Titolo 3=h3',
        menubar: false,
        branding: false,
        // Configurazione conservativa: whitelist di elementi consentiti.
        // Esclude script, iframe, gestori di eventi e attributi inline non necessari.
        valid_elements: 'p[!style],br,b,strong,i,em,u,ul,ol,li,h2,h3,a[href|title|target|rel],hr'
    });
    </script>

<?php require_once('../html/footer.html');?>
</body>
</html>
