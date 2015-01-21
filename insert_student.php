<?php
	define("host", "mysql:host=mysql.lionfree.net;dbname=u597449507_psy");
	define("user_name", "u597449507_psy");
	define("user_pwd", "psycsie");
	
	function pwd_encrypt($pass)
	{
		$pwd = $pass;
		$salt = strtr(base64_encode(mcrypt_create_iv(16, MCRYPT_RAND)), '+', '.');
		$salt_sha512 = '$6$rounds=5000$'.$salt.'$';
		$sha512md = crypt($pwd, $salt_sha512);
		return $sha512md;
	}
	
	$link = null;
	$link = new PDO(host,user_name,user_pwd);
	if($link==null)
	{
		echo "cannot link db.";
	}
	else
	{
		$file_open = fopen("data_2013_1122.csv", "r");
		$account = "";
		$password = "";
		
		$link -> query("SET NAMES utf8");
		$len = 0;
		while(!feof($file_open))
		{
			$str = explode(",", trim(fgets($file_open)));
			//密碼預設
			
			$db_pass = pwd_encrypt($str[1]);
			$acc = "u".$str[1]."@ms".substr($str[1],0,3).".nttu.edu.tw";
			
			/*if($acc=="NULL")
				continue;*/
			$name = $str[2];
			$account .= $acc."\r\n";
			$password .= $str[1]."\r\n";
			
			$sql = "INSERT INTO student(name,account,pwd,verify,active,lie_times) VALUES('$name','$acc','$db_pass','0','1','0')";
			//$link -> query($sql);
			$len++;
		}
		
		fclose($file_open);
		$link = null;
		
		echo $len;
		file_put_contents("student_account.txt",$account);
		file_put_contents("student_password.txt",$password);
	}
?>