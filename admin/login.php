<?php
session_start();
if (isset($_POST['username']) && isset($_POST['password'])){
	if ($_POST['username']=='admin' && ($_POST['password']=='allio')){
	
		$_SESSION['username']=$_POST['username']; 
		$_SESSION['password']=$_POST['password'];
		header("Location:index.php");
																	}
	else {header("Location:messages.php?id=1");}
} 

require_once ('header.php');
?>	

<h1>Accedi all'area riservata</h1>
<form method ="post" action="<?php $_SERVER['PHP_SELF']; ?>"/>
	<table>
			<tr><td>Nome utente:</td><td><input type="text" name="username"/></td></tr>
			<tr><td>Password:</td><td><input type="password" name="password"/></td></tr>
			<tr><td><input type="submit" value="Accedi"/></td></tr>
	</table>
</form>
<?php require_once('../html/footer.html');?>
</div>



