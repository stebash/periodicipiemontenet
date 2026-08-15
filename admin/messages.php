<?php
session_start(); 
if($_SESSION['username']=='admin') 

{ ?>
<html>
<head>
	<title>Periodici Piemonte</title>
	<meta http-equiv="Content-Type" content="text/html" charset="UTF-8"> 
	<link href="../css/style.css" rel="stylesheet" type="text/css">
</head>
<body>
<?php
require_once ('header.php');
$id=$_GET['id']; ?>
<div>


<?php 
	if ($id=='1') { echo "Nome utente o password errati"; } //pagina di login
	else if ($id=='2') {echo "Non ha i permessi per accedere a questa pagina";} //area riservata
	else if ($id=='3') {echo "La scheda è stata eliminata correttamente";} //eliminazione scheda periodico
	else if ($id=='4') {echo 'Non ci sono schede con questo numero';}
	else if ($id=='5') {echo "Si è verificato un errore durante l'inserimento";}
	else if ($id=='6') {echo 'Variazione inserita correttamente';}
	else if ($id=='7') {echo 'Scheda bibliografica inserita correttamente';}
	else if ($id=='8') {echo 'Scheda periodico inserita correttamente';}
	else if ($id=='9') {echo 'Persona inserita correttamente';}
	else if ($id=='10') {echo 'Upload non eseguito, file esistente.';} //caricamento immagine non riuscito
	else if ($id=='11') {echo 'Immagine inserita correttamente';} //caricamento immagine andato a buon fine
	else if ($id=='12') {echo 'Immagine non valida';} //caricamento immagine non valida
	else if ($id=='13') {echo 'Scheda biografica inserita correttamente';}
	else if ($id=='14') {echo 'Attenzione non ci sono schede che corrispondono al numero inserito';} 

	
?>
<p><a href="index.php">Torna al menù di amministrazione</a></p>
</div>
<?php require_once('../html/footer.html');?>
</body>
<?php }else{ 
header("Location:messages.php?id=2"); 
} 