<?php
// Recupero sicuro del parametro 'c'
$pagina = isset($_GET['c']) ? $_GET['c'] : '';

// Piccola funzione di output sicuro
function e(string $val): string {
    return htmlspecialchars($val, ENT_QUOTES, 'UTF-8');
}

switch ($pagina) {

    case 'ricerca-periodico':
        $n      = 'del periodico';
        $cerca  = 'periodici';
        $action = 'index.php?c=scheda';
        ?>
        <div class="form-ricerca">
            <h1 class="titolo-sezione">Cerca <?= e($cerca) ?></h1>
            <p class="testo-intro">
                Inserisci il numero della scheda <?= e($n) ?> di tuo interesse.
            </p>

            <form method="POST" action="<?= e($action) ?>" class="form-inline-blocco">
                <div class="form-group">
                    <label for="scheda-periodico" class="sr-only">Numero scheda</label>
                    <input
                        type="text"
                        id="scheda-periodico"
                        name="s"
                        class="form-control form-control-sm input-scheda"
                        placeholder="N. scheda"
                        required
                    >
                </div>

                <button type="submit" name="submit" class="btn btn-primary btn-sm">
                    Cerca
                </button>
            </form>
        </div>
        <?php
        break;

    case 'ricerca-bibliografie':
        $n      = 'della bibliografia';
        $cerca  = 'bibliografie';
        $action = 'index.php?c=bibliografia';
        ?>
        <div class="form-ricerca">
            <h1 class="titolo-sezione">Cerca <?= e($cerca) ?></h1>
            <p class="testo-intro">
                Inserisci il numero della scheda <?= e($n) ?> di tuo interesse.
            </p>

            <form method="POST" action="<?= e($action) ?>" class="form-inline-blocco">
                <div class="form-group">
                    <label for="scheda-bibliografia" class="sr-only">Numero scheda</label>
                    <input
                        type="text"
                        id="scheda-bibliografia"
                        name="s"
                        class="form-control form-control-sm input-scheda"
                        placeholder="N. scheda"
                        required
                    >
                </div>

                <button type="submit" name="submit" class="btn btn-primary btn-sm">
                    Cerca
                </button>
            </form>
        </div>
        <?php
        break;

    case 'ricerca-biografie':
        $n      = 'della biografia';
        $cerca  = 'biografie';
        $action = 'index.php?c=biografia';
        ?>
        <div class="form-ricerca">
            <h1 class="titolo-sezione">Cerca <?= e($cerca) ?></h1>
            <p class="testo-intro">
                Inserisci il numero della scheda <?= e($n) ?> di tuo interesse.
            </p>

            <form method="POST" action="<?= e($action) ?>" class="form-inline-blocco">
                <div class="form-group">
                    <label for="scheda-biografia" class="sr-only">Numero scheda</label>
                    <input
                        type="text"
                        id="scheda-biografia"
                        name="s"
                        class="form-control form-control-sm input-scheda"
                        placeholder="N. scheda"
                        required
                    >
                </div>

                <button type="submit" name="submit" class="btn btn-primary btn-sm">
                    Cerca
                </button>
            </form>
        </div>
        <?php
        break;

    case 'ricerca-anno-fondazione':
        ?>
        <div class="form-ricerca">
            <h1 class="titolo-sezione">Cerca per anno di fondazione</h1>
            <p class="testo-intro">
                Inserisci l'anno di fondazione del periodico.
            </p>

            <form method="POST" action="index.php?c=anno-fondazione" class="form-inline-blocco">
                <div class="form-group">
                    <label for="anno-fondazione" class="sr-only">Anno di fondazione</label>
                    <input
                        type="text"
                        id="anno-fondazione"
                        name="af"
                        class="form-control form-control-sm input-scheda"
                        placeholder="Es. 1923"
                        required
                    >
                </div>

                <button type="submit" name="submit" class="btn btn-primary btn-sm">
                    Cerca
                </button>
            </form>
        </div>
        <?php
        break;

    default:
        // Nessun form da mostrare
        break;
}
?>
