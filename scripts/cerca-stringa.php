<?php
// mi collego al db
include ("dbpdo.php");

// recupero e pulisco la stringa
$stringa = isset($_POST['stringa']) ? trim($_POST['stringa']) : '';
$cerca   = isset($_POST['cerca']) ? $_POST['cerca'] : '';

if (empty($cerca)) {
    echo 'Tramite il menù a tendina indica se stai cercando un periodico, una persona o tutto<br />';
}

if (empty($stringa)) {
    echo 'Inserisci il nome della persona o il titolo del periodico da cercare<br />';
    exit;
}

// preparo il pattern per il LIKE
$pattern = '%' . $stringa . '%';

// ================================
// 1) PERIODICI (ARCHIVIO)
// ================================
if ($cerca == 'per' || $cerca == 'tutti') {

    $stmt = $pdo->prepare("SELECT SCHEDA, TITOLO 
                           FROM ARCHIVIO 
                           WHERE TITOLO LIKE :pat
                           ORDER BY TITOLO");
    $stmt->execute([':pat' => $pattern]);
    $periodici = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $numPeriodici = count($periodici);

    echo 'Risultati tra i <strong>periodici</strong> per <strong>' . htmlspecialchars($stringa, ENT_QUOTES, 'UTF-8') . '</strong> ' . $numPeriodici . " risultati<br />";

    if ($numPeriodici > 0) {
        echo "<ul>";
        foreach ($periodici as $row) {
            echo '<li><a href="index.php?c=scheda&s=' . (int)$row['SCHEDA'] . '">';
            echo htmlspecialchars($row['TITOLO'], ENT_QUOTES, 'UTF-8');
            echo "</a></li>";
        }
        echo "</ul>";
    } else {
        echo 'Nessun periodico trovato<br />';
    }
}

// ================================
// 2) PERSONE & BIBLIOGRAFIE
//    (PERSONE + BIBLIO_EST)
//    e PERSONE nelle schede (PERSONE + ARCHIVIO)
// ================================
if ($cerca == 'persone' || $cerca == 'tutti') {

    // BIBLIOGRAFIE collegate a persone
    $bibliografie = $pdo->prepare("
        SELECT p.PERS, b.codice, b.TITOLO
        FROM PERSONE AS p
        RIGHT JOIN BIBLIO_EST AS b ON p.codice = b.codice
        WHERE p.PERS LIKE :pat
        ORDER BY p.PERS ASC, b.TITOLO ASC
    ");
    $bibliografie->execute([':pat' => $pattern]);
    $rowsBiblio = $bibliografie->fetchAll(PDO::FETCH_ASSOC);
    $numBiblio  = count($rowsBiblio);

    echo 'Risultati tra le <strong>bibliografie</strong> per <strong>' . htmlspecialchars($stringa, ENT_QUOTES, 'UTF-8') . '</strong> ' . $numBiblio . " risultati<br />";

    if ($numBiblio > 0) {
        echo "<ul>";
        foreach ($rowsBiblio as $row) {
            echo '<li>' . htmlspecialchars($row['PERS'], ENT_QUOTES, 'UTF-8') . ' ';
            echo '<a href="index.php?c=bibliografia&s=' . (int)$row['codice'] . '">';
            echo htmlspecialchars($row['TITOLO'], ENT_QUOTES, 'UTF-8');
            echo "</a></li>";
        }
        echo "</ul>";
    } else {
        echo 'Non ci sono bibliografie legate alla persona cercata<br />';
    }

    // PERSONE collegate alle schede periodici
    $direttore = $pdo->prepare("
        SELECT p.PERS, a.TITOLO, a.SCHEDA
        FROM PERSONE AS p
        INNER JOIN ARCHIVIO AS a ON p.codice = a.SCHEDA
        WHERE p.PERS LIKE :pat AND p.RUO != ' '
        ORDER BY p.PERS ASC, a.TITOLO ASC
    ");
    $direttore->execute([':pat' => $pattern]);
    $rowsDirettore = $direttore->fetchAll(PDO::FETCH_ASSOC);
    $numDirettore  = count($rowsDirettore);

    echo 'Risultati tra le <strong>persone presenti nelle schede periodici</strong> per <strong>' . htmlspecialchars($stringa, ENT_QUOTES, 'UTF-8') . '</strong> ' . $numDirettore . " risultati<br />";

    if ($numDirettore > 0) {
        echo "<ul>";
        foreach ($rowsDirettore as $row) {
            echo '<li>' . htmlspecialchars($row['PERS'], ENT_QUOTES, 'UTF-8') . ' ';
            echo '<a href="index.php?c=scheda&s=' . (int)$row['SCHEDA'] . '">';
            echo htmlspecialchars($row['TITOLO'], ENT_QUOTES, 'UTF-8');
            echo "</a></li>";
        }
        echo "</ul>";
    } else {
        echo 'Non ci sono periodici legati alla persona cercata<br />';
    }

    // ================================
    // 3) BIOGRAFIE (PERSONA + PERSONE)
// ================================
    $biografie = $pdo->prepare("
    SELECT PID, NOME
    FROM PERSONA
    WHERE NOME LIKE :pat
    ORDER BY NOME ASC
");
$biografie->execute([':pat' => $pattern]);
$rowsBio = $biografie->fetchAll(PDO::FETCH_ASSOC);
$numBio  = count($rowsBio);

echo 'Risultati tra le <strong>biografie</strong> per <strong>' 
     . htmlspecialchars($stringa, ENT_QUOTES, 'UTF-8') . '</strong> ' 
     . $numBio . " risultati<br />";

if ($numBio > 0) {
    echo "<ul>";
    foreach ($rowsBio as $row) {
        echo '<li><a href="index.php?c=biografia&s=' . (int)$row['PID'] . '">';
        echo htmlspecialchars($row['NOME'], ENT_QUOTES, 'UTF-8');
        echo "</a></li>";
    }
    echo "</ul>";
} else {
    echo 'Non ci sono biografie legate alla persona cercata<br />';
}

}
?>
