<?php
session_start();

if (($_SESSION['username'] ?? '') !== 'admin') {
    header("Location: messages.php?id=2");
    exit;
}

require_once('header.php');
require_once("../scripts/dbpdo.php"); // Connessione al DB

$tipo = $_POST['t'] ?? '';

try {
    // --- INTEGRAZIONE: AGGIORNAMENTO BIOGRAFIA ---
    if ($tipo === 'update_bio') {
        $id   = $_POST['PID'] ?? null;
        $nome = strip_tags($_POST['NOME'] ?? '');
        $bio  = strip_tags($_POST['BIO'] ?? '');

        if ($id) {
            $sql = "UPDATE PERSONA SET NOME = :nome, BIO = :bio WHERE PID = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':nome' => $nome,
                ':bio'  => $bio,
                ':id'   => $id
            ]);
            echo "<p align='center' style='color:green;'>Biografia aggiornata con successo</p>";
        } else {
            echo "<p align='center' style='color:red;'>Errore: ID mancante</p>";
        }
    }

    // --- AGGIORNAMENTO BIBLIOGRAFIA (Ottimizzato) ---
    else if ($tipo === 'bib') {
        $sql = "UPDATE BIBLIO_EST SET 
                    TITOLO = :titolo, 
                    editore = :editore, 
                    luogoEdiz = :luogo, 
                    codTipologia = :tipo, 
                    annoEdiz = :anno, 
                    NOTE = :note 
                WHERE codice = :id";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':titolo'  => strip_tags($_POST['TITOLO']),
            ':editore' => $_POST['editore'],
            ':luogo'   => $_POST['luogoEdiz'],
            ':tipo'    => $_POST['tipologia'],
            ':anno'    => $_POST['annoEdiz'],
            ':note'    => strip_tags($_POST['NOTE']),
            ':id'      => $_POST['ID']
        ]);
        echo "<p align='center'>Bibliografia aggiornata con successo</p>";
    }

    // --- AGGIORNAMENTO PERIODICI (Ottimizzato) ---
    else if ($tipo === 'per') {
        // ... (Recupero variabili POST come già presente nel tuo file)
        $aggiorna = "UPDATE ARCHIVIO SET 
                        TITOLO = :titolo, SOTTOTIT_1 = :s1, SOTTOTIT_2 = :s2, SOTTOTIT_3 = :s3,
                        ALTEZZA = :alt, BASE = :base, PAGINE = :pag, PAGINE_ROM = :pag_r,
                        GENERE = :gen, PERIODIC = :per, MONETA = :mon, TIRATURA = :tir,
                        NELLANNO = :anno, ESTENSIONE = :est, SUPPLEM = :sup, URL = :url,
                        INDICE = :ind, EDITORE = :edi, LUOGO_EDIZ = :led, TIPOGRAFIA = :tip,
                        LUOGO_STAM = :lst, CONTINUA = :con, INIPRI = :ipr, FINEPRI = :fpr,
                        NOTE = :not, ARCHIVIO = :arc, REPERTORIO = :rep, FONDOPRIV = :fon,
                        MICROFILM = :mic, PROVINCIA = :pro, LINGUA = :lin, BIBLIOPRI = :bpr
                    WHERE SCHEDA = :id";
        
        $stmt = $pdo->prepare($aggiorna);
        $stmt->execute([
            ':titolo' => $_POST['titolo'], ':s1' => $_POST['sottoti1'], ':s2' => $_POST['sottoti2'], ':s3' => $_POST['sottoti3'],
            ':alt' => $_POST['altezza'], ':base' => $_POST['base'], ':pag' => $_POST['pagine'], ':pag_r' => $_POST['pagine_rom'],
            ':gen' => $_POST['genere'], ':per' => $_POST['periodic'], ':mon' => $_POST['moneta'], ':tir' => $_POST['tiratura'],
            ':anno' => $_POST['anno'], ':est' => $_POST['estensione'], ':sup' => $_POST['supplem'], ':url' => $_POST['url'],
            ':ind' => $_POST['indice'], ':edi' => $_POST['editore'], ':led' => $_POST['luogo_ediz'], ':tip' => $_POST['tipografia'],
            ':lst' => $_POST['luogo_stam'], ':con' => $_POST['continua'], ':ipr' => $_POST['inipri'], ':fpr' => $_POST['finepri'],
            ':not' => $_POST['note'], ':arc' => $_POST['archivio'], ':rep' => $_POST['repertorio'], ':fon' => $_POST['fondopriv'],
            ':mic' => $_POST['microfilm'], ':pro' => $_POST['provincia'], ':lin' => $_POST['lingua'], ':bpr' => $_POST['bibliopri'],
            ':id' => $_POST['ID']
        ]);

        // Note aggiuntive
        $res_note = "UPDATE MEMO2003 SET MEMO = :memo WHERE SCHEDA = :id";
        $pdo->prepare($res_note)->execute([':memo' => $_POST['noteagg'], ':id' => $_POST['ID']]);

        echo "<p align='center'>Scheda periodici aggiornata correttamente</p>";
    }

} catch (PDOException $e) {
    echo "<p style='color:red;'>Errore durante l'aggiornamento: " . $e->getMessage() . "</p>";
}

echo "<p align='center'><a href='index.php'>Torna al menù</a></p>";
require_once('../html/footer.html');
?>