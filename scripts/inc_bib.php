<?php
if (isset($_GET['s'])){@$s= $_GET['s'];}
else if (isset($_POST['s'])){@$s= $_POST['s'];}
$result = $pdo->query("SELECT * FROM BIBLIO_EST LEFT JOIN TIPOLOGIA ON BIBLIO_EST.codTipologia=TIPOLOGIA.CODICE WHERE BIBLIO_EST.codice = $s"); 
$cerca_per = $pdo->query("SELECT * FROM PERSONE AS p JOIN BIBLIO_EST AS b ON p.codice=b.codice WHERE b.codice='$s'"); //cerca i redattori

if ($result->rowCount() <=0) { 
echo 'Non ci sono schede con questo numero';
} else {
echo "<h1>Scheda bibliografica</h1>";
echo "<ul>";

// keeps getting the next row until there are no more to get
while($row = $result->fetch()) {
	// Print out the contents of each row into a table 
	?>
	<li><strong>Scheda: <?php echo $s;?></strong></li>
	<?php if ($cerca_per->rowCount >= 0) { ?>
	<li><strong>Autore:</strong> 
				<?php while ($redattore= $cerca_per->fetch()) {
							$ultimo = end($redattore);
							if(($redattore[count($redattore)-2]) == $ultimo) {echo ' '.$redattore['PERS']; }
							else {echo ' '.$redattore['PERS'] .',';}
				}} ?></li>
	<li><strong>Titolo:</strong> <?php echo $row['TITOLO'];?></li>
	<li><strong>Editore:</strong> <?php echo $row['editore'];?></li>
	<li><strong>Luogo:</strong> <?php echo $row['luogoEdiz'];?></li>
	<li><strong>Tipologia:</strong> <?php echo $row['DESCRIZION'];?></li>
	<li><strong>Anno:</strong> <?php echo $row['annoEdiz'];?></li>
	<li><strong>Note:</strong> <?php echo $row['NOTE'];?></li>

						 <?php } 

 echo "</ul>";
 }
 ?>
