# AGENTS.md - Regole operative per il progetto

## Principi generali

- Preservare il comportamento esistente
- Preferire modifiche piccole, isolate e verificabili
- Prima di modificare un file, controllare se la stessa funzionalità esiste già altrove nel progetto o nella cronologia Git
- Prima analizzare il problema, poi identificare la causa, poi proporre la patch minima

## Sicurezza

- Usare prepared statements PDO per qualsiasi input utente nelle query SQL
- Validare i parametri GET/POST in modo coerente con il tipo atteso
- Non assumere la corrispondenza tra etichette dell'interfaccia e colonne del database: verificare sempre lo schema o il codice storico
- Non modificare lo schema del database senza autorizzazione esplicita
- Non modificare credenziali o configurazioni del database

## Sviluppo

- Non fare refactoring globale se non richiesto
- Non ripristinare automaticamente file legacy o `*-old.php`
- Verificare sempre i flussi reali prima di considerare un file attivo
- Il codice e i commenti del progetto devono rimanere in italiano
- Mantenere compatibilità con il codice legacy esistente
- Evitare modifiche non richieste a CSS, markup, database o area admin durante interventi circoscritti

## Git

- Usare Git con commit piccoli e descrittivi
- Prima del commit mostrare `git diff` e `git diff --check`
- Non usare `git add .` indiscriminatamente
- Non aggiungere al repository dump SQL, configurazioni locali o file temporanei

## File da ignorare

- `Sql1061224_1.sql`
- `opencode.json`
- `prompt.txt`
- `prompt2.md`

## Architettura del progetto

- `index.php` è il front controller pubblico
- `scripts/get_page.php` decide quale contenuto caricare in base a `?c=`
- Gli script in `scripts/` generano spesso HTML direttamente
- L'area amministrativa è in `admin/`
- Il database usa nomi di tabelle/colonne legacy con maiuscole/minuscole significative
- Il progetto non usa framework, test automatici o build system

## Note tecniche

- Se `php -l` non è disponibile o fallisce per problemi dell'ambiente locale, segnalarlo senza tentare di riparare PHP durante una modifica applicativa
- Per controlli sul dump SQL usare ricerche mirate, non leggere tutto il file
