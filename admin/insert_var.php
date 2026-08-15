<?php
declare(strict_types=1);

session_start();
if (!isset($_SESSION['username']) || $_SESSION['username'] !== 'admin') {
    header("Location:messages.php?id=2");
    exit;
}

require_once('header.php');
require_once("../scripts/dbpdo.php");

// -------------------------
// Helper
// -------------------------
function post_str(string $key, int $maxLen = 1000): string {
    $v = trim((string)($_POST[$key] ?? ''));
    if ($maxLen > 0 && mb_strlen($v) > $maxLen) {
        $v = mb_substr($v, 0, $maxLen);
    }
    return $v;
}

function post_int(string $key): ?int {
    $v = trim((string)($_POST[$key] ?? ''));
    if ($v === '' || !preg_match('/^\d+$/', $v)) return null;
    return (int)$v;
}

// -------------------------
// Input base
// -------------------------
$t = isset($_POST['t']) ? (int)$_POST['t'] : 0;
$scheda = post_int('s');
$datav  = post_str('datav', 5);
$annov  = post_str('annov', 11);

if ($t < 1 || $t > 10 || $scheda === null) {
    header("Location:messages.php?id=5");
    exit;
}

// anno solo YYYY
if (!preg_match('/^\d{4}$/', $annov)) {
    header("Location:messages.php?id=5");
    exit;
}

// DATA_V max 5 già; controllo soft
if ($datav !== '' && !preg_match('/^[0-9\/\.\-]{1,5}$/', $datav)) {
    header("Location:messages.php?id=5");
    exit;
}

// -------------------------
// Mappa tabelle/campi (MONETA = CHAR(1))
// -------------------------
$map = [
    1  => ['table'=>'VARIA_TI','fields'=>[
        ['col'=>'TIT_1','post'=>'tit_1','type'=>'str','max'=>200],
        ['col'=>'TIT_2','post'=>'tit_2','type'=>'str','max'=>200],
    ]],
    2  => ['table'=>'VARIA_SO','fields'=>[
        ['col'=>'SOT_1','post'=>'sot_1','type'=>'str','max'=>200],
        ['col'=>'SOT_2','post'=>'sot_2','type'=>'str','max'=>200],
        ['col'=>'SOT_3','post'=>'sot_3','type'=>'str','max'=>200],
    ]],
    3  => ['table'=>'VARIA_DI','fields'=>[
        ['col'=>'DIR_1','post'=>'dir_1','type'=>'str','max'=>200],
        ['col'=>'DIR_2','post'=>'dir_2','type'=>'str','max'=>30],
        ['col'=>'DIR_3','post'=>'dir_3','type'=>'str','max'=>30],
    ]],
    4  => ['table'=>'VARIA_ED','fields'=>[
        ['col'=>'EDITORE','post'=>'editore','type'=>'str','max'=>200],
        ['col'=>'LUOGEDIZ','post'=>'luogo_ediz','type'=>'str','max'=>200],
    ]],
    5  => ['table'=>'VARIA_FO','fields'=>[
        ['col'=>'ALTEZZA','post'=>'altezza','type'=>'str','max'=>20],
        ['col'=>'BASE','post'=>'base','type'=>'str','max'=>20],
    ]],
    6  => ['table'=>'VARIA_PA','fields'=>[
        ['col'=>'PAGINE','post'=>'pagine','type'=>'str','max'=>20],
        ['col'=>'PAGINE_A','post'=>'pagine_a','type'=>'str','max'=>20],
    ]],
    7  => ['table'=>'VARIA_PE','fields'=>[
        ['col'=>'PERIODIC','post'=>'periodic','type'=>'str','max'=>20],
    ]],
    8  => ['table'=>'VARIA_PR','fields'=>[
        ['col'=>'PREZZO','post'=>'prezzo','type'=>'str','max'=>30],
        // MONETA è CHAR(1): max 1 + validazione sotto
        ['col'=>'MONETA','post'=>'moneta','type'=>'char1','max'=>1],
    ]],
    9  => ['table'=>'VARIA_TP','fields'=>[
        ['col'=>'TIPOGRAFO','post'=>'tipografia','type'=>'str','max'=>200],
        ['col'=>'LUOGSTAMPA','post'=>'luogo_stampa','type'=>'str','max'=>200],
    ]],
    10 => ['table'=>'VARIA_TR','fields'=>[
        ['col'=>'TIRATURA','post'=>'tiratura','type'=>'str','max'=>30],
    ]],
];

if (!isset($map[$t])) {
    header("Location:messages.php?id=5");
    exit;
}

$table  = $map[$t]['table'];
$fields = $map[$t]['fields'];

// Validazione specifica moneta (solo per t=8)
if ($t === 8) {
    $moneta = post_str('moneta', 1);
    // CHAR(1): esattamente 1 carattere (può essere lettera o cifra)
    if ($moneta === '' || mb_strlen($moneta) !== 1) {
        header("Location:messages.php?id=5");
        exit;
    }
}

// -------------------------
// SQL generico
// -------------------------
$cols   = ['SCHEDA','DATA_V','ANNO_V'];
$params = [':scheda',':datav',':annov'];

foreach ($fields as $f) {
    $cols[] = $f['col'];
    $params[] = ':' . strtolower($f['col']);
}

$sql = "INSERT INTO {$table} (" . implode(', ', $cols) . ")
        VALUES (" . implode(', ', $params) . ")";

$stmt = $pdo->prepare($sql);

// Bind comuni
$stmt->bindValue(':scheda', $scheda, PDO::PARAM_INT);
$stmt->bindValue(':datav',  $datav,  PDO::PARAM_STR);
$stmt->bindValue(':annov',  $annov,  PDO::PARAM_STR);

// Bind specifici
foreach ($fields as $f) {
    $param = ':' . strtolower($f['col']);

    if ($f['type'] === 'char1') {
        $value = post_str($f['post'], 1);
        $stmt->bindValue($param, $value, PDO::PARAM_STR);
    } else {
        $value = post_str($f['post'], (int)$f['max']);
        $stmt->bindValue($param, $value, PDO::PARAM_STR);
    }
}

try {
    $ok = $stmt->execute();
    header("Location:messages.php?id=" . ($ok ? 6 : 5));
} catch (Throwable $e) {
    // error_log($e->getMessage());
    header("Location:messages.php?id=5");
}
exit;
