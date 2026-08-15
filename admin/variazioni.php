<?php
declare(strict_types=1);
include ("../scripts/dbpdo.php");

function e(string $v): string {
    return htmlspecialchars($v, ENT_QUOTES, 'UTF-8');
}

// PERIOD: colonna letterale con punto
$periodi = [];
$stmtP = $pdo->query("SELECT CODICE, `pe.DESCRIZION` AS DESCR FROM PERIOD ORDER BY `pe.DESCRIZION`");
if ($stmtP) $periodi = $stmtP->fetchAll(PDO::FETCH_ASSOC);

// MONETA: colonna letterale con punto
$monete = [];
$stmtM = $pdo->query("SELECT CODICE, `m.DESCRIZION` AS DESCR FROM MONETA ORDER BY `m.DESCRIZION`");
if ($stmtM) $monete = $stmtM->fetchAll(PDO::FETCH_ASSOC);

// Config form
$forms = [
    1 => ['label'=>'Titolo','title'=>"Variazioni del Titolo",'fields'=>[
        ['Inserisci il primo titolo','tit_1','text',200,''],
        ['Inserisci il secondo titolo','tit_2','text',200,''],
    ]],
    2 => ['label'=>'Sottotitolo','title'=>"Variazioni del Sottotitolo",'fields'=>[
        ['Inserisci il primo sottotitolo','sot_1','text',200,''],
        ['Inserisci il secondo sottotitolo','sot_2','text',200,''],
        ['Inserisci il terzo sottotitolo','sot_3','text',200,''],
    ]],
    3 => ['label'=>'Direttore','title'=>"Variazioni del Direttore",'fields'=>[
        ['Inserisci il primo direttore','dir_1','text',200,''],
        ['Inserisci il secondo direttore','dir_2','text',30,''],
        ['Inserisci il terzo direttore','dir_3','text',30,''],
    ]],
    4 => ['label'=>'Editore e luogo di edizione','title'=>"Variazioni dell\'Editore e luogo di Edizione",'fields'=>[
        ["Inserisci l'editore",'editore','text',200,''],
        ["Inserisci il luogo di edizione",'luogo_ediz','text',200,''],
    ]],
    5 => ['label'=>'Formato','title'=>"Variazioni del Formato",'fields'=>[
        ["Inserisci l'altezza",'altezza','text',20,''],
        ["Inserisci la base",'base','text',20,''],
    ]],
    6 => ['label'=>'Numero di pagine','title'=>"Variazioni del Numero di pagine",'fields'=>[
        ["Inserisci il numero di pagine",'pagine','text',20,''],
        ["Inserisci il numero di pagine arabe",'pagine_a','text',20,''],
    ]],
    7 => ['label'=>'Frequenza','title'=>"Variazioni della frequenza di pubblicazione (Periodicità)",'fields'=>[
        ["Seleziona la frequenza di pubblicazione",'periodic','select_period',0,''],
    ]],
    8 => ['label'=>'Prezzo','title'=>"Variazioni del Prezzo",'fields'=>[
        ["Inserisci prezzo",'prezzo','text',30,''],
        ["Seleziona la moneta",'moneta','select_moneta',0,''],
    ]],
    9 => ['label'=>'Tipografia','title'=>"Variazioni della tipografia e del luogo di stampa",'fields'=>[
        ["Inserisci la tipografia",'tipografia','text',200,''],
        ["Inserisci il luogo di stampa",'luogo_stampa','text',200,''],
    ]],
    10 => ['label'=>'Tiratura','title'=>"Variazioni della tiratura di stampa",'fields'=>[
        ["Inserisci la tiratura",'tiratura','text',30,''],
    ]],
];

function render_common_fields(): void { ?>
  <p>
    Inserisci il numero della scheda:
    <input type="number" name="s" min="1" step="1" required><br>

    Inserisci mese e giorno della variazione:
    <input type="text" name="datav" maxlength="5" placeholder="gg/mm"><br>

    Inserisci l'anno della variazione:
    <input type="text" name="annov" maxlength="4" pattern="\d{4}" placeholder="YYYY" required><br>
<?php } ?>

<p>Clicca sul tipo di variazione che vuoi inserire</p>

<div class="buttons" id="var-buttons">
<?php
$first = true;
foreach ($forms as $t => $cfg) {
    if (!$first) echo " -\n";
    $first = false;
    echo '<a href="#" class="showSingle" data-target="' . (int)$t . '">' . e($cfg['label']) . "</a>\n";
}
?>
</div>

<?php foreach ($forms as $t => $cfg): ?>
<div id="div<?= (int)$t ?>" class="targetDiv" style="display:none;">
  <h4><?= e($cfg['title']) ?></h4>
  <form method="POST" action="insert_var.php">
    <?php render_common_fields(); ?>

    <?php foreach ($cfg['fields'] as $f):
      [$label, $name, $type, $max, $ph] = $f;

      if ($type === 'select_period'): ?>
        <?= e($label) ?>:
        <select name="<?= e($name) ?>">
          <?php foreach ($periodi as $p): ?>
            <option value="<?= e((string)$p['CODICE']) ?>"><?= e((string)$p['DESCR']) ?></option>
          <?php endforeach; ?>
        </select><br>

      <?php elseif ($type === 'select_moneta'): ?>
        <?= e($label) ?>:
        <select name="<?= e($name) ?>">
          <?php foreach ($monete as $m): ?>
            <!-- QUI: value=CODICE (CHAR(1)), testo=descrizione -->
            <option value="<?= e((string)$m['CODICE']) ?>"><?= e((string)$m['DESCR']) ?></option>
          <?php endforeach; ?>
        </select><br>

      <?php else: ?>
        <?= e($label) ?>:
        <input
          type="<?= e($type) ?>"
          name="<?= e($name) ?>"
          <?= $max > 0 ? 'maxlength="' . (int)$max . '"' : '' ?>
          <?= $ph !== '' ? 'placeholder="' . e($ph) . '"' : '' ?>
        ><br>
      <?php endif; ?>
    <?php endforeach; ?>

    </p>
    <input type="hidden" name="t" value="<?= (int)$t ?>">
    <input type="submit" value="Inserisci">
  </form>
</div>
<?php endforeach; ?>

<script>
(function () {
  function hideAll() {
    document.querySelectorAll('.targetDiv').forEach(function (el) {
      el.style.display = 'none';
    });
  }

  document.querySelectorAll('.showSingle').forEach(function (a) {
    a.addEventListener('click', function (ev) {
      ev.preventDefault();
      var t = a.getAttribute('data-target');
      hideAll();
      var div = document.getElementById('div' + t);
      if (div) div.style.display = 'block';
    });
  });

  var first = document.getElementById('div1');
  if (first) first.style.display = 'block';
})();
</script>
