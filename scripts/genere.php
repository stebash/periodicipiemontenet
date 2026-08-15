<h1>Genere</h1>
<?php
// Query unica: generi + descrizione + conteggio record ARCHIVIO
$sql = "
    SELECT 
        GENERE.CODICE,
        GENERE.DESC_BREVE,
        COUNT(ARCHIVIO.GENERE) AS TOTALE
    FROM GENERE
    INNER JOIN ARCHIVIO ON GENERE.CODICE = ARCHIVIO.GENERE
    WHERE GENERE.DESC_BREVE != ''
    GROUP BY GENERE.CODICE, GENERE.DESC_BREVE
    ORDER BY GENERE.DESC_BREVE
";

$result = $pdo->query($sql);

if ($result && $result->rowCount() > 0) {

    echo "<ul>";

    while ($row = $result->fetch()) {

        $codice = $row['CODICE'];
        $desc   = $row['DESC_BREVE'];
        $totale = $row['TOTALE'];

        echo '<li><a href="index.php?c=genere&v=' . $codice . '&d=' . $desc . '">';
        echo $desc . " (" . $totale . ")";
        echo "</a></li>";
    }

    echo "</ul>";
}
?>
