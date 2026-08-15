<?php
declare(strict_types=1);

session_start();
if (!isset($_SESSION['username']) || $_SESSION['username'] !== 'admin') {
    header("Location:messages.php?id=2");
    exit;
} else {

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

$messaggio = "";

// -----------------------------------------------------------------------------
// LOGICA DI SALVATAGGIO (solo se premuto il pulsante)
// -----------------------------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['crea'])) {
    try {
        $sql = "INSERT INTO ARCHIVIO (
            TITOLO, SOTTOTIT_1, SOTTOTIT_2, SOTTOTIT_3, RIF_PREC, RIF_SUCC,
            ANNO_FOND, ANNO_CESS, ALTEZZA, BASE, PAGINE, PAGINE_ROM,
            GENERE, PERIODIC, PREZZO, MONETA, TIRATURA, NELLANNO,
            ESTENSIONE, LINGUA, SUPPLEM, INDICE, URL, EDITORE,
            LUOGO_EDIZ, PROVINCIA, TIPOGRAFIA, LUOGO_STAM, CONTINUA,
            BIBLIOPRI, INIPRI, FINEPRI, NOTE,
            ARCHIVIO, REPERTORIO, FONDOPRIV,
            MICROFILM, ardig, cataloghi_online,
            bibliografia
        ) VALUES (
            :titolo, :sotto1, :sotto2, :sotto3, :rif_prec, :rif_succ,
            :anno_f, :anno_c, :alt, :base, :pag, :pag_r,
            :genere, :period, :prezzo, :moneta, :tiratura, :nellanno,
            :estensione, :lingua, :supplem, :indice, :url, :editore,
            :luogo_e, :prov, :tipo, :luogo_s, :continua,
            :bib_pri, :ini_pri, :fine_pri, :note,
            :archivio, :reper, :fondo,
            :micro, :ardig, :cat_on,
            :bibliografia
        )";

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
            ':bibliografia' => $_POST['bibliografia'] ?? ''
        ]);

        header("Location:messages.php?id=" . ($ok ? 6 : 5));
        exit;

    } catch (Throwable $e) {
        // Se vuoi debug: error_log($e->getMessage());
        header("Location:messages.php?id=5");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Nuova Scheda Periodici</title>
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
        button { background: #28a745; color: white; padding: 15px; border: none; border-radius: 4px; cursor: pointer; font-size: 16px; width: 100%; margin-top: 10px; }
        .success { background: #d4edda; color: #155724; padding: 15px; margin-bottom: 20px; border-left: 5px solid #28a745; }
        .error { background: #f8d7da; color: #721c24; padding: 15px; margin-bottom: 20px; border-left: 5px solid #dc3545; }
        .button { display:inline-block; margin-top: 12px; padding: 10px 14px; background:#6c757d; color:#fff; border-radius:4px; text-decoration:none; }
    </style>
</head>
<body>

<div class="container">
    <h1>Nuova Scheda Archivio</h1>
    <?= $messaggio ?>

    <form method="POST">

        <div class="section">
            <div class="section-title">Testata e Relazioni</div>
            <div class="grid">
                <div class="form-group full">
                    <label>Titolo Principale:</label>
                    <input type="text" name="TITOLO" required>
                </div>
                <div class="form-group half">
                    <label>Sottotitoli (Organo Ufficiale, ecc.):</label>
                    <div style="display:flex; gap:5px;">
                        <input type="text" name="SOTTOTIT_1" style="flex:1" placeholder="1">
                        <input type="text" name="SOTTOTIT_2" style="flex:1" placeholder="2">
                        <input type="text" name="SOTTOTIT_3" style="flex:1" placeholder="3">
                    </div>
                </div>
                <div class="form-group">
                    <label>Scheda Prec. / Succ. (ID):</label>
                    <div style="display:flex; gap:5px;">
                        <input type="text" name="RIF_PREC" style="flex:1" placeholder="Prec.">
                        <input type="text" name="RIF_SUCC" style="flex:1" placeholder="Succ.">
                    </div>
                </div>
            </div>
        </div>

        <div class="section">
            <div class="section-title">Dati Cronologici e Fisici</div>
            <div class="grid">
                <div class="form-group"><label>Anno Fondazione:</label><input type="text" name="ANNO_FOND"></div>
                <div class="form-group"><label>Anno Cessazione:</label><input type="text" name="ANNO_CESS"></div>
                <div class="form-group"><label>Continua:</label><input type="text" name="CONTINUA"></div>

                <div class="form-group"><label>Altezza (cm):</label><input type="text" name="ALTEZZA"></div>
                <div class="form-group"><label>Base (cm):</label><input type="text" name="BASE"></div>
                <div class="form-group"><label>Pagine (Arabe / Romane):</label>
                    <div style="display:flex; gap:5px;">
                        <input type="text" name="PAGINE" style="flex:1" placeholder="Arabe">
                        <input type="text" name="PAGINE_ROM" style="flex:1" placeholder="Romane">
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
                            <option value="<?= htmlspecialchars((string)$opt['CODICE'], ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars((string)$opt['DESC_BREVE'], ENT_QUOTES, 'UTF-8') ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Periodicità:</label>
                    <select name="PERIODIC">
                        <option value="">-- Seleziona --</option>
                        <?php foreach(getOptions($pdo, 'PERIOD', 'CODICE', '`pe.DESCRIZION`') as $opt): ?>
                            <option value="<?= htmlspecialchars((string)$opt['CODICE'], ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars((string)$opt['pe.DESCRIZION'], ENT_QUOTES, 'UTF-8') ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Area Diffusione:</label>
                    <select name="ESTENSIONE">
                        <option value="">-- Seleziona --</option>
                        <?php foreach(getOptions($pdo, 'ESTENS', 'CODICE', '`es.DESCRIZION`') as $opt): ?>
                            <option value="<?= htmlspecialchars((string)$opt['CODICE'], ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars((string)$opt['es.DESCRIZION'], ENT_QUOTES, 'UTF-8') ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group"><label>Prezzo:</label><input type="text" name="PREZZO"></div>
                <div class="form-group">
                    <label>Moneta:</label>
                    <select name="MONETA">
                        <?php foreach(getOptions($pdo, 'MONETA', 'CODICE', '`m.DESCRIZION`') as $opt): ?>
                            <option value="<?= htmlspecialchars((string)$opt['CODICE'], ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars((string)$opt['m.DESCRIZION'], ENT_QUOTES, 'UTF-8') ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group"><label>Tiratura (Anno):</label>
                    <div style="display:flex; gap:5px;">
                        <input type="text" name="TIRATURA" style="flex:1" placeholder="Num.">
                        <input type="text" name="NELLANNO" style="flex:1" placeholder="Anno">
                    </div>
                </div>
            </div>
        </div>

        <div class="section">
            <div class="section-title">Produzione e Digitale</div>
            <div class="grid">
                <div class="form-group"><label>Editore:</label><input type="text" name="EDITORE"></div>
                <div class="form-group"><label>Luogo Ediz.:</label><input type="text" name="LUOGO_EDIZ"></div>
                <div class="form-group">
                    <label>Provincia:</label>
                    <select name="PROVINCIA">
                        <option value="">-- Seleziona --</option>
                        <?php foreach(getOptions($pdo, 'PROV', 'PROV', 'NOMEPROV') as $opt): ?>
                            <option value="<?= htmlspecialchars((string)$opt['PROV'], ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars((string)$opt['NOMEPROV'], ENT_QUOTES, 'UTF-8') ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group"><label>Tipografia:</label><input type="text" name="TIPOGRAFIA"></div>
                <div class="form-group"><label>Luogo Stampa:</label><input type="text" name="LUOGO_STAM"></div>
                <div class="form-group">
                    <label>Lingua:</label>
                    <select name="LINGUA">
                        <?php foreach(getOptions($pdo, 'LINGUA', 'CODICE', 'LDESCRIZION') as $opt): ?>
                            <option value="<?= htmlspecialchars((string)$opt['CODICE'], ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars((string)$opt['LDESCRIZION'], ENT_QUOTES, 'UTF-8') ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group full"><label>URL Sito:</label><input type="text" name="URL"></div>
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
                            <option value="<?= htmlspecialchars((string)$opt['CODICE'], ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars((string)$opt['NOME'], ENT_QUOTES, 'UTF-8') ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group"><label>Inizio Posseduto:</label><input type="text" name="INIPRI" placeholder="es. 1867"></div>
                <div class="form-group"><label>Fine Posseduto:</label><input type="text" name="FINEPRI" placeholder="es. 1900"></div>
            </div>
        </div>

        <div class="section">
            <div class="section-title">Note e Repertori</div>
            <div class="grid">
                <div class="form-group"><label>Supplementi:</label><input type="text" name="SUPPLEM"></div>
                <div class="form-group"><label>Indice:</label><input type="text" name="INDICE"></div>

                <div class="form-group"><label>Archivio:</label><input type="text" name="ARCHIVIO"></div>

                <div class="form-group"><label>Repertorio:</label><input type="text" name="REPERTORIO"></div>
                <div class="form-group"><label>Fondo Privato:</label><input type="text" name="FONDOPRIV"></div>
                <div class="form-group"><label>Microfilm / Scans.:</label><input type="text" name="MICROFILM"></div>
                <div class="form-group"><label>Archivi Digitali:</label><input type="text" name="ardig"></div>

                <div class="form-group full"><label>Cataloghi Online:</label><input type="text" name="cataloghi_online"></div>

                <div class="form-group full"><label>Bibliografia:</label><textarea name="bibliografia" rows="2"></textarea></div>

                <div class="form-group full"><label>Note aggiuntive:</label><textarea name="NOTE" rows="2"></textarea></div>
            </div>
        </div>

        <button type="submit" name="crea" value="1">Crea Nuova Scheda</button>
    </form>

    <a href="index.php" class="button">Torna al menù principale</a>
</div>

</body>
</html>

<?php } ?>
