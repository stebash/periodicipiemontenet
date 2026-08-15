<?php
// -----------------------------------------------------------------------------
// Questo file si aspetta che $pdo e $id esistano (creati in scheda.php)
// -----------------------------------------------------------------------------
if (!isset($pdo, $id)) {
    return; // esco silenziosamente se chiamato fuori contesto
}

// Se per qualche motivo e() non esistesse (es. incluso altrove), la definisco
if (!function_exists('e')) {
    function e(?string $value): string {
        return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
    }
}

// -----------------------------------------------------------------------------
// Helper per eseguire query e avere direttamente gli array di risultati
// -----------------------------------------------------------------------------
function fetchAllByScheda(PDO $pdo, string $sql, int $id): array {
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':id' => $id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// -----------------------------------------------------------------------------
// Helper formattazione SOLO ANNO
// -----------------------------------------------------------------------------

/**
 * Per variazioni con campi DATA_V + ANNO_V:
 * - Mostra solo ANNO_V (se presente)
 */
function formatAnnoV(?string $data_v, ?string $anno_v): string {
    $anno_v = trim((string)$anno_v);
    return $anno_v !== '' ? $anno_v : '';
}

/**
 * Per variazioni che hanno un solo campo DATA:
 * - Estrae solo l'anno (prima occorrenza di 4 cifre)
 */
function formatAnnoDaData(?string $data): string {
    $data = trim((string)$data);
    if ($data === '') {
        return '';
    }

    if (preg_match('/(\d{4})/', $data, $match)) {
        return $match[1]; // anno
    }

    return '';
}

// -----------------------------------------------------------------------------
// QUERY: recupero tutte le variazioni
// -----------------------------------------------------------------------------

// variazioni del titolo
$variazionitit = fetchAllByScheda(
    $pdo,
    "SELECT * FROM VARIA_TI WHERE SCHEDA = :id",
    (int)$id
);

// variazioni del sottotitolo
$variazionisot = fetchAllByScheda(
    $pdo,
    "SELECT * FROM VARIA_SO WHERE SCHEDA = :id",
    (int)$id
);

// variazioni del direttore
$direttore = fetchAllByScheda(
    $pdo,
    "SELECT * FROM VARIA_DI WHERE SCHEDA = :id",
    (int)$id
);

// variazioni di editore e luogo di edizione
$editore = fetchAllByScheda(
    $pdo,
    "SELECT * FROM VARIA_ED WHERE SCHEDA = :id",
    (int)$id
);

// variazioni di formato
$formato = fetchAllByScheda(
    $pdo,
    "SELECT * FROM VARIA_FO WHERE SCHEDA = :id",
    (int)$id
);

// variazioni del numero di pagine
$pagine = fetchAllByScheda(
    $pdo,
    "SELECT * FROM VARIA_PA WHERE SCHEDA = :id",
    (int)$id
);

// variazioni della frequenza (join con PERIOD)
$periodicita = fetchAllByScheda(
    $pdo,
    "SELECT v.*, p.`pe.DESCRIZION` AS periodicita_descr
     FROM VARIA_PE AS v
     LEFT JOIN PERIOD AS p ON v.PERIODIC = p.CODICE
     WHERE v.SCHEDA = :id",
    (int)$id
);

// variazioni del prezzo (join con MONETA)
$prezzo = fetchAllByScheda(
    $pdo,
    "SELECT v.*, m.`m.DESCRIZION` AS moneta_descr
     FROM VARIA_PR AS v
     LEFT JOIN MONETA AS m ON v.MONETA = m.CODICE
     WHERE v.SCHEDA = :id",
    (int)$id
);

// variazioni di tipografia
$tipografia = fetchAllByScheda(
    $pdo,
    "SELECT * FROM VARIA_TP WHERE SCHEDA = :id",
    (int)$id
);

// variazioni di tiratura
$tiratura = fetchAllByScheda(
    $pdo,
    "SELECT * FROM VARIA_TR WHERE SCHEDA = :id",
    (int)$id
);

// -----------------------------------------------------------------------------
// OUTPUT HTML – uso <li> perché il file è incluso dentro <ul> della scheda
// -----------------------------------------------------------------------------
?>

<?php if (!empty($variazionitit)): ?>
    <li><h2>Variazioni titolo</h2></li>
    <?php foreach ($variazionitit as $r): ?>
        <li><strong>Data variazione: </strong>
            <?= e(formatAnnoV($r['DATA_V'] ?? '', $r['ANNO_V'] ?? '')); ?>
        </li>
        <li><strong>Titolo:</strong>
            <?= e(trim(($r['TIT_1'] ?? '') . ' ' . ($r['TIT_2'] ?? ''))); ?>
        </li>
        <br>
    <?php endforeach; ?>
<?php endif; ?>

<?php if (!empty($variazionisot)): ?>
    <li><h2>Variazioni sottotitolo</h2></li>
    <?php foreach ($variazionisot as $r): ?>
        <li><strong>Data variazione: </strong>
            <?= e(formatAnnoV($r['DATA_V'] ?? '', $r['ANNO_V'] ?? '')); ?>
        </li>
        <li><strong>Sottotitolo:</strong>
            <?= e(trim(
                ($r['SOT_1'] ?? '') . ' ' .
                ($r['SOT_2'] ?? '') . ' ' .
                ($r['SOT_3'] ?? '')
            )); ?>
        </li>
        <br>
    <?php endforeach; ?>
<?php endif; ?>

<?php if (!empty($direttore)): ?>
    <li><h2>Variazioni direttore</h2></li>
    <?php foreach ($direttore as $r): ?>
        <li><strong>Data variazione: </strong>
            <?= e(formatAnnoV($r['DATA_V'] ?? '', $r['ANNO_V'] ?? '')); ?>
        </li>
        <li><strong>Direttore:</strong>
            <?= e(trim(
                ($r['DIR_1'] ?? '') . ' ' .
                ($r['DIR_2'] ?? '') . ' ' .
                ($r['DIR_3'] ?? '')
            )); ?>
        </li>
        <br>
    <?php endforeach; ?>
<?php endif; ?>

<?php if (!empty($editore)): ?>
    <li><h2>Variazioni editore</h2></li>
    <?php foreach ($editore as $r): ?>
        <li><strong>Data variazione: </strong>
            <?= e(formatAnnoV($r['DATA_V'] ?? '', $r['ANNO_V'] ?? '')); ?>
        </li>
        <li><strong>Editore:</strong>
            <?= e($r['EDITORE'] ?? ''); ?>
        </li>
        <li><strong>Luogo di edizione:</strong>
            <?= e($r['LUOGEDIZ'] ?? ''); ?>
        </li>
        <br>
    <?php endforeach; ?>
<?php endif; ?>

<?php if (!empty($formato)): ?>
    <li><h2>Variazioni formato</h2></li>
    <?php foreach ($formato as $r): ?>
        <li><strong>Data variazione: </strong>
            <?= e(formatAnnoV($r['DATA_V'] ?? '', $r['ANNO_V'] ?? '')); ?>
        </li>
        <li><strong>Altezza:</strong>
            <?= e($r['ALTEZZA'] ?? ''); ?>
        </li>
        <li><strong>Base:</strong>
            <?= e($r['BASE'] ?? ''); ?>
        </li>
        <br>
    <?php endforeach; ?>
<?php endif; ?>

<?php if (!empty($pagine)): ?>
    <li><h2>Variazioni numero di pagine</h2></li>
    <?php foreach ($pagine as $r): ?>
        <li><strong>Data variazione: </strong>
            <?= e(formatAnnoV($r['DATA_V'] ?? '', $r['ANNO_V'] ?? '')); ?>
        </li>
        <li><strong>Pagine:</strong>
            <?= e($r['PAGINE'] ?? ''); ?>
        </li>
        <li><strong>Pagine arabe:</strong>
            <?= e($r['PAGINE_A'] ?? ''); ?>
        </li>
        <br>
    <?php endforeach; ?>
<?php endif; ?>

<?php if (!empty($periodicita)): ?>
    <li><h2>Variazioni della frequenza di pubblicazione</h2></li>
    <?php foreach ($periodicita as $r): ?>
        <li><strong>Data variazione: </strong>
            <?= e(formatAnnoV($r['DATA_V'] ?? '', $r['ANNO_V'] ?? '')); ?>
        </li>
        <li><strong>Frequenza:</strong>
            <?= e($r['periodicita_descr'] ?? ''); ?>
        </li>
        <br>
    <?php endforeach; ?>
<?php endif; ?>

<?php if (!empty($prezzo)): ?>
    <li><h2>Variazioni del prezzo</h2></li>
    <?php foreach ($prezzo as $r): ?>
        <li><strong>Data variazione: </strong>
            <?= e(formatAnnoV($r['DATA_V'] ?? '', $r['ANNO_V'] ?? '')); ?>
        </li>
        <li><strong>Prezzo:</strong>
            <?= e($r['PREZZO'] ?? ''); ?>
        </li>
        <li><strong>Moneta:</strong>
            <?= e($r['moneta_descr'] ?? ''); ?>
        </li>
        <br>
    <?php endforeach; ?>
<?php endif; ?>

<?php if (!empty($tipografia)): ?>
    <li><h2>Variazioni della tipografia</h2></li>
    <?php foreach ($tipografia as $r): ?>
        <li><strong>Data variazione: </strong>
            <?= e(formatAnnoV($r['DATA_V'] ?? '', $r['ANNO_V'] ?? '')); ?>
        </li>
        <li><strong>Tipografo:</strong>
            <?= e($r['TIPOGRAFO'] ?? ''); ?>
        </li>
        <li><strong>Luogo stampa:</strong>
            <?= e($r['LUOGSTAMPA'] ?? ''); ?>
        </li>
        <br>
    <?php endforeach; ?>
<?php endif; ?>

<?php if (!empty($tiratura)): ?>
    <li><h2>Variazioni della tiratura</h2></li>
    <?php foreach ($tiratura as $r): ?>
        <li><strong>Data variazione: </strong>
            <?= e(formatAnnoDaData($r['DATA'] ?? '')); ?>
        </li>
        <li><strong>Tiratura:</strong>
            <?= e($r['TIRATURA'] ?? ''); ?>
        </li>
        <br>
    <?php endforeach; ?>
<?php endif; ?>
