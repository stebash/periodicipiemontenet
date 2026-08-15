<?php 
include("dbpdo.php");

// recupero tutte le notizie
$stmt = $pdo->query("SELECT id, titolo, testo FROM notizie ORDER BY id DESC");
$notizie = $stmt->fetchAll(PDO::FETCH_ASSOC);

function conta(PDO $pdo, string $sql, array $params = []): int {
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return (int)$stmt->fetchColumn();
}

$total_notizie = conta($pdo, "SELECT COUNT(*) FROM notizie");
?>

<h1>Notizie ed eventi</h1>

<p>Totale notizie: <?= $total_notizie ?></p>

<ul>
<?php foreach ($notizie as $n): ?>
    <li>
        <a href="#" 
           class="notizia-link" 
           data-id="<?= $n['id'] ?>"
           data-titolo="<?= htmlspecialchars($n['titolo'], ENT_QUOTES, 'UTF-8') ?>"
           data-testo="<?= htmlspecialchars($n['testo'], ENT_QUOTES, 'UTF-8') ?>">
           <?= htmlspecialchars($n['titolo'], ENT_QUOTES, 'UTF-8') ?>
        </a>
    </li>
<?php endforeach; ?>
</ul>

<hr>

<div id="notizia_contenuto" style="display:none;">
    <h2 id="notizia_titolo"></h2>
    <p id="notizia_testo"></p>
</div>

<script>
// gestione click sui titoli
document.querySelectorAll('.notizia-link').forEach(link => {
    link.addEventListener('click', function(e) {
        e.preventDefault();

        const titolo = this.dataset.titolo;
        const testo  = this.dataset.testo.replace(/\n/g, "<br>");

        // aggiorna contenuto
        document.getElementById("notizia_titolo").innerHTML = titolo;
        document.getElementById("notizia_testo").innerHTML = testo;

        // mostra la sezione
        document.getElementById("notizia_contenuto").style.display = "block";
    });
});
</script>
