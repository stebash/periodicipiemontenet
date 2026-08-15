<?php
// mi collego al db
include("dbpdo.php");

// recupero l'ID della scheda (PID) da GET o POST
$s = $_POST['s'] ?? $_GET['s'] ?? null;

// controllo di base sull'ID
if ($s === null || !ctype_digit((string)$s)) {
    echo 'ID biografia non valido.';
    return;
}

// preparo la query (uso prepared statement)
$sql = "
    SELECT 
        p.PID,
        p.NOME,
        p.BIO,
        i.ID   AS IMG_ID
    FROM PERSONA AS p
    LEFT JOIN IMMAGINI AS i 
        ON p.PID = i.SCHEDA
       AND i.TIPO = 'B'
    WHERE p.PID = :pid
    ORDER BY i.ID ASC
    LIMIT 1
";


$stmt = $pdo->prepare($sql);
$stmt->execute([':pid' => (int)$s]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$row) {
    echo 'Non ci sono biografie con questo numero';
    return;
}

// se esiste un'immagine associata e il tipo è quello giusto (es. 'B' per biografia)
$imgTag = '';
$imgTag = '';
if (!empty($row['IMG_ID'])) {
    $filename = '/img_bio/img' . (int)$row['IMG_ID'] . '.jpg';
    $imgTag = '<p style="text-align:right;">
        <img src="'.htmlspecialchars($filename, ENT_QUOTES, 'UTF-8').'" width="150" alt="'.htmlspecialchars($row['NOME'], ENT_QUOTES, 'UTF-8').'">
    </p>';
}


// output HTML
echo $imgTag;
?>
<ul>
    <li><strong>Scheda</strong>: <?php echo (int)$row['PID']; ?></li>
    <li><strong>Nome</strong>: <?php echo htmlspecialchars($row['NOME'], ENT_QUOTES, 'UTF-8'); ?></li>
    <li><strong>Dati biografici</strong>: <?php echo nl2br($row['BIO']); ?></li>
</ul>
