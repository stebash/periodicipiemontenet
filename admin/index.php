<?php
session_start(); 
if($_SESSION['username']=='admin') 

{ 
require_once ('header.php');
?>

<h1>Amministrazione</h1>

<div>
	<h2>Periodici</h2>
			<ul>
				<li><a href="inserisci_scheda.php">Inserisci un nuovo periodico</a></li>
				<li><a href="inserisci_pers.php">Inserisci una persona (es Direttore responsabile)</a></li>
				<li><form method="POST" action="modifica_scheda.php">
						Digita il numero del periodico da modificare
							<input type="text" size="6" name="s">
							<input type="submit" value="Modifica"></li>
					</form>
				<li><form method="POST" action="cancella.php">
						Digita il numero del periodico da eliminare
							<input type="text" size="6" name="s">
							<input type="hidden" name="t" value="per" />
							<input type="submit" value="Cancella">
					</form>
				</li>
			</ul>
		
			<h3>Variazioni</h3>
		    <?php require_once('variazioni.php'); ?>
    


				
				
</div>

<div><h2>Biografie</h2>
	<ul>
		<li><a href="inserisci_bio.php">Inserisci una nuova biografia</a></li>
		<li><form method="POST" action="modifica_bio.php">
				Digita il numero della biografia da modificare
					<input type="text" size="6" name="s">
					<input type="submit" value="Modifica">
			</form>
		</li>
		<li>
		<form method="POST" action="cancella.php">
				Digita il numero della biografia da eliminare
					<input type="text" size="6" name="s">
					<input type="hidden" name="t" value="bio" />
					<input type="submit" value="Cancella">
			</form>
		</li>
	</ul>
</div>

<div><h2>Bibliografie</h2>
	<ul>
		<li><a href="inserisci_biblio.php">Inserisci una nuova bibliografia</a></li>
		<li><form method="POST" action="modifica_biblio.php">
				Digita il numero della bibliografia da modificare
					<input type="text" size="6" name="s">
					<input type="submit" value="Modifica">
			</form>
		</li>
		<li><form method="POST" action="cancella.php">
				Digita il numero della bibliografia da eliminare
					<input type="text" size="6" name="s">
					<input type="hidden" name="t" value="bib" />
					<input type="submit" value="Cancella">
			</form></li>
	</ul>
</div>
<div><h2>Immagini</h2>
	<ul>
		<li>
<form method="POST" action="up.php" enctype="multipart/form-data">
  <table>
    <tr>
      <td>Digita il numero della scheda a cui l'immagine è riferita</td>
      <td><input type="text" name="s" size="6" maxlength="8" /></td>
    </tr>
    <tr>
      <td>Seleziona la tipologia di immagine</td>
      <td>
        <select name="tipo" required>
          <option value="S">Scheda periodico</option>
          <option value="B">Biografia</option>
        </select>
      </td>
    </tr>
  </table>

  <input type="file" name="file" id="file" accept="image/jpeg,image/png,image/gif" required><br>

  <input type="hidden" name="t" value="img" />
  <input type="submit" value="Inserisci immagine" name="upload" id="upload" />
</form>
	
		</li>
		<!--<li><form method="POST" action="show-img.php">
				Digita il numero della scheda che contiene l'immagine da eliminare
					<input type="text" name="s" size ="4" maxsize ="4">
					<input type="submit" value="Vai alla scheda">
			</form></li>
	</ul>
</div>-->

<p><a href="logout.php">Esci</a>

<?php }else{ 
header("Location:messages.php?id=2"); 
} 

require_once('../html/footer.html');?>

		</div>
</body>
</html>

