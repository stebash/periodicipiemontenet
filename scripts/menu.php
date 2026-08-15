<?php
// pagina corrente (default 'home' se non impostata)
$pagina_corrente = isset($_GET['c']) && $_GET['c'] !== '' ? $_GET['c'] : 'home';

function attivo($pagina_corrente, $voci) {
    $voci = (array)$voci;
    return in_array($pagina_corrente, $voci, true) ? 'active' : '';
}
?>

<nav id="primary_nav_wrap">
    <ul>
        <!-- HOME -->
        <li class="<?= attivo($pagina_corrente, 'home') ?>">
            <a href="index.php?c=home">Home</a>
        </li>

        <!-- PROGETTO -->
        <li class="<?= attivo($pagina_corrente, ['origini','collaboratori','come-partecipare']) ?>">
            <a href="#">Progetto</a>
            <ul>
                <li class="<?= attivo($pagina_corrente, 'origini') ?>">
                    <a href="index.php?c=origini">Origini</a>
                </li>
                <li class="<?= attivo($pagina_corrente, 'collaboratori') ?>">
                    <a href="index.php?c=collaboratori">Collaboratori</a>
                </li>
                <li class="<?= attivo($pagina_corrente, 'come-partecipare') ?>">
                    <a href="index.php?c=come-partecipare">Come partecipare</a>
                </li>

                <!-- LOGIN (link esterno, niente stato active) -->
                <li>
                    <a href="https://www.periodicipiemonte.net/admin/login.php">Login</a>
                </li>
            </ul>
        </li>

        <!-- PERIODICI -->
        <li class="<?= attivo($pagina_corrente, ['storia','cronologia','ricerca-periodico','ricerca-anno-fondazione']) ?>">
            <a href="#">Periodici</a>
            <ul>
                <li class="<?= attivo($pagina_corrente, 'storia') ?>">
                    <a href="index.php?c=storia">Storia dei periodici</a>
                </li>
                <li class="<?= attivo($pagina_corrente, 'cronologia') ?>">
                    <a href="index.php?c=cronologia">Cronologia</a>
                </li>
                <li class="<?= attivo($pagina_corrente, 'ricerca-periodico') ?>">
                    <a href="index.php?c=ricerca-periodico">Ricerca per codice</a>
                </li>
                <li class="<?= attivo($pagina_corrente, 'ricerca-anno-fondazione') ?>">
                    <a href="index.php?c=ricerca-anno-fondazione">Ricerca per anno di fondazione</a>
                </li>
            </ul>
        </li>

        <!-- BIBLIOGRAFIE -->
        <li class="<?= attivo($pagina_corrente, [
                'monografie','opere-collettive','tesi','manoscritti','opuscoli','estratti',
                'spogli-bibliografici','citazioni','cataloghi-repertori','articoli-saggi',
                'ricerca-bibliografie'
        ]) ?>">
            <a href="#">Bibliografie</a>
            <ul>
                <li class="<?= attivo($pagina_corrente, 'monografie') ?>">
                    <a href="index.php?c=monografie">Monografie</a>
                </li>
                <li class="<?= attivo($pagina_corrente, 'opere-collettive') ?>">
                    <a href="index.php?c=opere-collettive">Opere collettive</a>
                </li>
                <li class="<?= attivo($pagina_corrente, 'tesi') ?>">
                    <a href="index.php?c=tesi">Tesi</a>
                </li>
                <li class="<?= attivo($pagina_corrente, 'manoscritti') ?>">
                    <a href="index.php?c=manoscritti">Manoscritti</a>
                </li>
                <li class="<?= attivo($pagina_corrente, 'opuscoli') ?>">
                    <a href="index.php?c=opuscoli">Opuscoli</a>
                </li>
                <li class="<?= attivo($pagina_corrente, 'estratti') ?>">
                    <a href="index.php?c=estratti">Estratti</a>
                </li>
                <li class="<?= attivo($pagina_corrente, 'spogli-bibliografici') ?>">
                    <a href="index.php?c=spogli-bibliografici">Spogli bibliografici</a>
                </li>
                <li class="<?= attivo($pagina_corrente, 'citazioni') ?>">
                    <a href="index.php?c=citazioni">Citazioni</a>
                </li>
                <li class="<?= attivo($pagina_corrente, 'cataloghi-repertori') ?>">
                    <a href="index.php?c=cataloghi-repertori">Cataloghi e repertori</a>
                </li>
                <li class="<?= attivo($pagina_corrente, 'articoli-saggi') ?>">
                    <a href="index.php?c=articoli-saggi">Articoli e saggi</a>
                </li>
                <!-- voce aggiunta: ricerca per codice -->
                <li class="<?= attivo($pagina_corrente, 'ricerca-bibliografie') ?>">
                    <a href="index.php?c=ricerca-bibliografie">Ricerca per codice</a>
                </li>
            </ul>
        </li>

        <!-- BIOGRAFIE -->
        <li class="<?= attivo($pagina_corrente, 'ricerca-biografie') ?>">
            <a href="#">Biografie</a>
            <ul>
                <li class="<?= attivo($pagina_corrente, 'ricerca-biografie') ?>">
                    <a href="index.php?c=ricerca-biografie">Ricerca per codice</a>
                </li>
            </ul>
        </li>

        <!-- ACRONIMI -->
        <li class="<?= attivo($pagina_corrente, 'acronimi') ?>">
            <a href="index.php?c=acronimi">Acronimi, Abbreviazioni</a>
        </li>

        <!-- MEDIA -->
        <li class="<?= attivo($pagina_corrente, 'media') ?>">
            <a href="index.php?c=media">Media</a>
        </li>

        <!-- CONTATTI (voce semplice) -->
        <li class="<?= attivo($pagina_corrente, 'contatti') ?>">
            <a href="index.php?c=contatti">Contatti</a>
        </li>
    </ul>
</nav>
