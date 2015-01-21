<?php
	include_once("../controller/Controller.php");
	$router = array();
	if(!empty($_POST["action"]))
	{
		$router["action"] = $_POST["action"];
		$router["data"] = $_POST["data"];
		$controller = new Controller();
		echo $controller -> invoke($router);
	}
	
?>