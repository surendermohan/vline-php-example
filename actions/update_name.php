<?php
session_start();
include("../classes/DbHandler.php");
$dbh = new DbHandler();
$mysqli = $dbh->connect('../');
if(!$mysqli){
	header("Location: ../install/index.php");
}
else{
	$data = $_SESSION['user'];
	$fullname = $_POST['fullname'];
	//$auth = $dbh->authUser($_POST);
	if(strlen($fullname) > 0){
		$data['name'] = $fullname;
		$dbh->saveUser($data);
		$_SESSION['plainuserauth'] = 1;
		$_SESSION['user']['name'] = $fullname;
		header("Location: ../main.php");
	} else{
		header("Location: ../index.php?failed=1");
	}
}
?>