<?php 
include("dbpdo.php");

/**
 * Conta i record di una tabella o di una query personalizzata.
 * @param PDO $pdo
 * @param string $sql  Query di conteggio
 * @param array $params Parametri opzionali
 * @return int
 */
function conta(PDO $pdo, string $sql, array $params = []): int {
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return (int)$stmt->fetchColumn();
}

// conteggi
$total_schede   = conta($pdo, "SELECT COUNT(*) FROM ARCHIVIO");
$total_biblio   = conta($pdo, "SELECT COUNT(*) FROM BIBLIO_EST");
$total_immagini = conta($pdo, "SELECT COUNT(*) FROM IMMAGINI");

// Contatore visite: una sola visita per sessione PHP.
// Avvio difensivo della sessione: normalmente è già attiva perché
// index.php la avvia prima di qualsiasi output.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Incremento al massimo una volta per sessione.
if (empty($_SESSION['visita_contata'])) {
    $stmt = $pdo->prepare("UPDATE visite SET visitatori = visitatori + 1 WHERE id = 1");
    $stmt->execute();
    $_SESSION['visita_contata'] = true;
}

// Il valore mostrato è sempre letto dal database, anche senza incremento.
$total_visite = conta($pdo, "SELECT visitatori FROM visite WHERE id = 1");

?>

<h1>Contenuti</h1>
<p>
    <strong>Immagini:</strong> <?= $total_immagini ?><br>
    <strong>Schede:</strong> <?= $total_schede ?><br>
    <strong>Bibliografie:</strong> <?= $total_biblio ?>
</p>
