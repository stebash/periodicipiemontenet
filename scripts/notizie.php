<?php
require_once __DIR__ . '/dbpdo.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if ($id) {
    // DETTAGLIO DI UNA SINGOLA NOTIZIA (c=notizia&id=N)
    $stmt = $pdo->prepare('
        SELECT id, titolo, testo, allegato, dagg
        FROM notizie
        WHERE id = ?
        LIMIT 1
    ');
    $stmt->execute([$id]);
    $notizia = $stmt->fetch();

    if (!$notizia) {
        echo '<h1>Notizia non trovata</h1>';
        echo '<p>La notizia richiesta non esiste.</p>';
        echo '<p><a href="index.php?c=notizie-eventi">Torna alle notizie</a></p>';
    } else { ?>

        <article>
            <h1><?= htmlspecialchars($notizia['titolo'], ENT_QUOTES, 'UTF-8') ?></h1>

            <?php if (!empty($notizia['dagg'])): ?>
                <p>
                    <small>
                        <?= htmlspecialchars(
                            date('d/m/Y', strtotime($notizia['dagg'])),
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </small>
                </p>
            <?php endif; ?>

            <div class="testo-notizia">
                <?= $notizia['testo'] ?>
            </div>

            <?php if (!empty($notizia['allegato'])): ?>
                <?php
                $nomeAllegato = basename($notizia['allegato']);
                $percorsoAllegato = dirname(__DIR__) . '/attach/' . $nomeAllegato;
                ?>
                <?php if (is_file($percorsoAllegato)): ?>
                    <p>
                        <a
                            href="attach/<?= rawurlencode($nomeAllegato) ?>"
                            download
                        >Scarica la locandina</a>
                    </p>
                <?php endif; ?>
            <?php endif; ?>
        </article>

        <p><a href="index.php?c=notizie-eventi">← Torna all'elenco delle notizie</a></p>

    <?php }
} else {
    // ELENCO DELLE NOTIZIE (c=notizie-eventi)
    $stmt = $pdo->query('
        SELECT id, titolo, dagg
        FROM notizie
        ORDER BY dagg DESC, id DESC
    ');
    $notizie = $stmt->fetchAll();
?>

    <h1>Notizie ed eventi</h1>

    <?php if (empty($notizie)): ?>

        <p>Non sono presenti notizie.</p>

    <?php else: ?>

        <ul>
            <?php foreach ($notizie as $notizia): ?>
                <li>
                    <a href="index.php?c=notizia&amp;id=<?= (int)$notizia['id'] ?>">
                        <?= htmlspecialchars($notizia['titolo'], ENT_QUOTES, 'UTF-8') ?>
                    </a>

                    <?php if (!empty($notizia['dagg'])): ?>
                        -
                        <?= htmlspecialchars(
                            date('d/m/Y', strtotime($notizia['dagg'])),
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    <?php endif; ?>
                </li>
            <?php endforeach; ?>
        </ul>

    <?php endif; ?>

<?php } ?>
