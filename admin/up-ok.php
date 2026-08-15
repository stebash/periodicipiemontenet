<?php
declare(strict_types=1);
session_start();

// 1) Autorizzazione
if (!isset($_SESSION['username']) || $_SESSION['username'] !== 'admin') {
    header("Location: login.php");
    exit;
}

// 2) Connessione DB
require __DIR__ . "/../scripts/dbpdo.php";
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// 3) Input
$scheda = trim((string)($_POST['s'] ?? ''));
$tipo   = trim((string)($_POST['tipo'] ?? ''));

// scheda numerica + tipo ammesso
if ($scheda === '' || !ctype_digit($scheda) || !in_array($tipo, ['S', 'B'], true)) {
    header("Location:messages.php?id=12");
    exit;
}

// 4) File upload
if (!isset($_FILES['file']) || !is_array($_FILES['file'])) {
    header("Location:messages.php?id=12");
    exit;
}

$file = $_FILES['file'];

if (!isset($file['error']) || $file['error'] !== UPLOAD_ERR_OK) {
    header("Location:messages.php?id=12");
    exit;
}

if (!isset($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
    header("Location:messages.php?id=12");
    exit;
}

// Dimensione max 4MB
$maxBytes = 4_000_000;
$size = (int)($file['size'] ?? 0);
if ($size <= 0 || $size > $maxBytes) {
    header("Location:messages.php?id=12");
    exit;
}

// MIME reale
$finfo = new finfo(FILEINFO_MIME_TYPE);
$mime  = $finfo->file($file['tmp_name']);

$allowed = [
    'image/jpeg' => 'jpg',
    'image/png'  => 'png',
    'image/gif'  => 'gif',
];

if (!isset($allowed[$mime])) {
    header("Location:messages.php?id=12");
    exit;
}

$ext = $allowed[$mime];

// 5) Directory destinazione (NO realpath qui)
$uploadDir = __DIR__ . "/../img";

// se non esiste, provo a crearla
if (!is_dir($uploadDir)) {
    if (!mkdir($uploadDir, 0755, true)) {
        header("Location:messages.php?id=12");
        exit;
    }
}

// controllo scrittura
if (!is_writable($uploadDir)) {
    header("Location:messages.php?id=12");
    exit;
}

// 6) DB + move file
try {
    $pdo->beginTransaction();

    $stmt = $pdo->prepare("INSERT INTO IMMAGINI (scheda, TIPO) VALUES (:scheda, :tipo)");
    $stmt->execute([
        ':scheda' => (int)$scheda,
        ':tipo'   => $tipo,
    ]);

    $imgId = (int)$pdo->lastInsertId();
    if ($imgId <= 0) {
        $pdo->rollBack();
        header("Location:messages.php?id=12");
        exit;
    }

    $newName  = "img{$imgId}.{$ext}";
    $destPath = $uploadDir . DIRECTORY_SEPARATOR . $newName;

    // sposto il file
    if (!move_uploaded_file($file['tmp_name'], $destPath)) {
        $pdo->rollBack();
        header("Location:messages.php?id=12");
        exit;
    }

    // (Facoltativo ma utile) Imposta permessi al file
    @chmod($destPath, 0644);

    $pdo->commit();

    header("Location:messages.php?id=11");
    exit;

} catch (Throwable $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    // error_log($e->getMessage());
    header("Location:messages.php?id=12");
    exit;
}
