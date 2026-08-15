<?php
session_start();
require_once("../scripts/dbpdo.php");

// Controllo sicurezza
if (($_SESSION['username'] ?? '') !== 'admin') {
    header("Location: messages.php?id=2");
    exit;
}

$tipo = $_POST['t'] ?? '';

try {
    if ($tipo === 'bio') {
        // Inserimento Biografia (Tabella PERSONA)
        $sql = "INSERT INTO PERSONA (NOME, BIO) VALUES (:nome, :bio)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':nome' => $_POST['PERS'] ?? '',
            ':bio'  => $_POST['RUO']  ?? ''
        ]);
        header("Location: messages.php?id=13");
        
    } elseif ($tipo === 'bib') {
        // 1. Inserimento nella tabella BIBLIO_EST
        $sql = "INSERT INTO BIBLIO_EST (TITOLO, editore, luogoEdiz, codTipologia, annoEdiz, NOTE) 
                VALUES (:titolo, :editore, :luogo, :tipologia, :anno, :note)";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':titolo'    => $_POST['TITOLO'] ?? '',
            ':editore'   => $_POST['editore'] ?? '',
            ':luogo'     => $_POST['luogoEdiz'] ?? '',
            ':tipologia' => $_POST['tipologia'] ?? '',
            ':anno'      => $_POST['annoEdiz'] ?? '',
            ':note'      => $_POST['NOTE'] ?? ''
        ]);

        $newId = $pdo->lastInsertId();

        // 2. Se è stato inserito un autore, creiamo il record nella tabella PERSONE 
        // per permettere alla query di visualizzazione (JOIN) di trovarlo
        if (!empty($_POST['autore']) && $newId) {
            $sqlAutore = "INSERT INTO PERSONE (codice, PERS, RUO) VALUES (:codice, :pers, 'Autore')";
            $stmtA = $pdo->prepare($sqlAutore);
            $stmtA->execute([
                ':codice' => $newId,
                ':pers'   => $_POST['autore']
            ]);
        }

        header("Location: messages.php?id=9");
    } elseif ($tipo === 'pers') {
        // Inserimento Persona/Collaboratore (Tabella PERSONE)
        $sql = "INSERT INTO PERSONE (codice, PERS, RUO) VALUES (:codice, :pers, :ruo)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':codice' => $_POST['scheda'] ?? '',
            ':pers'   => $_POST['cognom'] ?? '',
            ':ruo'    => $_POST['ruolo']  ?? ''
        ]);
        header("Location: messages.php?id=9");
        
    } else {
        header("Location: messages.php?id=5"); // Tipo non riconosciuto
    }
} catch (PDOException $e) {
    // In caso di errore SQL
    header("Location: messages.php?id=5");
}
exit;