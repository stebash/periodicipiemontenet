<?php
session_start();

if (!isset($_SESSION['username']) || $_SESSION['username'] !== 'admin') {
    header('Location: messages.php?id=2');
    exit;
}

/*
 * Connessione PDO al database.
 * allegati.php si trova nella cartella admin/, mentre dbpdo.php si trova
 * nella cartella scripts/ alla stessa profondita' di admin/.
 */
require_once dirname(__DIR__) . '/scripts/dbpdo.php';

if (!isset($pdo) || !($pdo instanceof PDO)) {
    die('Connessione al database non disponibile. Verificare scripts/dbpdo.php.');
}

$attachDir = dirname(__DIR__) . '/attach';

if (!is_dir($attachDir)) {
    if (!mkdir($attachDir, 0755, true)) {
        die('Impossibile creare la cartella attach.');
    }
}

$op = $_POST['op'] ?? '';
$id = filter_input(INPUT_POST, 's', FILTER_VALIDATE_INT);

if (!$id || $id < 1) {
    die('Numero della notizia non valido.');
}

// Verifica che la notizia esista e recupera l'eventuale allegato corrente.
$stmt = $pdo->prepare('SELECT allegato FROM notizie WHERE id = ?');
$stmt->execute([$id]);
$notizia = $stmt->fetch();

if (!$notizia) {
    die('Notizia non trovata.');
}

if ($op === 'add') {
    if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
        die('Errore durante il caricamento del file.');
    }

    $tmpName = $_FILES['file']['tmp_name'];
    $originalName = $_FILES['file']['name'];
    $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));

    if ($extension !== 'pdf') {
        die('Sono ammessi soltanto file PDF.');
    }

    // Controllo MIME lato server, non soltanto sull'estensione.
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file($tmpName);
    if ($mime !== 'application/pdf') {
        die('Il file selezionato non risulta essere un PDF valido.');
    }

    // Limite massimo: 20 MB.
    if ($_FILES['file']['size'] > 20 * 1024 * 1024) {
        die('Il file supera la dimensione massima consentita di 20 MB.');
    }

    $fileName = 'att' . $id . '.pdf';
    $destination = $attachDir . '/' . $fileName;

    if (!move_uploaded_file($tmpName, $destination)) {
        die('Impossibile salvare il file nella cartella attach.');
    }

    try {
        $stmt = $pdo->prepare('UPDATE notizie SET allegato = ? WHERE id = ?');
        $stmt->execute([$fileName, $id]);
    } catch (PDOException $e) {
        @unlink($destination);
        die('Errore durante l\'aggiornamento del database.');
    }

    header('Location: index.php?msg=allegato_aggiunto');
    exit;
}

if ($op === 'delete') {
    $fileName = $notizia['allegato'] ?? null;

    // Compatibilita' con i file storici gia' chiamati att<ID>.pdf.
    if (!$fileName) {
        $candidate = 'att' . $id . '.pdf';
        if (is_file($attachDir . '/' . $candidate)) {
            $fileName = $candidate;
        }
    }

    if ($fileName) {
        // basename evita che un valore anomalo nel DB punti fuori da /attach.
        $path = $attachDir . '/' . basename($fileName);
        if (is_file($path) && !unlink($path)) {
            die('Impossibile eliminare il file dalla cartella attach.');
        }
    }

    try {
        $stmt = $pdo->prepare('UPDATE notizie SET allegato = NULL WHERE id = ?');
        $stmt->execute([$id]);
    } catch (PDOException $e) {
        die('Errore durante l\'aggiornamento del database.');
    }

    header('Location: index.php?msg=allegato_eliminato');
    exit;
}

die('Operazione non valida.');
