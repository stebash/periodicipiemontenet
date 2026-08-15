<?php
declare(strict_types=1);

session_start();
if (!isset($_SESSION['username']) || $_SESSION['username'] !== 'admin') {
    header("Location:messages.php?id=2");
    exit;
}

require_once('header.php');

// -----------------------------------------------------------------------------
// Connessione al DB e Utility
// -----------------------------------------------------------------------------
require_once("../scripts/dbpdo.php");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

function getOptions(PDO $pdo, string $table, string $valueCol, string $descCol): array {
    $stmt = $pdo->query("SELECT $valueCol, $descCol FROM $table ORDER BY $descCol ASC");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
function e($v): string {
    return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');
}

// -----------------------------------------------------------------------------
// Recupero ID scheda
// -----------------------------------------------------------------------------
$id = $_GET['s'] ?? $_POST['s'] ?? null;
if ($id === null || !ctype_digit((string)$id)) {
    header("Location:messages.php?id=5");
    exit;
}

// -----------------------------------------------------------------------------
// UPDATE SOLO SE PREMO "SALVA"
// -----------------------------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['salva'])) {
    try {
        $sql = "UPDATE ARCHIVIO SET
            TITOLO = :titolo,
            SOTTOTIT_1 = :sotto1,
            SOTTOTIT_2 = :sotto2,
            SOTTOTIT_3 = :sotto3,
            RIF_PREC = :rif_prec,
            RIF_SUCC = :rif_succ,

            ANNO_FOND = :anno_f,
            ANNO_CESS = :anno_c,
            ALTEZZA = :alt,
            BASE = :base,
            PAGINE = :pag,
            PAGINE_ROM = :pag_r,

            GENERE = :genere,
            PERIODIC = :period,
            PREZZO = :prezzo,
            MONETA = :moneta,
            TIRATURA = :tiratura,
            NELLANNO = :nellanno,

            ESTENSIONE = :estensione,
            LINGUA = :lingua,
            SUPPLEM = :supplem,
            INDICE = :indice,
            URL = :url,
            EDITORE = :editore,

            LUOGO_EDIZ = :luogo_e,
            PROVINCIA = :prov,
            TIPOGRAFIA = :tipo,
            LUOGO_STAM = :luogo_s,
            CONTINUA = :continua,

            BIBLIOPRI = :bib_pri,
            INIPRI = :ini_pri,
            FINEPRI = :fine_pri,

            NOTE = :note,
            ARCHIVIO = :archivio,
            REPERTORIO = :reper,
            FONDOPRIV = :fondo,
            MICROFILM = :micro,
            ardig = :ardig,
            cataloghi_online = :cat_on,
            bibliografia = :bibliografia
        WHERE SCHEDA = :id";

        $stmt = $pdo->prepare($sql);
        $ok = $stmt->execute([
            ':titolo'       => $_POST['TITOLO'] ?? '',
            ':sotto1'       => $_POST['SOTTOTIT_1'] ?? '',
            ':sotto2'       => $_POST['SOTTOTIT_2'] ?? '',
            ':sotto3'       => $_POST['SOTTOTIT_3'] ?? '',
            ':rif_prec'     => $_POST['RIF_PREC'] ?? '',
            ':rif_succ'     => $_POST['RIF_SUCC'] ?? '',

            ':anno_f'       => $_POST['ANNO_FOND'] ?? '',
            ':anno_c'       => $_POST['ANNO_CESS'] ?? '',
            ':alt'          => $_POST['ALTEZZA'] ?? '',
            ':base'         => $_POST['BASE'] ?? '',
            ':pag'          => $_POST['PAGINE'] ?? '',
            ':pag_r'        => $_POST['PAGINE_ROM'] ?? '',

            ':genere'       => $_POST['GENERE'] ?? '',
            ':period'       => $_POST['PERIODIC'] ?? '',
            ':prezzo'       => $_POST['PREZZO'] ?? '',
            ':moneta'       => $_POST['MONETA'] ?? '',
            ':tiratura'     => $_POST['TIRATURA'] ?? '',
            ':nellanno'     => $_POST['NELLANNO'] ?? '',

            ':estensione'   => $_POST['ESTENSIONE'] ?? '',
            ':lingua'       => $_POST['LINGUA'] ?? '',
            ':supplem'      => $_POST['SUPPLEM'] ?? '',
            ':indice'       => $_POST['INDICE'] ?? '',
            ':url'          => $_POST['URL'] ?? '',
            ':editore'      => $_POST['EDITORE'] ?? '',

            ':luogo_e'      => $_POST['LUOGO_EDIZ'] ?? '',
            ':prov'         => $_POST['PROVINCIA'] ?? '',
            ':tipo'         => $_POST['TIPOGRAFIA'] ?? '',
            ':luogo_s'      => $_POST['LUOGO_STAM'] ?? '',
            ':continua'     => $_POST['CONTINUA'] ?? '',

            ':bib_pri'      => $_POST['BIBLIOPRI'] ?? '',
            ':ini_pri'      => $_POST['INIPRI'] ?? '',
            ':fine_pri'     => $_POST['FINEPRI'] ?? '',

            ':note'         => $_POST['NOTE'] ?? '',
            ':archivio'     => $_POST['ARCHIVIO'] ?? '',
            ':reper'        => $_POST['REPERTORIO'] ?? '',
            ':fondo'        => $_POST['FONDOPRIV'] ?? '',
            ':micro'        => $_POST['MICROFILM'] ?? '',
            ':ardig'        => $_POST['ardig'] ?? '',
            ':cat_on'       => $_POST['cataloghi_online'] ?? '',
            ':bibliografia' => $_POST['bibliografia'] ?? '',
            ':id'           => $id
        ]);

        header("Location:messages.php?id=" . ($ok ? 6 : 5));
        exit;

    } catch (Throwable $e) {
        header("Location:messages.php?id=5");
        exit;
    }
}

// -----------------------------------------------------------------------------
// RECUPERO DATI (sempre)
// -----------------------------------------------------------------------------

$stmt = $pdo->prepare("SELECT * FROM ARCHIVIO WHERE SCHEDA = :id");
$stmt->execute([':id' => $id]);
$dati = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$dati) {
    header("Location:messages.php?id=5");
    exit;
}

// Note aggiuntive
$stmtNoteAgg = $pdo->prepare("
    SELECT *
    FROM MEMO2003
    WHERE SCHEDA = :id
");
$stmtNoteAgg->execute([':id' => $id]);
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Modifica Scheda <?= e($id) ?></title>
    <style>
        body { font-family: sans-serif; background: #f0f2f5; padding: 20px; font-size: 14px; }
        .container { max-width: 1000px; background: #fff; margin: auto; padding: 25px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        .section { background: #f8f9fa; border: 1px solid #dee2e6; padding: 15px; margin-bottom: 20px; border-radius: 4px; }
        .section-title { font-weight: bold; color: #0056b3; border-bottom: 1px solid #0056b3; margin-bottom: 15px; padding-bottom: 5px; text-transform: uppercase; }
        .grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 15px; }
        .full { grid-column: span 3; }
        .half { grid-column: span 2; }
        .form-group { display: flex; flex-direction: column; }
        label { font-weight: bold; margin-bottom: 4px; color: #444; }
        input, select, textarea { padding: 8px; border: 1px solid #ccc; border-radius: 4px; }
        button { background: #007bff; color: white; padding: 15px; border: none; border-radius: 4px; cursor: pointer; font-size: 16px; width: 100%; margin-top: 10px; }
        .button { display:inline-block; margin-top: 12px; padding: 10px 14px; background:#6c757d; color:#fff; border-radius:4px; text-decoration:none; }
    </style>
</head>
<body>

<div class="container">
    <h1>Modifica Scheda Archivio (ID: <?= e($id) ?>)</h1>

    <form method="POST">
        <input type="hidden" name="s" value="<?= e($id) ?>">

        <div class="section">
            <div class="section-title">Testata e Relazioni</div>
            <div class="grid">
                <div class="form-group full">
                    <label>Titolo Principale:</label>
                    <input type="text" name="TITOLO" required value="<?= e($dati['TITOLO']) ?>">
                </div>

                <div class="form-group half">
                    <label>Sottotitoli (Organo Ufficiale, ecc.):</label>
                    <div style="display:flex; gap:5px;">
                        <input type="text" name="SOTTOTIT_1" style="flex:1" placeholder="1" value="<?= e($dati['SOTTOTIT_1']) ?>">
                        <input type="text" name="SOTTOTIT_2" style="flex:1" placeholder="2" value="<?= e($dati['SOTTOTIT_2']) ?>">
                        <input type="text" name="SOTTOTIT_3" style="flex:1" placeholder="3" value="<?= e($dati['SOTTOTIT_3']) ?>">
                    </div>
                </div>

                <div class="form-group">
                    <label>Scheda Prec. / Succ. (ID):</label>
                    <div style="display:flex; gap:5px;">
                        <input type="text" name="RIF_PREC" style="flex:1" placeholder="Prec." value="<?= e($dati['RIF_PREC']) ?>">
                        <input type="text" name="RIF_SUCC" style="flex:1" placeholder="Succ." value="<?= e($dati['RIF_SUCC']) ?>">
                    </div>
                </div>
            </div>
        </div>

        <div class="section">
            <div class="section-title">Dati Cronologici e Fisici</div>
            <div class="grid">
                <div class="form-group"><label>Anno Fondazione:</label><input type="text" name="ANNO_FOND" value="<?= e($dati['ANNO_FOND']) ?>"></div>
                <div class="form-group"><label>Anno Cessazione:</label><input type="text" name="ANNO_CESS" value="<?= e($dati['ANNO_CESS']) ?>"></div>
                <div class="form-group"><label>Continua:</label><input type="text" name="CONTINUA" value="<?= e($dati['CONTINUA']) ?>"></div>

                <div class="form-group"><label>Altezza (cm):</label><input type="text" name="ALTEZZA" value="<?= e($dati['ALTEZZA']) ?>"></div>
                <div class="form-group"><label>Base (cm):</label><input type="text" name="BASE" value="<?= e($dati['BASE']) ?>"></div>

                <div class="form-group"><label>Pagine (Arabe / Romane):</label>
                    <div style="display:flex; gap:5px;">
                        <input type="text" name="PAGINE" style="flex:1" placeholder="Arabe" value="<?= e($dati['PAGINE']) ?>">
                        <input type="text" name="PAGINE_ROM" style="flex:1" placeholder="Romane" value="<?= e($dati['PAGINE_ROM']) ?>">
                    </div>
                </div>
            </div>
        </div>

        <div class="section">
            <div class="section-title">Classificazione e Mercato</div>
            <div class="grid">
                <div class="form-group">
                    <label>Genere:</label>
                    <select name="GENERE">
                        <option value="">-- Seleziona --</option>
                        <?php foreach(getOptions($pdo, 'GENERE', 'CODICE', 'DESC_BREVE') as $opt): ?>
                            <option value="<?= e($opt['CODICE']) ?>" <?= ((string)$dati['GENERE'] === (string)$opt['CODICE']) ? 'selected' : '' ?>>
                                <?= e($opt['DESC_BREVE']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Periodicità:</label>
                    <select name="PERIODIC">
                        <option value="">-- Seleziona --</option>
                        <?php foreach(getOptions($pdo, 'PERIOD', 'CODICE', '`pe.DESCRIZION`') as $opt): ?>
                            <option value="<?= e($opt['CODICE']) ?>" <?= ((string)$dati['PERIODIC'] === (string)$opt['CODICE']) ? 'selected' : '' ?>>
                                <?= e($opt['pe.DESCRIZION']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Area Diffusione:</label>
                    <select name="ESTENSIONE">
                        <option value="">-- Seleziona --</option>
                        <?php foreach(getOptions($pdo, 'ESTENS', 'CODICE', '`es.DESCRIZION`') as $opt): ?>
                            <option value="<?= e($opt['CODICE']) ?>" <?= ((string)$dati['ESTENSIONE'] === (string)$opt['CODICE']) ? 'selected' : '' ?>>
                                <?= e($opt['es.DESCRIZION']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group"><label>Prezzo:</label><input type="text" name="PREZZO" value="<?= e($dati['PREZZO']) ?>"></div>

                <div class="form-group">
                    <label>Moneta:</label>
                    <select name="MONETA">
                        <option value="">-- Seleziona --</option>
                        <?php foreach(getOptions($pdo, 'MONETA', 'CODICE', '`m.DESCRIZION`') as $opt): ?>
                            <option value="<?= e($opt['CODICE']) ?>" <?= ((string)$dati['MONETA'] === (string)$opt['CODICE']) ? 'selected' : '' ?>>
                                <?= e($opt['m.DESCRIZION']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group"><label>Tiratura (Anno):</label>
                    <div style="display:flex; gap:5px;">
                        <input type="text" name="TIRATURA" style="flex:1" placeholder="Num." value="<?= e($dati['TIRATURA']) ?>">
                        <input type="text" name="NELLANNO" style="flex:1" placeholder="Anno" value="<?= e($dati['NELLANNO']) ?>">
                    </div>
                </div>
            </div>
        </div>

        <div class="section">
            <div class="section-title">Produzione e Digitale</div>
            <div class="grid">
                <div class="form-group"><label>Editore:</label><input type="text" name="EDITORE" value="<?= e($dati['EDITORE']) ?>"></div>
                <div class="form-group"><label>Luogo Ediz.:</label><input type="text" name="LUOGO_EDIZ" value="<?= e($dati['LUOGO_EDIZ']) ?>"></div>

                <div class="form-group">
                    <label>Provincia:</label>
                    <select name="PROVINCIA">
                        <option value="">-- Seleziona --</option>
                        <?php foreach(getOptions($pdo, 'PROV', 'PROV', 'NOMEPROV') as $opt): ?>
                            <option value="<?= e($opt['PROV']) ?>" <?= ((string)$dati['PROVINCIA'] === (string)$opt['PROV']) ? 'selected' : '' ?>>
                                <?= e($opt['NOMEPROV']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group"><label>Tipografia:</label><input type="text" name="TIPOGRAFIA" value="<?= e($dati['TIPOGRAFIA']) ?>"></div>
                <div class="form-group"><label>Luogo Stampa:</label><input type="text" name="LUOGO_STAM" value="<?= e($dati['LUOGO_STAM']) ?>"></div>

                <div class="form-group">
                    <label>Lingua:</label>
                    <select name="LINGUA">
                        <option value="">-- Seleziona --</option>
                        <?php foreach(getOptions($pdo, 'LINGUA', 'CODICE', 'LDESCRIZION') as $opt): ?>
                            <option value="<?= e($opt['CODICE']) ?>" <?= ((string)$dati['LINGUA'] === (string)$opt['CODICE']) ? 'selected' : '' ?>>
                                <?= e($opt['LDESCRIZION']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group full"><label>URL Sito:</label><input type="text" name="URL" value="<?= e($dati['URL']) ?>"></div>
            </div>
        </div>

        <div class="section">
            <div class="section-title">Biblioteca Principale e Posseduto</div>
            <div class="grid">
                <div class="form-group">
                    <label>Biblioteca (Codice):</label>
                    <select name="BIBLIOPRI">
                        <option value="">-- Seleziona --</option>
                        <?php foreach(getOptions($pdo, 'BIBLIOT', 'CODICE', 'NOME') as $opt): ?>
                            <option value="<?= e($opt['CODICE']) ?>" <?= ((string)$dati['BIBLIOPRI'] === (string)$opt['CODICE']) ? 'selected' : '' ?>>
                                <?= e($opt['NOME']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group"><label>Inizio Posseduto:</label><input type="text" name="INIPRI" value="<?= e($dati['INIPRI']) ?>"></div>
                <div class="form-group"><label>Fine Posseduto:</label><input type="text" name="FINEPRI" value="<?= e($dati['FINEPRI']) ?>"></div>
            </div>
        </div>

        <div class="section">
            <div class="section-title">Note e Repertori</div>
            <div class="grid">
                <div class="form-group"><label>Supplementi:</label><input type="text" name="SUPPLEM" value="<?= e($dati['SUPPLEM']) ?>"></div>
                <div class="form-group"><label>Indice:</label><input type="text" name="INDICE" value="<?= e($dati['INDICE']) ?>"></div>
                <duv class="form-group"><label>Note:</label><input type="text" name="NOTE" value="<?= e($dati['NOTE']) ?>"></div>        
                <div class="form-group"><label>Archivio:</label><input type="text" name="ARCHIVIO" value="<?= e($dati['ARCHIVIO']) ?>"></div>

                <div class="form-group"><label>Repertorio:</label><input type="text" name="REPERTORIO" value="<?= e($dati['REPERTORIO']) ?>"></div>
                <div class="form-group"><label>Fondo Privato:</label><input type="text" name="FONDOPRIV" value="<?= e($dati['FONDOPRIV']) ?>"></div>
                <div class="form-group"><label>Microfilm / Scans.:</label><input type="text" name="MICROFILM" value="<?= e($dati['MICROFILM']) ?>"></div>
                <div class="form-group"><label>Archivi Digitali:</label><input type="text" name="ardig" value="<?= e($dati['ardig']) ?>"></div>

                <div class="form-group full"><label>Cataloghi Online:</label><input type="text" name="cataloghi_online" value="<?= e($dati['cataloghi_online']) ?>"></div>

                <div class="form-group full"><label>Bibliografia:</label><textarea name="bibliografia" rows="2"><?= e($dati['bibliografia']) ?></textarea></div>

                <div class="form-group full">
    <label>Note aggiuntive:</label>
    <textarea name="nagg" rows="2"><?php 
        while ($na = $stmtNoteAgg->fetch(PDO::FETCH_ASSOC)) {
            // 1. strip_tags rimuove i tag HTML (es. <p>, <b>, ecc.)
            // 2. htmlspecialchars gestisce i caratteri speciali residui per la sicurezza
            echo htmlspecialchars(strip_tags($na['MEMO'] ?? ''));
        } 
    ?></textarea>
</div>
        </div>

        <button type="submit" name="salva" value="1">Salva Modifiche</button>
    </form>

    <a href="index.php" class="button">Torna al menù principale</a>
</div>

</body>
</html>
