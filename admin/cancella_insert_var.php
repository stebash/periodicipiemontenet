<?php
session_start(); 
if($_SESSION['username']=='admin') {
require_once ('header.php');
include ("../scripts/dbpdo.php"); //mi collego al db

	if ($_POST ['t']=='1'){
							$result = $pdo->prepare("INSERT INTO VARIA_TI (SCHEDA, DATA_V, ANNO_V, TIT_1, TIT_2) VALUES (:scheda, :datav, :annov, :tit1, :tit2)");
							$result->BindParam(':tit1', $_POST[tit_1],PDO::PARAM_STR);
							$result->BindParam(':tit2', $_POST[tit_2],PDO::PARAM_STR);
						}
	if ($_POST ['t']=='2'){
							$result = $pdo->prepare("INSERT INTO VARIA_SO (SCHEDA, DATA_V, ANNO_V, SOT_1, SOT_2, SOT_3) VALUES (:scheda, :datav, :annov, :sot1, :sot2, :sot3)");
							$result->BindParam(':sot1', $_POST[sot_1],PDO::PARAM_STR);
							$result->BindParam(':sot2', $_POST[sot_2],PDO::PARAM_STR);
							$result->BindParam(':sot3', $_POST[sot_3],PDO::PARAM_STR);
						}
	if ($_POST ['t']=='3'){
							$result = $pdo->prepare("INSERT INTO VARIA_DI (SCHEDA, DATA_V, ANNO_V, DIR_1, DIR_2, DIR_3) VALUES (:scheda, :datav, :annov, :dir1, :dir2, :dir3)");
							$result->BindParam(':dir1', $_POST[dir_1],PDO::PARAM_STR);
							$result->BindParam(':dir2', $_POST[dir_2],PDO::PARAM_STR);
							$result->BindParam(':dir3', $_POST[dir_3],PDO::PARAM_STR);
						 } 
	if ($_POST ['t']=='4'){
							$result = $pdo->prepare("INSERT INTO VARIA_ED (SCHEDA, DATA_V, ANNO_V, EDITORE, LUOGEDIZ) VALUES (:scheda, :datav, :annov, :editore, :luogo_ediz)");
							$result->BindParam(':editore', $_POST[editore],PDO::PARAM_STR);
							$result->BindParam(':luogo_ediz', $_POST[luogo_ediz],PDO::PARAM_STR);
						  }
	if ($_POST ['t']=='5'){
							$result = $pdo->prepare("INSERT INTO VARIA_FO (SCHEDA, DATA_V, ANNO_V, ALTEZZA, BASE) VALUES (:scheda, :datav, :annov, :altezza, :base)");
							$result->BindParam(':altezza', $_POST[altezza],PDO::PARAM_STR);
							$result->BindParam(':base', $_POST[base],PDO::PARAM_STR);
						  }
	if ($_POST ['t']=='6'){
							$result = $pdo->prepare("INSERT INTO VARIA_PA (SCHEDA, DATA_V, ANNO_V, PAGINE, PAGINE_A) VALUES (:scheda, :datav, :annov, :pagine, :pagine_a)");
							$result->BindParam(':pagine', $_POST[pagine],PDO::PARAM_STR);
							$result->BindParam(':pagine_a', $_POST[pagine_a],PDO::PARAM_STR);
						  }
	if ($_POST ['t']=='7'){
							$result = $pdo->prepare("INSERT INTO VARIA_PE (SCHEDA, DATA_V, ANNO_V, PERIODIC) VALUES (:scheda, :datav, :annov, :periodic)");
							$result->BindParam(':periodic', $_POST[periodic],PDO::PARAM_STR);
						  }
	if ($_POST ['t']=='8'){
							$result = $pdo->prepare("INSERT INTO VARIA_PR (SCHEDA, DATA_V, ANNO_V, PREZZO, MONETA) VALUES (:scheda, :datav, :annov, :prezzo, :moneta)");
							$result->BindParam(':prezzo', $_POST[prezzo],PDO::PARAM_STR);
							$result->BindParam(':moneta', $_POST[moneta],PDO::PARAM_STR);
						  }
	if ($_POST ['t']=='9'){
							$result = $pdo->prepare("INSERT INTO VARIA_TP (SCHEDA, DATA_V, ANNO_V, TIPOGRAFO, LUOGSTAMPA) VALUES (:scheda, :datav, :annov, :tipografo, :luogo_stampa)");
							$result->BindParam(':tipografo', $_POST[tipografo],PDO::PARAM_STR);
							$result->BindParam(':luogo_stampa', $_POST[luogo_stampa],PDO::PARAM_STR);
						  }
	if ($_POST ['t']=='10'){
							$result = $pdo->prepare("INSERT INTO VARIA_TR (SCHEDA, DATA_V, ANNO_V, TIRATURA) VALUES (:scheda, :datav, :annov, :tiratura)");
							$result->BindParam(':tiratura', $_POST[tiratura],PDO::PARAM_STR);
						  }

//campi comuni a tutte le variazioni	
	$result->BindParam(':scheda', $_POST[s],PDO::PARAM_STR);
	$result->BindParam(':datav', $_POST[datav],PDO::PARAM_STR);
	$result->BindParam(':annov', $_POST[annov],PDO::PARAM_STR);

$result->execute();
		if ($result->rowCount() =='1') {header("Location:messages.php?id=6");}
		else {header("Location:messages.php?id=5");} 

}else{
	
header("Location:messages.php?id=2"); 
} 

 ?>
