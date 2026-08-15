<?php
// -----------------------------------------------------------------------------
// Recupero dell'ID scheda da GET/POST e validazione
// -----------------------------------------------------------------------------
$id = $_GET['s'] ?? $_POST['s'] ?? null;

if ($id === null || !ctype_digit((string) $id)) {
    echo '<p><strong>Errore:</strong> codice scheda non valido.</p>';
    exit;
}

// -----------------------------------------------------------------------------
// Connessione al DB
// -----------------------------------------------------------------------------
require __DIR__ . '/dbpdo.php'; // adatta il percorso se necessario

// (facoltativo se già impostato in dbpdo.php)
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// -----------------------------------------------------------------------------
// Piccola funzione per l'escape in HTML
// -----------------------------------------------------------------------------
function e(?string $value): string {
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

// -----------------------------------------------------------------------------
// QUERY PRINCIPALE: dati della scheda + descrizioni collegate
// -----------------------------------------------------------------------------
$sqlScheda = "
    SELECT
        a.*,
        g.DESC_BREVE          AS genere_descr,
        p.`pe.DESCRIZION`     AS periodicita_descr,
        m.`m.DESCRIZION`      AS moneta_descr,
        e.`es.DESCRIZION`     AS estensione_descr,
        l.LDESCRIZION         AS lingua_descr,
        pr.NOMEPROV           AS provincia_nome,
        b.NOME                AS biblioteca_principale,
        sg.DESC_BREVE         AS sottogenere_descr
    FROM ARCHIVIO AS a
    LEFT JOIN GENERE   AS g ON a.GENERE     = g.CODICE
    LEFT JOIN GENERE   AS sg ON a.SOTTOGEN  = sg.CODICE
    LEFT JOIN PERIOD   AS p ON a.PERIODIC   = p.CODICE
    LEFT JOIN MONETA   AS m ON a.MONETA     = m.CODICE
    LEFT JOIN ESTENS   AS e ON a.ESTENSIONE = e.CODICE
    LEFT JOIN LINGUA   AS l ON a.LINGUA     = l.CODICE
    LEFT JOIN PROV     AS pr ON a.PROVINCIA = pr.PROV
    LEFT JOIN BIBLIOT  AS b ON a.BIBLIOPRI  = b.CODICE
    WHERE a.SCHEDA = :id
    LIMIT 1
";



$stmtScheda = $pdo->prepare($sqlScheda);
$stmtScheda->execute([':id' => $id]);
$scheda = $stmtScheda->fetch(PDO::FETCH_ASSOC);

if (!$scheda) {
    echo '<p><strong>Attenzione:</strong> non ci sono schede con questo numero.</p>';
    exit;
}

// -----------------------------------------------------------------------------
// ALTRE QUERY CORRELATE (note, immagini, biblioteche, persone, bibliografia…)
// -----------------------------------------------------------------------------

// Note (variazioni titolo, ecc.)
$stmtNote = $pdo->prepare("
    SELECT DISTINCT *
    FROM ARCHIVIO
    WHERE SCHEDA = :id
");
$stmtNote->execute([':id' => $id]);

// Immagini
$stmtImg = $pdo->prepare("
    SELECT *
    FROM IMMAGINI
    WHERE SCHEDA = :id
      AND TIPO   = 'S'
");
$stmtImg->execute([':id' => $id]);

// Biblioteche
$stmtBib = $pdo->prepare("
    SELECT BIB_PER.*, BIBLIOT.*
    FROM BIB_PER
    LEFT JOIN BIBLIOT ON BIB_PER.BIBLIO = BIBLIOT.CODICE
    WHERE SCHEDA = :id
");
$stmtBib->execute([':id' => $id]);

// Note aggiuntive
$stmtNoteAgg = $pdo->prepare("
    SELECT *
    FROM MEMO2003
    WHERE SCHEDA = :id
");
$stmtNoteAgg->execute([':id' => $id]);

// Persone (redattori, ecc.)
$stmtPersone = $pdo->prepare("
    SELECT *
    FROM PERSONE
    WHERE codice = :id
      AND RUO <> ' '
");
$stmtPersone->execute([':id' => $id]);

// Bibliografia
$stmtBiblio = $pdo->prepare("
    SELECT *
    FROM BIBLIOGR_copy
    WHERE SCHEDA = :id
");
$stmtBiblio->execute([':id' => $id]);

// Opere
$stmtOpere = $pdo->prepare("
    SELECT *
    FROM opere
    WHERE id_periodico = :id
");
$stmtOpere->execute([':id' => $id]);

// -----------------------------------------------------------------------------
// OUTPUT HTML
// Mantengo la tua struttura: titolo, ul/li, ecc. ma con e() per l'escape
// -----------------------------------------------------------------------------
?>

<h1><?= e($scheda['TITOLO']); ?></h1>
<?php
// concateno i tre sottotitoli come facevi tu
$sottotitolo = trim(
    ($scheda['SOTTOTIT_1'] ?? '') . ' ' .
    ($scheda['SOTTOTIT_2'] ?? '') . ' ' .
    ($scheda['SOTTOTIT_3'] ?? '')
);
if ($sottotitolo !== ''): ?>
    <h4 style="font-size:20px"><?= e($sottotitolo); ?></h4>
<?php endif; ?>

<div id="scheda">
    <ul>
        <li><strong>Codice Scheda:</strong> <?= e($id); ?></li>
        <li><strong>Anno di fondazione:</strong> <?= e($scheda['ANNO_FOND']); ?></li>
        <li><strong>Anno di cessazione:</strong> <?= e($scheda['ANNO_CESS']); ?></li>

        <?php if (!empty($scheda['RIF_PREC'])): ?>
            <li>
                <strong>Scheda precedente:</strong>
                <a href="index.php?c=scheda&amp;s=<?= e($scheda['RIF_PREC']); ?>">
                    <?= e($scheda['RIF_PREC']); ?>
                </a>
            </li>
        <?php endif; ?>

        <?php if (!empty($scheda['RIF_SUCC'])): ?>
            <li>
                <strong>Scheda successiva:</strong>
                <a href="index.php?c=scheda&amp;s=<?= e($scheda['RIF_SUCC']); ?>">
                    <?= e($scheda['RIF_SUCC']); ?>
                </a>
            </li>
        <?php endif; ?>

        <li><strong>Altezza:</strong> <?= e($scheda['ALTEZZA']); ?></li>
        <li><strong>Base:</strong> <?= e($scheda['BASE']); ?></li>
        <li><strong>Pag. arabe:</strong> <?= e($scheda['PAGINE']); ?></li>
        <li><strong>Pag. romane:</strong> <?= e($scheda['PAGINE_ROM']); ?></li>

        <li><strong>Genere:</strong> <?= e($scheda['genere_descr']); ?></li>
        <li><strong>Altro genere:</strong> <?= e($scheda['sottogenere_descr']); ?></li>
        <li><strong>Periodicità:</strong> <?= e($scheda['periodicita_descr']); ?></li>
        <li><strong>Prezzo:</strong> <?= e($scheda['PREZZO']); ?></li>
        <li><strong>Moneta:</strong> <?= e($scheda['moneta_descr']); ?></li>
        <li><strong>Tiratura:</strong> <?= e($scheda['TIRATURA']); ?></li>
        <li><strong>Nell'anno:</strong> <?= e($scheda['NELLANNO']); ?></li>
        <li><strong>Area di diffusione:</strong> <?= e($scheda['estensione_descr']); ?></li>
        <li><strong>Lingua:</strong> <?= e($scheda['lingua_descr']); ?></li>
        <li><strong>Supplementi:</strong> <?= e($scheda['SUPPLEM']); ?></li>
        <li><strong>Sito internet:</strong> <?= e($scheda['URL']); ?></li>
        <li><strong>Indice:</strong> <?= e($scheda['INDICE']); ?></li>
        <li><strong>Editore:</strong> <?= e($scheda['EDITORE']); ?></li>
        <li><strong>Luogo di edizione:</strong> <?= e($scheda['LUOGO_EDIZ']); ?></li>
        <li><strong>Provincia:</strong> <?= e($scheda['provincia_nome']); ?></li>
        <li><strong>Tipografia:</strong> <?= e($scheda['TIPOGRAFIA']); ?></li>
        <li><strong>Luogo di stampa:</strong> <?= e($scheda['LUOGO_STAM']); ?></li>
        <li><strong>Continua:</strong> <?= e($scheda['CONTINUA']); ?></li>

        <?php if ($stmtPersone->rowCount() > 0): ?>
            <li><h2>Persone</h2></li>
            <?php while ($redattore = $stmtPersone->fetch(PDO::FETCH_ASSOC)): ?>
                <li><?= e($redattore['RUO']) . ': ' . e($redattore['PERS']); ?></li>
            <?php endwhile; ?>
        <?php endif; ?>

        <?php
        // Se usi ancora variazioni.php lo puoi mantenere qui
        include 'variazioni.php';
        
    if (
        !empty($scheda['biblioteca_principale']) ||
        !empty($scheda['INIPRI']) ||
        !empty($scheda['FINEPRI'])
    ): ?>

    <li><h2>Biblioteca principale</h2></li>

    <?php if (!empty($scheda['biblioteca_principale'])): ?>
        <li><strong>Biblioteca principale:</strong>
            <?= e($scheda['biblioteca_principale']); ?>
        </li>
    <?php endif; ?>

    <?php if (!empty($scheda['INIPRI'])): ?>
        <li><strong>Inizio:</strong> <?= e($scheda['INIPRI']); ?></li>
    <?php endif; ?>

    <?php if (!empty($scheda['FINEPRI'])): ?>
        <li><strong>Fine:</strong> <?= e($scheda['FINEPRI']); ?></li>
    <?php endif; ?>

<?php endif; ?>



        <li><strong>Note:</strong>
            <?php while ($nota = $stmtNote->fetch(PDO::FETCH_ASSOC)): ?>
                <div><?= e(stripslashes($nota['NOTE'] ?? '')); ?></div>
            <?php endwhile; ?>
        </li>

<li><strong>Archivio:</strong> <?= e($scheda['ARCHIVIO']); ?></li>

<?php if (!empty($scheda['REPERTORIO'])): ?>
    <li><strong>Repertorio:</strong> <?= e($scheda['REPERTORIO']); ?></li>
<?php endif; ?>

<?php if (!empty($scheda['FONDOPRIV'])): ?>
    <li><strong>Fondo privato:</strong> <?= e($scheda['FONDOPRIV']); ?></li>
<?php endif; ?>

<?php if (!empty($scheda['MICROFILM'])): ?>
    <li><strong>Microfilm / Scansione:</strong> <?= e($scheda['MICROFILM']); ?></li>
<?php endif; ?>

<?php if (!empty($scheda['ardig'])): ?>
    <li><strong>Archivi digitali:</strong> <?= e($scheda['ardig']); ?></li>
<?php endif; ?>

<?php if (!empty($scheda['cataloghi_online'])): ?>
    <li><strong>Cataloghi online:</strong> <?= e($scheda['cataloghi_online']); ?></li>
<?php endif; ?>


<?php if (!empty($scheda['bibliografia'])): ?>
    <li><strong>Bibliografia:</strong> <?= e($scheda['bibliografia']); ?></li>
<?php endif; ?>

<?php if ($stmtNoteAgg->rowCount() > 0): ?>
    <li><h2>Note aggiuntive</h2></li>
    <?php while ($na = $stmtNoteAgg->fetch(PDO::FETCH_ASSOC)): ?>
        <?php
            $raw = (string)($na['MEMO'] ?? '');

            // 1) Decodifica entità HTML (&#39; &agrave; &quot; ecc.) -> caratteri reali
            $decoded = html_entity_decode($raw, ENT_QUOTES | ENT_HTML5, 'UTF-8');

            // 2) Rimuovi eventuali tag HTML (se presenti)
            $clean = strip_tags($decoded);

            // (opzionale) normalizza spazi
            $clean = trim(preg_replace('/\s+/', ' ', $clean));
        ?>
        <li><?= e($clean); ?></li>
    <?php endwhile; ?>
<?php endif; ?>


        <?php if ($stmtOpere->rowCount() > 0): ?>
            <li><h2>Opere</h2></li>
            <?php while ($op = $stmtOpere->fetch(PDO::FETCH_ASSOC)): ?>
                <li><?= e($op['titolo'] ?? ''); ?></li>
            <?php endwhile; ?>
        <?php endif; ?>
    </ul>

    <?php while ($img = $stmtImg->fetch(PDO::FETCH_ASSOC)): ?>
        <p>
            <img src="img/img<?= e($img['ID']); ?>.jpg" style="width:100%; max-width:300px; height:auto;" alt="Immagine scheda <?= e($id); ?>">
        </p>
    <?php endwhile; ?>
</div>
