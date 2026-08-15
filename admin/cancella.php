<?php
session_start();

// 1. Controllo Autorizzazione
if (!isset($_SESSION['username']) || $_SESSION['username'] !== 'admin') {
    header("Location: login.php"); // O una pagina di errore appropriata
    exit;
}

// 2. Connessione al DB
require __DIR__ . "/../scripts/dbpdo.php";
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// 3. Recupero e validazione parametri
$s = $_POST['s'] ?? null; // ID del record
$t = $_POST['t'] ?? null; // Tipo (bio, bib, per)

if (!$s || !$t) {
    header("Location: messages.php?id=4"); // Errore parametri mancanti
    exit;
}

// 4. Definizione della tabella e della colonna ID in base al tipo
$map = [
    'bio' => ['table' => 'PERSONA',  'col' => 'PID'],
    'bib' => ['table' => 'BIBLIO_EST', 'col' => 'codice'],
    'per' => ['table' => 'ARCHIVIO', 'col' => 'SCHEDA']
];

if (!array_key_exists($t, $map)) {
    header("Location: messages.php?id=4");
    exit;
}

$table = $map[$t]['table'];
$column = $map[$t]['col'];

try {
    // 5. Verifica esistenza del record
    $checkStmt = $pdo->prepare("SELECT 1 FROM $table WHERE $column = :id");
    $checkStmt->execute([':id' => $s]);
    
    if ($checkStmt->fetch()) {
        // 6. Esecuzione eliminazione
        $deleteStmt = $pdo->prepare("DELETE FROM $table WHERE $column = :id");
        $deleteStmt->execute([':id' => $s]);
        
        // Successo
        header("Location: messages.php?id=3");
        exit;
    } else {
        // Record non trovato
        header("Location: messages.php?id=4");
        exit;
    }
} catch (PDOException $e) {
    // Gestione errori database
    // In produzione meglio loggare l'errore $e->getMessage() e mostrare un messaggio generico
    header("Location: messages.php?id=2");
    exit;
}