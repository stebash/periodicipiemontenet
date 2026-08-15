<?php
session_start();

// 1. Controllo Autorizzazione
if (($_SESSION['username'] ?? '') !== 'admin') {
    header("Location: messages.php?id=2");
    exit;
}

require_once("../scripts/dbpdo.php");
require_once('header.php');

// 2. Recupero ID: cerca prima in POST (dal form), poi in GET (da un link)
$id = $_POST['s'] ?? $_GET['id'] ?? null;

// 3. Validazione robusta
if ($id === null || trim($id) === '' || !ctype_digit((string)$id)) {
    // Debug opzionale: echo "Valore ricevuto: " . var_export($id, true); 
    die("<div style='color:red; text-align:center; padding:20px;'>
            <h2>Errore: ID Biografia non valido.</h2>
            <p>Il valore ricevuto è: " . htmlspecialchars($id) . "</p>
            <a href='../admin'>Torna indietro</a>
         </div>");
}

// 4. Recupero dati dal database
try {
    $stmt = $pdo->prepare("SELECT * FROM PERSONA WHERE PID = :id");
    $stmt->execute([':id' => $id]);
    $bio = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$bio) {
        die("<div style='text-align:center; padding:20px;'>
                <h2>Errore: Biografia n. $id non trovata nel database.</h2>
                <a href='../admin'>Torna indietro</a>
             </div>");
    }
} catch (PDOException $e) {
    die("Errore database: " . $e->getMessage());
}

// Funzione helper per la sicurezza in output
function e(?string $v): string { 
    return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); 
}
?>

<div class="container" style="max-width: 800px; margin: auto; padding: 20px;">
    <h1>Modifica Scheda Biografica (ID: <?= e($bio['PID']) ?>)</h1>
    
    <form method="POST" action="update.php">
        <input type="hidden" name="t" value="update_bio" />
        <input type="hidden" name="PID" value="<?= e($bio['PID']) ?>" />
        
        <table class="table-form" style="width: 100%; border-spacing: 10px;">
            <tr>
                <td style="width: 150px;"><strong>Nome:</strong></td>
                <td><input type="text" name="NOME" style="width: 100%; padding: 8px;" value="<?= e($bio['NOME']) ?>" required /></td>
            </tr>
            <tr>
                <td style="vertical-align: top;"><strong>Dati biografici:</strong></td>
                <td><textarea name="BIO" rows="12" style="width: 100%; padding: 8px;" required><?= e($bio['BIO']) ?></textarea></td>
            </tr>
        </table>
        
        <div style="margin-top:20px; text-align: center;">
            <input type="submit" value="Salva Modifiche" class="btn-submit" style="padding: 10px 30px; cursor: pointer; background: #28a745; color: white; border: none; border-radius: 4px;" />
            <br><br>
            <a href="../admin">Annulla e torna all'amministrazione</a>
        </div>
    </form>
</div>

<?php require_once('../html/footer.html'); ?>