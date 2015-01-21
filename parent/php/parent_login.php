<?php
	session_start();
	include_once("db_connect.php");
	function link_db()
	{
		$link_db = null;
		try
		{
			$link_db = new PDO(host, user_name, user_pwd);
		}
		catch(PDOEcxception $e)
		{
			$link_db = null;
		}
		
		return $link_db;
	}
	
	function sha_verify($mypass,$dbpass)
	{
		$verify_hash = crypt($mypass, $dbpass);
		if($verify_hash==$dbpass)
			return true;
		else
			return false;
	}
	
	$response = array();
	$varUsername = "";
	$varPassword = "";
	if(!empty($_POST["paramUsername"]))
		$varUsername = $_POST['paramUsername'];
	if(!empty($_POST["paramPassword"]))	
		$varPassword = $_POST['paramPassword'];
	
	if($varUsername == "" || $varPassword == "")
	{
		$response["result"] = "post-error";
	}
	else
	{
		$link = link_db();
		if($link==null)
		{
			$response["result"] = "link-db-error";
		}
		else
		{
			$link -> query("SET NAMES utf8");
			$sql = "SELECT * FROM parent WHERE active = '1' AND account = :account";
			$stmt = $link -> prepare($sql);
			$stmt -> execute(array(":account"=>$varUsername));
			$count = 0;
			$res = $stmt->fetch();
			if(count($res)==0)
			{
				$response["result"] = "not-active";
			}
			else
			{
				$sql = "SELECT account,pwd FROM parent WHERE account = :account";
				$stmt = $link -> prepare($sql);
				$stmt -> execute(array(":account"=>$varUsername));
				$res = $stmt->fetch(PDO::FETCH_ASSOC);
				if(count($res)!=2)
				{
					$response["result"] = "login-fail";
				}
				else
				{
					if(sha_verify($varPassword,$res["pwd"]))
					{
						$response["result"] = "login-success";
						$_SESSION["parent_token"] = $res["account"];
					}
					else
					{
						$response["result"] = "login-fail";
					}
				}
				
			}
			$link = null;
		}
	}
	
	echo json_encode($response);
?>
