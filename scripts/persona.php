<?php
$s=$_GET['s'];

$result = $pdo->query("SELECT * FROM PERSONA WHERE PID = '$s'"); //cerco le informazioni legate alla persona
$result3 = $pdo->query("SELECT * FROM IMMAGINI WHERE SCHEDA = '$s'"); //cerco le immagini associate alla scheda


while($row = $result3->fetch()) { ?>


<img src="img_bio/img<?php echo $row['ID']; ?>.jpg" height="200" align="right">
<?php } 

while($row = $result->fetch()) {

$sc=$row['codice'];
	// Print out the contents of each row into a table
	echo '<p><strong>Scheda: </strong>'.$row['ID'].'<br />';
	echo '<strong>Nome:</strong> '.$row['NOME'].'<br /><br />';
	echo '<strong>Dati biografici</strong><br />'.$row['BIO'].'</p>';
}?>

