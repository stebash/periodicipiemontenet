<?php
		$scheda=$_POST[s];
		$datav=addslashes(strip_tags ($_POST[datav]));
		$annov=addslashes(strip_tags ($_POST[annov]));
if ($_POST ['t']=='1'){
		$tit_1=addslashes(strip_tags ($_POST[tit_1]));
		$tit_2=addslashes(strip_tags ($_POST[tit_2]));
}
if ($_POST ['t']=='2'){
		$tit_1=addslashes(strip_tags ($_POST[sot_1]));
		$tit_2=addslashes(strip_tags ($_POST[sot_2]));
		$tit_3=addslashes(strip_tags ($_POST[sot_3]));
}
if ($_POST ['t']=='3'){
		$dir1=addslashes(strip_tags ($_POST['dir_1']));
		$dir2=addslashes(strip_tags ($_POST['dir_2']));
		$dir3=addslashes(strip_tags ($_POST['dir_3']));
}
if ($_POST ['t']=='4'){
		$editore=addslashes(strip_tags ($_POST[editore]));
		$luogo_ediz=addslashes(strip_tags ($_POST[luogo_ediz]));
}
if ($_POST ['t']=='5'){
		$altezza=addslashes(strip_tags ($_POST[altezza]));
		$base=addslashes(strip_tags ($_POST[base]));
}
if ($_POST ['t']=='6'){
		$pagine=addslashes(strip_tags ($_POST[pagine]));
		$pagine_a=addslashes(strip_tags ($_POST[pagine_a]));
}
if ($_POST ['t']=='7'){
		$periodic=addslashes(strip_tags ($_POST[periodic]));
}
if ($_POST ['t']=='8'){
		$prezzo=addslashes(strip_tags ($_POST[prezzo]));
		$moneta=addslashes(strip_tags ($_POST[moneta]));
}
if ($_POST ['t']=='9'){
		$prezzo=addslashes(strip_tags ($_POST[tipografo]));
		$moneta=addslashes(strip_tags ($_POST[luogo_stampa]));
}
if ($_POST ['t']=='10'){
		$prezzo=addslashes(strip_tags ($_POST[tiratura]));
}
?>