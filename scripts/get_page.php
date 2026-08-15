<?php 
// 1. RECUPERO SICURO DEL PARAMETRO
// Se 'c' esiste lo usiamo, altrimenti usiamo una stringa vuota.
// Questo previene errori se l'utente apre il sito senza parametri.
$pagina = isset($_GET['c']) ? $_GET['c'] : '';

// 2. GESTIONE DEI CONTENUTI (ROUTING)
switch ($pagina) {

    // --- HOME PAGE (Caso vuoto o 'home') ---
    case '':        // Se non c'è parametro nella URL
    case 'home':    // Se c'è ?c=home
        $content = 'html/home.html';
        break;

    // --- VOCI PROGETTO ---
    case 'origini':          $content = 'html/origini.html'; break;
    case 'collaboratori':    $content = 'html/collaboratori.html'; break;
    case 'come-partecipare': $content = 'html/come-partecipare.html'; break;
    

    // --- VOCI PERIODICI ---
    case 'storia':     $content = 'html/storia.html'; break;
    case 'cronologia': $content = 'html/cronologia.html'; break;
    
    // Questo punta a cerca.php, lo gestisco qui o nel gruppo sotto?
    // Nel tuo originale era qui, ma puntava allo script di ricerca.
    case 'anno-fondazione': 
        $content = 'scripts/cerca.php'; break;

    // --- VOCI BIBLIOGRAFIE (Tutte puntano a cerca.php) ---
    // Nota come possiamo mettere tutti i 'case' uno sotto l'altro senza 'break'
    // per dire "se è uno qualunque di questi, fai la stessa cosa".
    case 'monografie':
    case 'opere-collettive':
    case 'tesi':
    case 'manoscritti':
    case 'opuscoli':
    case 'estratti':
    case 'spogli-bibliografici':
    case 'citazioni':
    case 'cataloghi-repertori':
    case 'articoli-saggi':
        $content = 'scripts/cerca.php';
        break;

    // --- PAGINE STATICHE VARIE ---
    case 'acronimi': $content = 'html/acronimi.html'; break;
    case 'media':    $content = 'html/media.html'; break;
    case 'contatti': $content = 'html/contatti.html'; break;

    // --- BARRE LATERALI (Puntano a cerca.php) ---
    case 'genere':
    case 'provincia':
    case 'testate-in-vita':
    case 'biblioteca':
    case 'cerca-biblioteca':
        $content = "scripts/cerca.php";
        break;

    // --- SCRIPT SPECIFICI ---
    case 'scheda':       $content = "scripts/scheda.php"; break;
    case 'bibliografia': $content = "scripts/inc_bib.php"; break;
    case 'biografia':    $content = "scripts/inc_bio.php"; break;
    case 'cerca':        $content = 'scripts/cerca-stringa.php'; break;
    case 'persona':      $content = 'scripts/persona.php'; break;

    // --- PAGINE DI RICERCA (Form) ---
    case 'ricerca-periodico':
    case 'ricerca-bibliografie':
    case 'ricerca-biografie':
    case 'ricerca-anno-fondazione':
        $content = 'scripts/form_ricerca.php';
        break;

    // --- AREA RISERVATA ---
    case 'login':  $content = 'admin/login.php'; break;
    case 'admin':  $content = 'admin/admin.php'; break;
    case 'logout': $content = 'admin/logout.php'; break;

    // --- DEFAULT (SICUREZZA) ---
    // Se qualcuno scrive una url a caso (es: ?c=pippo), mandalo alla home
    default:
        $content = 'html/home.html';
        break;
}
?>