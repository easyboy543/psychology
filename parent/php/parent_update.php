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
	
	function sha_encrypt($mypass)
	{
		$salt = strtr(base64_encode(mcrypt_create_iv(16, MCRYPT_RAND)), '+', '.');
		$salt_sha512 = '$6$rounds=5000$'.$salt.'$';
		$sha512md = crypt($mypass, $salt_sha512);
		return $sha512md;
	}
	
	function sha_verify($mypass,$dbpass)
	{
		$verify_hash = crypt($mypass, $dbpass);
		if($verify_hash == $dbpass)
			return true;
		else	
			return false;
	}
	
	$response = array();
	$varUsername = "";
	$varPassword = "";
	$varchangePassword = "";
	if(!empty($_POST["paramUsername"]))
		$varUsername = $_POST['paramUsername'];
	if(!empty($_POST["paramPassword"]))	
		$varPassword = $_POST['paramPassword'];
	if(!empty($_POST["paramchangePassword"]))
		$varchangePassword = $_POST['paramchangePassword'];
		
	if($varUsername == "" || $varPassword == "" || $varchangePassword=="")
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
			$sql = "SELECT account,pwd,id FROM parent WHERE account =:account AND active = '1'";
			$stmt = $link -> prepare($sql);
			$stmt -> execute(array(":account"=>$varUsername));
			$count = 0;
			$res = $stmt->fetch(PDO::FETCH_ASSOC);
			if(count($res)!=3)
			{
				$response["result"] = "not-active";
			}
			else
			{
				if($varPassword==$varchangePassword)
				{
					$response["result"] = "pass-not-change";
				}
				else if(!sha_verify($varPassword,$res["pwd"]))
				{
					$response["result"] = "update-fail";
				}
				else
				{
					$sql = "UPDATE parent SET pwd = :pwd WHERE id = :id";
					$stmt = $link -> prepare($sql);
					$result = $stmt -> execute(array(":pwd"=>sha_encrypt($varchangePassword),":id"=>$res["id"]));
					if(!$result)
					{
						$response["result"] = "update-fail";
					}
					else
					{
						$response["result"] = "update-success";
					}
				
				}
			}
			$link = null;
		}
	}
	
	echo json_encode($response);
?>

