<?php
// Parametri in ingresso
$c   = isset($_GET['c']) ? $_GET['c'] : '';
$v   = isset($_GET['v']) ? $_GET['v'] : null;   // es. codice GENERE / PROVINCIA / PROV per biblioteca
$b   = isset($_GET['b']) ? $_GET['b'] : null;   // biblioteca selezionata
$d   = isset($_GET['d']) ? $_GET['d'] : '';     // descrizione (es. nome genere, provincia, ecc.)
$af  = isset($_REQUEST['af']) ? $_REQUEST['af'] : null; // anno fondazione (può arrivare da POST o GET)

// Paginazione
$x_pag = 20;
$pag   = isset($_GET['pag']) && is_numeric($_GET['pag']) && $_GET['pag'] > 0 ? (int)$_GET['pag'] : 1;
$first = ($pag - 1) * $x_pag;

// Funzione per escaping HTML
function e(string $val): string {
    return htmlspecialchars($val, ENT_QUOTES, 'UTF-8');
}

// Variabile per il conteggio totale (per la paginazione)
$totalRecords = 0;

// ======================
// 1) RICERCA PER GENERE / PROVINCIA
// ======================
if ($c === 'genere' || $c === 'provincia') {

    if ($c === 'genere') {
        $colonna = 'GENERE';
        $titolo  = 'GENERE ';
    } else {
        $colonna = 'PROVINCIA';
        $titolo  = 'PROVINCIA ';
    }

    // Conteggio
    $sqlCount = "SELECT COUNT(*) FROM ARCHIVIO WHERE $colonna = :val AND TITOLO != ''";
    $stmtCount = $pdo->prepare($sqlCount);
    $stmtCount->execute([':val' => $v]);
    $totalRecords = (int) $stmtCount->fetchColumn();

    // Dati
    $sql = "SELECT TITOLO, SCHEDA
            FROM ARCHIVIO
            WHERE $colonna = :val AND TITOLO != ''
            ORDER BY SCHEDA
            LIMIT :offset, :limit";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':val', $v, PDO::PARAM_STR);
    $stmt->bindValue(':offset', $first, PDO::PARAM_INT);
    $stmt->bindValue(':limit', $x_pag, PDO::PARAM_INT);
    $stmt->execute();

    echo '<h1>' . e($titolo . $d) . '</h1>';
    echo '<ul>';
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo '<li><a href="index.php?c=scheda&s=' . e($row['SCHEDA']) . '">'
           . e($row['TITOLO']) . '</a></li>';
    }
    echo '</ul>';
}

// ======================
// 2) ELENCO BIBLIOTECHE PER PROVINCIA
// ======================
elseif ($c === 'biblioteca') {

    $titolo = 'BIBLIOTECA ';

    $sqlCount = "SELECT COUNT(*) FROM BIBLIOT WHERE COD_PROV = :prov";
    $stmtCount = $pdo->prepare($sqlCount);
    $stmtCount->execute([':prov' => $v]);
    $totalRecords = (int) $stmtCount->fetchColumn();

    $sql = "SELECT CODICE, NOME
            FROM BIBLIOT
            WHERE COD_PROV = :prov
            ORDER BY NOME
            LIMIT :offset, :limit";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':prov', $v, PDO::PARAM_STR);
    $stmt->bindValue(':offset', $first, PDO::PARAM_INT);
    $stmt->bindValue(':limit', $x_pag, PDO::PARAM_INT);
    $stmt->execute();

    echo '<h1>' . e($titolo . $d) . '</h1>';
    echo '<ul>';
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo '<li><a href="index.php?c=cerca-biblioteca&d=' . e($d) . '&b=' . e($row['CODICE']) . '">'
           . e($row['NOME']) . '</a></li>';
    }
    echo '</ul>';
}

// ======================
// 3) RICERCA PERIODICI PER BIBLIOTECA
// ======================
elseif ($c === 'cerca-biblioteca') {

    $titolo = 'Cerca biblioteca di ';

    $sqlCount = "SELECT COUNT(*) FROM ARCHIVIO WHERE BIBLIOPRI = :biblio";
    $stmtCount = $pdo->prepare($sqlCount);
    $stmtCount->execute([':biblio' => $b]);
    $totalRecords = (int) $stmtCount->fetchColumn();

    $sql = "SELECT TITOLO, SCHEDA
            FROM ARCHIVIO
            WHERE BIBLIOPRI = :biblio
            ORDER BY TITOLO
            LIMIT :offset, :limit";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':biblio', $b, PDO::PARAM_STR);
    $stmt->bindValue(':offset', $first, PDO::PARAM_INT);
    $stmt->bindValue(':limit', $x_pag, PDO::PARAM_INT);
    $stmt->execute();

    echo '<h1>' . e($titolo . $d) . '</h1>';
    echo '<ul>';
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo '<li><a href="index.php?c=scheda&d=' . e($d) . '&s=' . e($row['SCHEDA']) . '">'
           . e($row['TITOLO']) . '</a></li>';
    }
    echo '</ul>';
}

// ======================
// 4) BIBLIOGRAFIE PER TIPOLOGIA
// ======================
elseif (
    $c === 'monografie' ||
    $c === 'opere-collettive' ||
    $c === 'tesi' ||
    $c === 'manoscritti' ||
    $c === 'opuscoli' ||
    $c === 'estratti' ||
    $c === 'spogli-bibliografici' ||
    $c === 'citazioni' ||
    $c === 'cataloghi-repertori' ||
    $c === 'articoli-saggi'
) {
    // Mappatura tipologia
    $tipologie = [
        'monografie'          => ['id' => '0',  'titolo' => 'Monografie'],
        'opere-collettive'    => ['id' => '1',  'titolo' => 'Opere collettive'],
        'tesi'                => ['id' => '2',  'titolo' => 'Tesi'],
        'manoscritti'         => ['id' => '3',  'titolo' => 'Manoscritti'],
        'opuscoli'            => ['id' => '4',  'titolo' => 'Opuscoli'],
        'estratti'            => ['id' => '5',  'titolo' => 'Estratti'],
        'spogli-bibliografici'=> ['id' => '6',  'titolo' => 'Spogli bibliografici'],
        'citazioni'           => ['id' => '7',  'titolo' => 'Citazioni'],
        'cataloghi-repertori' => ['id' => '9',  'titolo' => 'Cataloghi repertori'],
        'articoli-saggi'      => ['id' => '10', 'titolo' => 'Articoli e saggi'],
    ];

    if (!isset($tipologie[$c])) {
        $totalRecords = 0;
    } else {
        $idTipologia = $tipologie[$c]['id'];
        $titolo      = $tipologie[$c]['titolo'];

        $sqlCount = "SELECT COUNT(*) FROM BIBLIO_EST WHERE CodTipologia = :id";
        $stmtCount = $pdo->prepare($sqlCount);
        $stmtCount->execute([':id' => $idTipologia]);
        $totalRecords = (int) $stmtCount->fetchColumn();

        $sql = "SELECT DISTINCT codice, TITOLO
                FROM BIBLIO_EST
                WHERE CodTipologia = :id
                ORDER BY TITOLO
                LIMIT :offset, :limit";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':id', $idTipologia, PDO::PARAM_STR);
        $stmt->bindValue(':offset', $first, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $x_pag, PDO::PARAM_INT);
        $stmt->execute();

        echo '<h1>' . e($titolo . $d) . '</h1>';
        echo '<ul>';
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo '<li><a href="index.php?c=bibliografia&s=' . e($row['codice']) . '">'
               . e($row['TITOLO']) . '</a></li>';
        }
        echo '</ul>';
    }
}

// ======================
// 5) TESTATE IN VITA
// ======================
elseif ($c === 'testate-in-vita') {

    $titolo = 'TESTATE IN VITA';

    $sqlCount = "SELECT COUNT(*) FROM ARCHIVIO WHERE CONTINUA = 'S'";
    $stmtCount = $pdo->query($sqlCount);
    $totalRecords = (int) $stmtCount->fetchColumn();

    $sql = "SELECT TITOLO, SCHEDA, ANNO_FOND
            FROM ARCHIVIO
            WHERE CONTINUA = 'S'
            ORDER BY TITOLO
            LIMIT :offset, :limit";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':offset', $first, PDO::PARAM_INT);
    $stmt->bindValue(':limit', $x_pag, PDO::PARAM_INT);
    $stmt->execute();

    echo '<h1>' . e($titolo . $d) . '</h1>';
    echo '<ul>';
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo '<li><a href="index.php?c=scheda&s=' . e($row['SCHEDA']) . '">'
           . e($row['TITOLO']) . '</a></li>';
    }
    echo '</ul>';
}

// ======================
// 6) RICERCA PER ANNO DI FONDAZIONE
// ======================
elseif ($c === 'anno-fondazione') {

    $titolo = 'ANNO DI FONDAZIONE ';

    // $af può arrivare da POST (form_ricerca) o da GET (paginazione)
    if ($af === null || $af === '') {
        echo '<p>Nessun anno di fondazione specificato.</p>';
        $totalRecords = 0;
    } else {
        $sqlCount = "SELECT COUNT(*) FROM ARCHIVIO WHERE ANNO_FOND = :af";
        $stmtCount = $pdo->prepare($sqlCount);
        $stmtCount->execute([':af' => $af]);
        $totalRecords = (int) $stmtCount->fetchColumn();

        $sql = "SELECT TITOLO, SCHEDA
                FROM ARCHIVIO
                WHERE ANNO_FOND = :af
                ORDER BY TITOLO
                LIMIT :offset, :limit";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':af', $af, PDO::PARAM_STR);
        $stmt->bindValue(':offset', $first, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $x_pag, PDO::PARAM_INT);
        $stmt->execute();

        echo '<h1>' . e($titolo . $af) . '</h1>';
        echo '<ul>';
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo '<li><a href="index.php?c=scheda&s=' . e($row['SCHEDA']) . '">'
               . e($row['TITOLO']) . '</a></li>';
        }
        echo '</ul>';
    }
}

// ======================
// 7) PAGINAZIONE COMUNE
// ======================

if ($totalRecords > $x_pag) {
    $all_pages = (int) ceil($totalRecords / $x_pag);

    // Costruisco la base della query string senza 'pag'
    $params = ['c' => $c];

    if ($v !== null)  $params['v']  = $v;
    if ($d !== '')    $params['d']  = $d;
    if ($b !== null)  $params['b']  = $b;
    if ($af !== null) $params['af'] = $af;

    $baseUrl = $_SERVER['PHP_SELF'] . '?' . http_build_query($params);

    if ($pag > 1) {
        echo '<a href="' . e($baseUrl . '&pag=' . ($pag - 1)) . '">Pagina Indietro</a>&nbsp;';
    }
    if ($pag < $all_pages) {
        echo '<a href="' . e($baseUrl . '&pag=' . ($pag + 1)) . '">Pagina Avanti</a>';
    }
}
?>
